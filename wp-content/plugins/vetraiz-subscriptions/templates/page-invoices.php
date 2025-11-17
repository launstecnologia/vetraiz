<?php
/**
 * Invoices page template
 *
 * @package Vetraiz_Subscriptions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<div class="vetraiz-invoices-page">
	<?php
	$user_id = get_current_user_id();
	$payments = Vetraiz_Subscriptions_Payment::get_user_payments( $user_id );
	include VETRAIZ_SUBSCRIPTIONS_PLUGIN_DIR . 'templates/my-invoices.php';
	?>
</div>

<?php
get_footer();

