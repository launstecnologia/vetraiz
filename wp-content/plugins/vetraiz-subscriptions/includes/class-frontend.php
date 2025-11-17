<?php
/**
 * Frontend pages and forms
 *
 * @package Vetraiz_Subscriptions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Vetraiz_Subscriptions_Frontend {
	
	/**
	 * Instance
	 *
	 * @var Vetraiz_Subscriptions_Frontend
	 */
	private static $instance = null;
	
	/**
	 * Get instance
	 *
	 * @return Vetraiz_Subscriptions_Frontend
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	/**
	 * Constructor
	 */
	private function __construct() {
		add_action( 'init', array( $this, 'add_rewrite_rules' ) );
		add_action( 'template_redirect', array( $this, 'handle_requests' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		
		// Shortcodes
		add_shortcode( 'vetraiz_subscribe_form', array( $this, 'subscribe_form_shortcode' ) );
		add_shortcode( 'vetraiz_my_subscription', array( $this, 'my_subscription_shortcode' ) );
		add_shortcode( 'vetraiz_my_invoices', array( $this, 'my_invoices_shortcode' ) );
	}
	
	/**
	 * Add rewrite rules
	 */
	public function add_rewrite_rules() {
		add_rewrite_rule( '^minha-assinatura/?$', 'index.php?vetraiz_page=subscription', 'top' );
		add_rewrite_rule( '^minhas-faturas/?$', 'index.php?vetraiz_page=invoices', 'top' );
		add_rewrite_rule( '^fatura/([0-9]+)/?$', 'index.php?vetraiz_page=invoice&invoice_id=$matches[1]', 'top' );
		
		add_rewrite_tag( '%vetraiz_page%', '([^&]+)' );
		add_rewrite_tag( '%invoice_id%', '([0-9]+)' );
	}
	
	/**
	 * Handle requests
	 */
	public function handle_requests() {
		$page = get_query_var( 'vetraiz_page' );
		
		if ( ! $page ) {
			return;
		}
		
		if ( ! is_user_logged_in() ) {
			wp_redirect( wp_login_url( home_url( $_SERVER['REQUEST_URI'] ) ) );
			exit;
		}
		
		switch ( $page ) {
			case 'subscription':
				$this->render_subscription_page();
				break;
			case 'invoices':
				$this->render_invoices_page();
				break;
			case 'invoice':
				$invoice_id = get_query_var( 'invoice_id' );
				$this->render_invoice_page( $invoice_id );
				break;
		}
	}
	
	/**
	 * Enqueue scripts
	 */
	public function enqueue_scripts() {
		wp_enqueue_style( 'vetraiz-subscriptions', VETRAIZ_SUBSCRIPTIONS_PLUGIN_URL . 'assets/css/frontend.css', array(), VETRAIZ_SUBSCRIPTIONS_VERSION );
		wp_enqueue_script( 'vetraiz-subscriptions', VETRAIZ_SUBSCRIPTIONS_PLUGIN_URL . 'assets/js/frontend.js', array( 'jquery' ), VETRAIZ_SUBSCRIPTIONS_VERSION, true );
	}
	
	/**
	 * Subscribe form shortcode
	 *
	 * @return string
	 */
	public function subscribe_form_shortcode() {
		if ( ! is_user_logged_in() ) {
			return '<p>Você precisa estar logado para assinar. <a href="' . wp_login_url() . '">Fazer login</a></p>';
		}
		
		$user_id = get_current_user_id();
		
		// Check if user already has subscription
		$subscription = Vetraiz_Subscriptions_Subscription::get_user_subscription( $user_id );
		if ( $subscription && in_array( $subscription->status, array( 'active', 'pending' ), true ) ) {
			return '<div class="vetraiz-alert vetraiz-alert-info">Você já possui uma assinatura ativa. <a href="' . home_url( '/minha-assinatura' ) . '">Ver minha assinatura</a></div>';
		}
		
		ob_start();
		include VETRAIZ_SUBSCRIPTIONS_PLUGIN_DIR . 'templates/subscribe-form.php';
		return ob_get_clean();
	}
	
	/**
	 * My subscription shortcode
	 *
	 * @return string
	 */
	public function my_subscription_shortcode() {
		if ( ! is_user_logged_in() ) {
			return '<p>Você precisa estar logado. <a href="' . wp_login_url() . '">Fazer login</a></p>';
		}
		
		$user_id = get_current_user_id();
		$subscription = Vetraiz_Subscriptions_Subscription::get_user_subscription( $user_id );
		
		ob_start();
		include VETRAIZ_SUBSCRIPTIONS_PLUGIN_DIR . 'templates/my-subscription.php';
		return ob_get_clean();
	}
	
	/**
	 * My invoices shortcode
	 *
	 * @return string
	 */
	public function my_invoices_shortcode() {
		if ( ! is_user_logged_in() ) {
			return '<p>Você precisa estar logado. <a href="' . wp_login_url() . '">Fazer login</a></p>';
		}
		
		$user_id = get_current_user_id();
		$payments = Vetraiz_Subscriptions_Payment::get_user_payments( $user_id );
		
		ob_start();
		include VETRAIZ_SUBSCRIPTIONS_PLUGIN_DIR . 'templates/my-invoices.php';
		return ob_get_clean();
	}
	
	/**
	 * Render subscription page
	 */
	private function render_subscription_page() {
		$user_id = get_current_user_id();
		$subscription = Vetraiz_Subscriptions_Subscription::get_user_subscription( $user_id );
		
		include VETRAIZ_SUBSCRIPTIONS_PLUGIN_DIR . 'templates/page-subscription.php';
		exit;
	}
	
	/**
	 * Render invoices page
	 */
	private function render_invoices_page() {
		$user_id = get_current_user_id();
		$payments = Vetraiz_Subscriptions_Payment::get_user_payments( $user_id );
		
		include VETRAIZ_SUBSCRIPTIONS_PLUGIN_DIR . 'templates/page-invoices.php';
		exit;
	}
	
	/**
	 * Render invoice page
	 *
	 * @param int $invoice_id Invoice ID.
	 */
	private function render_invoice_page( $invoice_id ) {
		global $wpdb;
		$user_id = get_current_user_id();
		
		$table = $wpdb->prefix . 'vetraiz_subscription_payments';
		$payment = $wpdb->get_row( $wpdb->prepare(
			"SELECT * FROM $table WHERE id = %d AND user_id = %d",
			$invoice_id,
			$user_id
		) );
		
		if ( ! $payment ) {
			wp_die( 'Fatura não encontrada' );
		}
		
		// Make payment available to template
		$GLOBALS['vetraiz_payment'] = $payment;
		
		include VETRAIZ_SUBSCRIPTIONS_PLUGIN_DIR . 'templates/page-invoice.php';
		exit;
	}
}

