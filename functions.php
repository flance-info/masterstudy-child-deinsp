<?php

ini_set( 'display_errors', 1 );
ini_set( 'display_startup_errors', 1 );
error_reporting( E_ALL );

use MasterStudy\Lms\Repositories\CurriculumRepository;
function enqueue_voomly_embed_script() {
	wp_enqueue_script(
		'reset-progress',
		get_stylesheet_directory_uri() . '/assets/js/reset-progress.js', // Update the path as necessary
		array( 'jquery' ),
		time(),
		true
	);
	wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array( 'parent-style' ), time() );
}

add_action( 'wp_enqueue_scripts', 'enqueue_voomly_embed_script' );

add_action('init', 'setup_reset_student_progress_action');

function setup_reset_student_progress_action() {
    add_action('wp_ajax_stm_lms_dashboard_reset_student_progress_child', 'reset_student_progress_child');
  
}


function reset_student_progress_child() {
	try {
		// Verify the nonce for security
		check_ajax_referer( 'stm_lms_dashboard_reset_student_progress', 'nonce' );
		// Check if the current user is an instructor
		if ( ! STM_LMS_User_Manager_Interface::isInstructor() ) {
			throw new Exception( 'Unauthorized access' );
		}
	
		$data         = $_POST;
		// Validate the required data
		if ( empty( $data['user_id'] ) || empty( $data['course_id'] ) ) {
			throw new Exception( 'Invalid data' );
		}
		$course_id  = intval( $data['course_id'] );
		$student_id = intval( $data['user_id'] );
		// Retrieve the course curriculum
		$curriculum = ( new CurriculumRepository() )->get_curriculum( $course_id );
		if ( empty( $curriculum['materials'] ) ) {
			throw new Exception( 'No curriculum found' );
		}
		// Reset progress for each material type
		foreach ( $curriculum['materials'] as $material ) {
			switch ( $material['post_type'] ) {
				case 'stm-lessons':
					STM_LMS_User_Manager_Course_User::reset_lesson( $student_id, $course_id, $material['post_id'] );
					break;
				case 'stm-assignments':
					STM_LMS_User_Manager_Course_User::reset_assignment( $student_id, $course_id, $material['post_id'] );
					break;
				case 'stm-quizzes':
					STM_LMS_User_Manager_Course_User::reset_quiz( $student_id, $course_id, $material['post_id'] );
					break;
			}
		}
		// Reset user answers and update course progress
		stm_lms_reset_user_answers( $course_id, $student_id );
		STM_LMS_Course::update_course_progress( $student_id, $course_id, true );
		// Send a success response with the updated progress
		wp_send_json_success( STM_LMS_User_Manager_Course_User::_student_progress( $course_id, $student_id ) );
	} catch (Exception $e) {
           wp_send_json_error( $e->getMessage() );
       }
   }