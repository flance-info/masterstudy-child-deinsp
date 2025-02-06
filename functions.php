<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


function enqueue_voomly_embed_script() {
	wp_register_script('masterstudy-buy-button-child', get_stylesheet_directory_uri() . '/js/buy-button.js', array(), '3.4.6', true);

	wp_enqueue_style('child-style', get_stylesheet_directory_uri() . '/style.css', array('parent-style'), '3.3.3');
}
add_action('wp_enqueue_scripts', 'enqueue_voomly_embed_script');

function register_custom_elementor_widgets() {
    // Include widget file with correct path
    require_once get_stylesheet_directory() . '/elementor-widgets/stm_lms_custom_single_course_carousel.php';
    
    // Register widget
    \Elementor\Plugin::instance()->widgets_manager->register( new \ChildThemeElementor\Widgets\StmLmsCustomSingleCourseCarousel() );
}
add_action( 'elementor/widgets/register', 'register_custom_elementor_widgets' );

// Create a new Elementor category for child theme widgets
function add_elementor_widget_categories( $elements_manager ) {
    $elements_manager->add_category(
        'masterstudy-child',
        [
            'title' => __( 'MasterStudy Child Theme', 'masterstudy-child' ),
            'icon' => 'fa fa-plug',
        ]
    );
}
add_action( 'elementor/elements/categories_registered', 'add_elementor_widget_categories' );