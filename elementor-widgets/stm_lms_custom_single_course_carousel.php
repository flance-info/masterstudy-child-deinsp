<?php

namespace ChildThemeElementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class StmLmsCustomSingleCourseCarousel extends Widget_Base {

    public function __construct( $data = array(), $args = null ) {
        parent::__construct( $data, $args );
        wp_register_style( 'custom_single_course_carousel', get_stylesheet_directory_uri() . '/assets/css/custom_single_course_carousel.css', array(), '1.0.0', false );
    }

    public function get_name() {
        return 'stm_lms_custom_single_course_carousel';
    }

    public function get_title() {
        return __( 'Custom Single Course Carousel', 'masterstudy-child' );
    }

    public function get_icon() {
        return 'eicon-post-slider lms-icon';
    }

    public function get_style_depends() {
        return array(
            'custom_single_course_carousel',
            'masterstudy-buy-button',
            'masterstudy-buy-button-points',
            'masterstudy-buy-button-group-courses',
            'masterstudy-buy-button-prerequisites',
            'masterstudy-group-course',
            'masterstudy-buy-button-affiliate',
        );
    }

    public function get_categories() {
        return array( 'masterstudy-child' );
    }

    public static function show_reviews() {
        return \STM_LMS_Options::get_option( 'course_tab_reviews', true );
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_content',
            array(
                'label' => __( 'Content', 'masterstudy-child' ),
            )
        );

        $sort_options = array(
            'none'    => __( 'None', 'masterstudy-child' ),
            'popular' => __( 'Popular', 'masterstudy-child' ),
            'free'    => __( 'Free', 'masterstudy-child' ),
        );

        if ( self::show_reviews() ) {
            $sort_options['rating'] = __( 'Rating', 'masterstudy-child' );
        }

        $this->add_control(
            'query',
            array(
                'name'        => 'query',
                'label'       => __( 'Sort', 'masterstudy-child' ),
                'type'        => \Elementor\Controls_Manager::SELECT,
                'label_block' => true,
                'options'     => $sort_options,
                'default'     => 'none',
            )
        );

        $this->add_control(
            'prev_next',
            array(
                'name'        => 'prev_next',
                'label'       => __( 'Prev/Next Buttons', 'masterstudy-child' ),
                'type'        => \Elementor\Controls_Manager::SELECT,
                'label_block' => true,
                'options'     => array(
                    'enable'  => __( 'Enable', 'masterstudy-child' ),
                    'disable' => __( 'Disable', 'masterstudy-child' ),
                ),
                'default'     => 'enable',
            )
        );

        $this->add_control(
            'pagination',
            array(
                'name'        => 'pagination',
                'label'       => __( 'Pagination', 'masterstudy-child' ),
                'type'        => \Elementor\Controls_Manager::SELECT,
                'label_block' => true,
                'options'     => array(
                    'enable'  => __( 'Enable', 'masterstudy-child' ),
                    'disable' => __( 'Disable', 'masterstudy-child' ),
                ),
                'default'     => 'disable',
            )
        );

        $this->add_control(
            'courses',
            array(
                'name'        => 'courses',
                'label'       => __( 'Select Courses', 'masterstudy-child' ),
                'type'        => \Elementor\Controls_Manager::SELECT2,
                'label_block' => true,
                'multiple'    => true,
                'options'     => stm_lms_elementor_autocomplete_courses(),
            )
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $atts = array(
            'css'        => '',
            'query'      => ! empty( $settings['query'] ) ? $settings['query'] : 'none',
            'prev_next'  => ! empty( $settings['prev_next'] ) ? $settings['prev_next'] : 'enable',
            'pagination' => ! empty( $settings['pagination'] ) ? $settings['pagination'] : 'disable',
            'courses'    => ! empty( $settings['courses'] ) ? $settings['courses'] : array(),
        );
        $uniq = stm_lms_create_unique_id( $atts );
        $atts['uniq'] = $uniq;
        
        // Use custom template from child theme
        $template_path = get_stylesheet_directory() . '/stm-lms-templates/widget/stm_lms_single_course_carousel.php';
        
        if (file_exists($template_path)) {
            include $template_path;
        } else {
            // Fallback to default template if custom one doesn't exist
            \STM_LMS_Templates::show_lms_template( 'shortcodes/stm_lms_single_course_carousel', $atts );
        }
    }

    protected function content_template() {
    }
}

function stm_lms_elementor_autocomplete_courses() {
    $courses = get_posts(array(
        'post_type'   => 'stm-course-bundles', // Assuming 'stm-courses' is the post type for courses
        'numberposts' => -1,            // Retrieve all courses
        'post_status' => 'publish',     // Only published courses
    ));

    $options = array();

    if (!empty($courses)) {
        foreach ($courses as $course) {
            $options[$course->ID] = $course->post_title;
        }
    }

    return $options;
} 