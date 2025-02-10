<?php
/**
 * @var int $bundle_id
 */

use MasterStudy\Lms\Pro\addons\CourseBundle\Repository\CourseBundleRepository;



wp_enqueue_style( 'masterstudy-bundle-button' );
wp_enqueue_script( 'masterstudy-bundle-button' );

$guest_checkout       = STM_LMS_Options::get_option( 'guest_checkout', false );
$bundle_price         = CourseBundleRepository::get_bundle_price( $bundle_id );
$bundle_courses_price = CourseBundleRepository::get_bundle_courses_price( $bundle_id );
$is_logged            = is_user_logged_in();
wp_localize_script(
	'masterstudy-bundle-button',
	'bundle_data',
	array(
		'guest_checkout' => $guest_checkout && ! $is_logged,
		'guest_nonce'    => wp_create_nonce( 'stm_lms_add_to_cart_guest' ),
		'nonce'          => wp_create_nonce( 'stm_lms_add_bundle_to_cart' ),
	)
);
if (!function_exists('is_bundle_purchased')){
function is_bundle_purchased($bundle_id) {
	$user_id = get_current_user_id();
   
	if (!$user_id) {
		return false;
	}

	$customer_orders = wc_get_orders(array(
		'customer_id' => $user_id,
		'status' => array('completed'),
		'limit' => -1,
	));

	foreach ($customer_orders as $order) {
		$items = $order->get_items();
	 
		foreach ($items as $item) {
			$product_id = $item->get_product_id();
			$stm_lms_product_id = get_post_meta($product_id, 'stm_lms_product_id', true);

			if ($stm_lms_product_id == $bundle_id) {
				return true;
			}
		}
	}

	return false;
}
}

$is_bundle_purchased = is_bundle_purchased($bundle_id);
?>

<a
	href="<?php echo $is_bundle_purchased ? esc_url('/benutzerkonto/enrolled-courses/') : '#'; ?>"
	class="masterstudy-bundle-button <?php echo ($is_logged || ( ! $is_logged && $guest_checkout )) && !$is_bundle_purchased ? 'masterstudy-bundle-button_active' : ''; ?>"
	<?php echo ($is_logged || ( ! $is_logged && $guest_checkout )) && !$is_bundle_purchased ? 'data-bundle="' . intval( $bundle_id ) . '"' : 'data-authorization-modal="login"'; ?>
>
	<span class="masterstudy-bundle-button__title">
		<?php 
		if ($is_bundle_purchased) {
			esc_html_e('View Courses', 'masterstudy-lms-learning-management-system-pro');
		} else {
			esc_html_e('Get now', 'masterstudy-lms-learning-management-system-pro');
		}
		?>
	</span>
	<?php if (!$is_bundle_purchased && (!empty($bundle_courses_price) || !empty($bundle_price))) { ?>
		<span class="masterstudy-bundle-button__separator"></span>
		<div class="masterstudy-bundle-button__price">
			<?php if (!empty($bundle_price)) { ?>
				<span class="masterstudy-bundle-button__price-bundle">
					<?php echo esc_html(STM_LMS_Helpers::display_price($bundle_price)); ?>
				</span>
				<?php
			}
			if (!empty($bundle_courses_price)) {
				?>
				<span class="masterstudy-bundle-button__price-courses">
					<?php echo esc_html(STM_LMS_Helpers::display_price($bundle_courses_price)); ?>
				</span>
			<?php } ?>
		</div>
	<?php } ?>
</a>
