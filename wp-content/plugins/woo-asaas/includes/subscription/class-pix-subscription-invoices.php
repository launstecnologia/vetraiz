<?php
/**
 * PIX Subscription Invoices class
 *
 * @package WooAsaas
 */

namespace WC_Asaas\Subscription;

use WC_Asaas\WC_Asaas;
use WC_Asaas\Gateway\Pix;
use WC_Asaas\Gateway\Gateway;
use WC_Asaas\Api\Api;
use WC_Asaas\Api\Response\Error_Response;
use WC_Asaas\Meta_Data\Order;
use WC_Asaas\Meta_Data\Subscription_Meta;

/**
 * Manages PIX invoices for subscriptions
 */
class Pix_Subscription_Invoices {

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
		add_action( 'woocommerce_subscription_renewal_payment_complete', array( $this, 'maybe_create_pix_invoice' ), 10, 2 );
		add_action( 'woocommerce_subscription_status_on-hold', array( $this, 'maybe_create_pix_invoice_for_overdue' ), 10, 1 );
		add_action( 'woocommerce_subscription_renewal_order_created', array( $this, 'create_pix_invoice_for_renewal' ), 10, 2 );
	}

	/**
	 * Prevent the instance from being cloned.
	 */
	private function __clone() {
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
	 * Create PIX invoice for subscription renewal if payment method is PIX
	 *
	 * @param \WC_Subscription $subscription The subscription.
	 * @param \WC_Order        $renewal_order The renewal order.
	 * @return void
	 */
	public function maybe_create_pix_invoice( $subscription, $renewal_order ) {
		if ( 'asaas-pix' !== $renewal_order->get_payment_method() ) {
			return;
		}

		// Check if order already has a PIX payment
		$order_meta = new Order( $renewal_order->get_id() );
		if ( $order_meta->get_meta_data() ) {
			return; // Already has payment
		}

		$this->create_pix_payment_for_order( $renewal_order, $subscription );
	}

	/**
	 * Create PIX invoice for overdue subscription
	 *
	 * @param \WC_Subscription $subscription The subscription.
	 * @return void
	 */
	public function maybe_create_pix_invoice_for_overdue( $subscription ) {
		if ( 'asaas-pix' !== $subscription->get_payment_method() ) {
			return;
		}

		// Get the latest renewal order
		$renewal_orders = $subscription->get_related_orders( 'ids', 'renewal' );
		if ( empty( $renewal_orders ) ) {
			return;
		}

		$latest_renewal_id = max( $renewal_orders );
		$renewal_order     = wc_get_order( $latest_renewal_id );

		if ( ! $renewal_order || $renewal_order->is_paid() ) {
			return;
		}

		$order_meta = new Order( $renewal_order->get_id() );
		if ( $order_meta->get_meta_data() ) {
			return; // Already has payment
		}

		$this->create_pix_payment_for_order( $renewal_order, $subscription );
	}

	/**
	 * Create PIX invoice when renewal order is created
	 *
	 * @param \WC_Order        $renewal_order The renewal order.
	 * @param \WC_Subscription $subscription The subscription.
	 * @return void
	 */
	public function create_pix_invoice_for_renewal( $renewal_order, $subscription ) {
		if ( 'asaas-pix' !== $renewal_order->get_payment_method() ) {
			return;
		}

		// Only create if order needs payment
		if ( ! $renewal_order->needs_payment() ) {
			return;
		}

		// Check if order already has a PIX payment
		$order_meta = new Order( $renewal_order->get_id() );
		if ( $order_meta->get_meta_data() ) {
			return; // Already has payment
		}

		// Create PIX payment
		$this->create_pix_payment_for_order( $renewal_order, $subscription );
	}

	/**
	 * Create PIX payment for a renewal order
	 *
	 * @param \WC_Order        $order The order.
	 * @param \WC_Subscription $subscription The subscription.
	 * @return bool|string Payment ID on success, false on failure.
	 */
	public function create_pix_payment_for_order( $order, $subscription ) {
		$gateway = wc_get_payment_gateway_by_order( $order );
		if ( ! $gateway || 'asaas-pix' !== $gateway->id ) {
			return false;
		}

		// Ensure gateway is Pix instance
		if ( ! ( $gateway instanceof Pix ) ) {
			$gateway = WC_Asaas::get_instance()->get_gateway_by_id( 'asaas-pix' );
		}

		$api = new Api( $gateway );

		// Get customer
		$customer = $gateway->get_customer( $order->get_user_id() );
		if ( ! $customer || ! $customer->has_meta() ) {
			// Try to get from subscription
			$parent_order = $subscription->get_parent();
			if ( $parent_order ) {
				$parent_order_meta = new Order( $parent_order->get_id() );
				$parent_meta       = $parent_order_meta->get_meta_data();
				if ( $parent_meta && isset( $parent_meta->customer ) ) {
					$customer_id = $parent_meta->customer;
				} else {
					return false;
				}
			} else {
				return false;
			}
		} else {
			$customer_meta = $customer->get_meta();
			$customer_id   = $customer_meta['id'];
		}

		// Create PIX payment
		$payment_data = array(
			'customer'          => $customer_id,
			'billingType'       => 'PIX',
			'value'             => $order->get_total(),
			'dueDate'           => $gateway->create_due_date()->format( 'Y-m-d' ),
			'externalReference' => $order->get_id(),
			/* translators: %d: the order id  */
			'description'       => sprintf( __( 'Subscription Renewal Order #%d', 'woo-asaas' ), $order->get_id() ),
		);

		// Check if subscription has Asaas subscription ID
		$subscription_meta = new Subscription_Meta( $subscription->get_id() );
		$asaas_subscription_id = $subscription_meta->get_subscription_id();

		if ( $asaas_subscription_id ) {
			// Link to subscription if exists
			$payment_data['subscription'] = $asaas_subscription_id;
		}

		$response = $api->payments()->create( $payment_data );

		if ( is_a( $response, Error_Response::class ) ) {
			$gateway->get_logger()->log( 'Failed to create PIX payment for renewal order #' . $order->get_id() . ': ' . wp_json_encode( $response->get_errors() ) );
			return false;
		}

		$payment_created = $response->get_json();

		// Get PIX info
		$pix_info_response = $api->payments()->pix_info( $payment_created->id );
		if ( is_a( $pix_info_response, Error_Response::class ) ) {
			$gateway->get_logger()->log( 'Failed to get PIX info for payment #' . $payment_created->id );
			// Still save the payment, but without PIX info
			$order_meta = new Order( $order->get_id() );
			$order_meta->set_meta_data( $payment_created );
			$gateway->add_payment_id_to_order( $payment_created->id, $order );
			return $payment_created->id;
		}

		$pix_info = $pix_info_response->get_json();
		$json     = $gateway->join_responses( $payment_created, $pix_info );

		// Save payment data to order
		$order_meta = new Order( $order->get_id() );
		$order_meta->set_meta_data( $json );
		$gateway->add_payment_id_to_order( $payment_created->id, $order );

		// Update order status
		$gateway->awaiting_payment_status( $order, __( 'PIX payment created. Waiting for payment.', 'woo-asaas' ) );

		return $payment_created->id;
	}

	/**
	 * Get PIX payment info for an order
	 *
	 * @param int $order_id The order ID.
	 * @return object|false PIX payment info or false if not found.
	 */
	public function get_pix_payment_info( $order_id ) {
		$order = wc_get_order( $order_id );
		if ( ! $order || 'asaas-pix' !== $order->get_payment_method() ) {
			return false;
		}

		$order_meta = new Order( $order_id );
		$meta_data  = $order_meta->get_meta_data();

		if ( ! $meta_data ) {
			// Try to create payment if it doesn't exist
			$subscriptions = wcs_get_subscriptions_for_order( $order, array( 'order_type' => array( 'any' ) ) );
			if ( ! empty( $subscriptions ) ) {
				$subscription = reset( $subscriptions );
				$this->create_pix_payment_for_order( $order, $subscription );
				$order_meta = new Order( $order_id );
				$meta_data  = $order_meta->get_meta_data();
			}
		}

		return $meta_data;
	}
}

