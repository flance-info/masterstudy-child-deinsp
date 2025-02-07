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
						<?php STM_LMS_Templates::show_lms_template( 'course/parts/panel_info', array( 'number' => 1 ) ); ?>


						<div class="stm_lms_courses__single--info_meta">
							<?php STM_LMS_Templates::show_lms_template( 'courses/parts/meta', compact( 'level', 'duration', 'lectures' ) ); ?>
						</div>


						<?php
						$courses = CourseBundleRepository::get_bundle_courses( $post_id );
						$price   = CourseBundleRepository::get_bundle_price( $post_id );
						if ( ! empty( $courses ) ) : ?>
						
						<div class="stm_lms_single_bundle__courses_wrapper">

							<h2><a class="h2" href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

							<div class="stm_lms_single_bundle__courses">

								<?php foreach ( $courses as $course_id ) : ?>

									<a href="<?php echo esc_url( get_the_permalink( $course_id ) ); ?>" class="stm_lms_single_bundle__courses_course">

										<div class="stm_lms_single_bundle__courses_course__inner">

											<div class="stm_lms_single_bundle__courses_course__image">
												<?php
												$img_size = '85x50';
												if ( function_exists( 'stm_get_VC_img' ) ) {
													// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
													echo stm_lms_lazyload_image( stm_get_VC_img( get_post_thumbnail_id( $course_id ), $img_size ) );
												} else {
													echo get_the_post_thumbnail( $course_id, $img_size );
												}
												?>
											</div>

											<?php $course_expiration_days = STM_LMS_Course::get_course_expiration_days( $course_id ); ?>

											<div class="stm_lms_single_bundle__courses_course__data heading_font">

												<?php
												if ( $course_expiration_days ) {
													STM_LMS_Templates::show_lms_template( 'expiration/info', compact( 'course_expiration_days' ) );
												}
												?>

												<div class="stm_lms_single_bundle__courses_course__title">
													<?php echo esc_html( get_the_title( $course_id ) ); ?>
												</div>

												<?php if ( ! empty( $price ) ) : ?>
													<div class="stm_lms_single_bundle__courses_course__price">
														<?php
														// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
														echo STM_LMS_Helpers::display_price( STM_LMS_Course::get_course_price( $course_id ) );
														?>
													</div>
												<?php endif; ?>

											</div>

										</div>

									</a>

								<?php endforeach; ?>

							</div>


							<?php
							endif;
							?>
						</div>

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


