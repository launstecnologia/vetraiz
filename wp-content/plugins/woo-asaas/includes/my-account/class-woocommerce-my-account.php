<?php
/**
 * WooCommerce My Account class
 *
 * @package WooAsaas
 */

namespace WC_Asaas\My_Account;

use Exception;
use WC_Asaas\Subscription\Pix_Subscription_Invoices;
use WC_Asaas\Meta_Data\Order;

/**
 * Interact with WooCommerce My Account settings
 */
class WooCommerce_My_Account {

	/**
	 * Instance of this class
	 *
	 * @var self
	 */
	protected static $instance = null;


	/**
	 * Is not allowed to call from outside to prevent from creating multiple instances.
	 */
	private function __construct() {
	}

	/**
	 * Prevent the instance from being cloned.
	 */
	private function __clone() {
	}

	/**
	 * Prevent from being unserialized.
	 *
	 * @throws Exception If create a second instance of it.
	 */
	public function __wakeup() {
		throw new Exception( esc_html__( 'Cannot unserialize singleton', 'woo-asaas' ) );
	}

	/**
	 * Return an instance of this class
	 *
	 * @return self A single instance of this class.
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Filters my orders actions based on related payment gateways.
	 *
	 * @param array     $actions My orders actions.
	 * @param \WC_Order $order The WooCommerce order.
	 * @return array    $actions The filtered my orders actions.
	 */
	public function my_orders_actions( $actions, $order ) {
		// For PIX orders, replace pay action with view PIX action
		if ( 'asaas-pix' === $order->get_payment_method() ) {
			// Remove default pay action
			unset( $actions['pay'] );

			// Add view/pay PIX action for pending orders
			if ( $order->needs_payment() || $order->has_status( array( 'pending', 'on-hold', 'failed' ) ) ) {
				$pix_invoices = Pix_Subscription_Invoices::get_instance();
				$pix_info     = $pix_invoices->get_pix_payment_info( $order->get_id() );

				if ( $pix_info || $this->is_subscription_renewal( $order ) ) {
					$actions['pay-pix'] = array(
						'url'  => wc_get_endpoint_url( 'view-pix-payment', $order->get_id(), wc_get_page_permalink( 'myaccount' ) ),
						'name' => __( 'Pagar com PIX', 'woo-asaas' ),
					);
				}
			}
		}

		// Removes the pay action for Asaas Ticket orders.
		if ( 'asaas-ticket' === $order->get_payment_method() && isset( $actions['pay'] ) ) {
			unset( $actions['pay'] );
		}

		return $actions;
	}

	/**
	 * Check if order is a subscription renewal
	 *
	 * @param \WC_Order $order The order.
	 * @return bool
	 */
	private function is_subscription_renewal( $order ) {
		if ( ! function_exists( 'wcs_get_subscriptions_for_order' ) ) {
			return false;
		}

		$subscriptions = wcs_get_subscriptions_for_order( $order, array( 'order_type' => array( 'renewal' ) ) );
		return ! empty( $subscriptions );
	}

}
