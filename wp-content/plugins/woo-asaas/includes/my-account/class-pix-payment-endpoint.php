<?php
/**
 * PIX Payment Endpoint class
 *
 * @package WooAsaas
 */

namespace WC_Asaas\My_Account;

use WC_Asaas\WC_Asaas;
use WC_Asaas\Subscription\Pix_Subscription_Invoices;
use WC_Asaas\Meta_Data\Order;

/**
 * Handles PIX payment viewing endpoint
 */
class Pix_Payment_Endpoint {

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
		add_action( 'init', array( $this, 'add_endpoint' ) );
		add_filter( 'query_vars', array( $this, 'add_query_vars' ) );
		add_action( 'woocommerce_account_view-pix-payment_endpoint', array( $this, 'render_pix_payment_page' ) );
		add_action( 'woocommerce_account_view-pix-payment_endpoint', array( $this, 'maybe_create_pix_payment' ), 5 );
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
	 * Add custom endpoint
	 *
	 * @return void
	 */
	public function add_endpoint() {
		add_rewrite_endpoint( 'view-pix-payment', EP_ROOT | EP_PAGES );
	}

	/**
	 * Add query vars
	 *
	 * @param array $vars Query vars.
	 * @return array
	 */
	public function add_query_vars( $vars ) {
		$vars[] = 'view-pix-payment';
		return $vars;
	}

	/**
	 * Maybe create PIX payment if it doesn't exist
	 *
	 * @return void
	 */
	public function maybe_create_pix_payment() {
		global $wp;

		if ( ! isset( $wp->query_vars['view-pix-payment'] ) ) {
			return;
		}

		$order_id = absint( $wp->query_vars['view-pix-payment'] );
		$order    = wc_get_order( $order_id );

		if ( ! $order ) {
			wc_add_notice( __( 'Pedido não encontrado.', 'woo-asaas' ), 'error' );
			wp_safe_redirect( wc_get_page_permalink( 'myaccount' ) );
			exit;
		}

		// Check if user owns this order
		if ( get_current_user_id() !== $order->get_user_id() ) {
			wc_add_notice( __( 'Você não tem permissão para visualizar este pedido.', 'woo-asaas' ), 'error' );
			wp_safe_redirect( wc_get_page_permalink( 'myaccount' ) );
			exit;
		}

		// Check if payment method is PIX
		if ( 'asaas-pix' !== $order->get_payment_method() ) {
			wc_add_notice( __( 'Este pedido não é um pagamento PIX.', 'woo-asaas' ), 'error' );
			wp_safe_redirect( wc_get_endpoint_url( 'orders', '', wc_get_page_permalink( 'myaccount' ) ) );
			exit;
		}

		$order_meta = new Order( $order_id );
		$pix_info   = $order_meta->get_meta_data();

		// If no PIX info, try to create payment
		if ( ! $pix_info && $order->needs_payment() ) {
			$subscriptions = wcs_get_subscriptions_for_order( $order, array( 'order_type' => array( 'any' ) ) );
			if ( ! empty( $subscriptions ) ) {
				$subscription      = reset( $subscriptions );
				$pix_invoices      = Pix_Subscription_Invoices::get_instance();
				$payment_id        = $pix_invoices->create_pix_payment_for_order( $order, $subscription );
				if ( $payment_id ) {
					wc_add_notice( __( 'Fatura PIX gerada com sucesso!', 'woo-asaas' ), 'success' );
				}
			}
		}
	}

	/**
	 * Render PIX payment page
	 *
	 * @return void
	 */
	public function render_pix_payment_page() {
		global $wp;

		if ( ! isset( $wp->query_vars['view-pix-payment'] ) ) {
			return;
		}

		$order_id = absint( $wp->query_vars['view-pix-payment'] );
		$order    = wc_get_order( $order_id );

		if ( ! $order ) {
			return;
		}

		$order_meta = new Order( $order_id );
		$pix_info   = $order_meta->get_meta_data();

		if ( ! $pix_info ) {
			echo '<div class="woocommerce-error">';
			echo '<p>' . esc_html__( 'Informações do PIX não encontradas. Por favor, entre em contato conosco.', 'woo-asaas' ) . '</p>';
			echo '</div>';
			return;
		}

		// Get PIX data
		$pix_qr_code = isset( $pix_info->payload ) ? $pix_info->payload : '';
		$pix_copy_paste = isset( $pix_info->payload ) ? $pix_info->payload : '';

		// Check if payment is already paid
		$is_paid = $order->is_paid() || in_array( $order->get_status(), wc_get_is_paid_statuses(), true );

		wc_get_template(
			'myaccount/pix-payment.php',
			array(
				'order'         => $order,
				'pix_info'      => $pix_info,
				'pix_qr_code'   => $pix_qr_code,
				'pix_copy_paste' => $pix_copy_paste,
				'is_paid'       => $is_paid,
			),
			'woocommerce/asaas/',
			WC_Asaas::get_instance()->get_templates_path()
		);
	}
}

