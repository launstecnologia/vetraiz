<?php
/**
 * Subscription page template
 *
 * @package Vetraiz_Subscriptions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<div class="vetraiz-subscription-page">
	<?php
	$user_id = get_current_user_id();
	$subscription = Vetraiz_Subscriptions_Subscription::get_user_subscription( $user_id );
	include VETRAIZ_SUBSCRIPTIONS_PLUGIN_DIR . 'templates/my-subscription.php';
	?>
</div>

<?php
get_footer();

