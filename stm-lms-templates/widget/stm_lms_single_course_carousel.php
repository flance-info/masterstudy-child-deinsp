<?php

$args = array(
		'post_type'      => 'stm-course-bundles',
		'posts_per_page' => - 1,
		'post__in'       => ! empty( $atts['courses'] ) ? $atts['courses'] : array(),
		'orderby'        => 'post__in',
);
if ( ! empty( $query ) ) {
	$args = array_merge( $args, STM_LMS_Helpers::sort_query( esc_attr( $query ) ) );
}
$q = new WP_Query( $args );
stm_lms_register_style( 'course' );
wp_enqueue_script( 'imagesloaded' );
wp_enqueue_script( 'owl.carousel' );
wp_enqueue_style( 'owl.carousel' );
stm_lms_module_styles( 'single_course_carousel' );
stm_lms_module_scripts( 'single_course_carousel', 'style_1' );

use MasterStudy\Lms\Pro\addons\CourseBundle\Repository\CourseBundleRepository;

stm_lms_register_script( 'bundles/card' );
$public = true;
$columns = ( ! empty( $columns ) ) ? $columns : '6';
$args    = ( ! empty( $args ) ) ? $args : array();
$argsd   = array(
		'posts_per_page' => 12,
		'post_status'    => 'publish',
		'stm_lms_page'   => 0,
		'author'         => ''
);
$bundles = ( new CourseBundleRepository() )->get_bundles(
		wp_parse_args(
				$argsd,
				array(
						'post__in'       => ! empty( $atts['courses'] ) ? $atts['courses'] : array(),
						'posts_per_page' => - 1,
				)
		),
		$public
);
function get_bundle_by_id( $bundles_list, $bundle_id ) {
	foreach ( $bundles_list as $bundle ) {
		if ( $bundle['id'] == $bundle_id ) {
			return $bundle;
		}
	}

	return null; // Return null if no bundle is found with the given ID
}

$bundles_list = ( ! empty( $bundles['posts'] ) ) ? $bundles['posts'] : array();
$courses_data = ( ! empty( $bundles['courses'] ) ) ? $bundles['courses'] : array();
$pages        = ( ! empty( $bundles['pages'] ) ) ? $bundles['pages'] : 1;
stm_lms_register_style( 'expiration/main' );

if ( $q->have_posts() ) :
	?>
	<div class="stm_lms_single_course_carousel_wrapper <?php // phpcs:ignore Squiz.PHP.EmbeddedPhp
	echo esc_attr( $atts['uniq'] ?? '' );
	if ( isset( $atts['prev_next'] ) && 'disable' === $atts['prev_next'] ) {
		echo esc_attr( 'no-nav' );
	}
	// phpcs:ignore Squiz.PHP.EmbeddedPhp
	?>"
		 data-items="1"
		 data-pagination="<?php echo esc_attr( $atts['pagination'] ); ?>">
		<div class="stm_lms_single_course_carousel">
			<?php
			while ( $q->have_posts() ) :
				$q->the_post();
				$post_id  = get_the_ID();

				$level    = get_post_meta( $post_id, 'level', true );
				$duration = get_post_meta( $post_id, 'duration_info', true );
				$lectures = STM_LMS_Course::curriculum_info( $post_id );
				?>

				<div class="stm_lms_single_course_carousel_item stm_carousel_glitch">

					<a href="<?php the_permalink(); ?>" class="stm_lms_single_course_carousel_item__image">
						<?php
						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						echo stm_lms_lazyload_image( stm_lms_get_VC_attachment_img_safe( get_post_thumbnail_id(), '504x335' ) );
						?>
					</a>

					<div class="stm_lms_single_course_carousel_item__content">


						<?php
						$courses = CourseBundleRepository::get_bundle_courses( $post_id );
						$price   = CourseBundleRepository::get_bundle_price( $post_id );
						if ( ! empty( $courses ) ) : ?>

							<?php
							$bundle = get_bundle_by_id( $bundles_list, $post_id );
							$courses = $courses_data;
							STM_LMS_Templates::show_lms_template( 'bundles/card/php/main', compact( 'bundle', 'courses' ) ); ?>


						<?php
						endif;
						?>

					</div>

				</div>

			<?php endwhile; ?>
		</div>

		<?php if ( 'disable' !== $atts['prev_next'] ) : ?>
			<div class="stm_lms_courses_carousel__buttons">
				<div class="stm_lms_courses_carousel__button stm_lms_courses_carousel__button_prev sbc_h sbrc_h">
					<i class="fa fa-chevron-left"></i>
				</div>
				<div class="stm_lms_courses_carousel__button stm_lms_courses_carousel__button_next sbc_h sbrc_h">
					<i class="fa fa-chevron-right"></i>
				</div>
			</div>
		<?php endif; ?>

	</div>
<?php
endif;
wp_reset_postdata();


