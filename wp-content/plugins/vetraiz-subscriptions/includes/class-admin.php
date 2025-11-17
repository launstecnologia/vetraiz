<?php
/**
 * Admin settings
 *
 * @package Vetraiz_Subscriptions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Vetraiz_Subscriptions_Admin {
	
	/**
	 * Instance
	 *
	 * @var Vetraiz_Subscriptions_Admin
	 */
	private static $instance = null;
	
	/**
	 * Get instance
	 *
	 * @return Vetraiz_Subscriptions_Admin
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
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}
	
	/**
	 * Add admin menu
	 */
	public function add_admin_menu() {
		add_menu_page(
			'Vetraiz Assinaturas',
			'Assinaturas',
			'manage_options',
			'vetraiz-subscriptions',
			array( $this, 'render_settings_page' ),
			'dashicons-video-alt3',
			30
		);
		
		add_submenu_page(
			'vetraiz-subscriptions',
			'Configurações',
			'Configurações',
			'manage_options',
			'vetraiz-subscriptions',
			array( $this, 'render_settings_page' )
		);
		
		add_submenu_page(
			'vetraiz-subscriptions',
			'Todas as Assinaturas',
			'Todas as Assinaturas',
			'manage_options',
			'vetraiz-subscriptions-list',
			array( $this, 'render_subscriptions_list' )
		);
		
		add_submenu_page(
			'vetraiz-subscriptions',
			'Pagamentos',
			'Pagamentos',
			'manage_options',
			'vetraiz-subscriptions-payments',
			array( $this, 'render_payments_list' )
		);
	}
	
	/**
	 * Register settings
	 */
	public function register_settings() {
		register_setting( 'vetraiz_subscriptions_settings', 'vetraiz_asaas_api_key' );
		register_setting( 'vetraiz_subscriptions_settings', 'vetraiz_asaas_sandbox' );
		register_setting( 'vetraiz_subscriptions_settings', 'vetraiz_asaas_webhook_token' );
		register_setting( 'vetraiz_subscriptions_settings', 'vetraiz_subscribe_page_id' );
		register_setting( 'vetraiz_subscriptions_settings', 'vetraiz_plan_name' );
		register_setting( 'vetraiz_subscriptions_settings', 'vetraiz_plan_value' );
		register_setting( 'vetraiz_subscriptions_settings', 'vetraiz_video_post_type' );
		register_setting( 'vetraiz_subscriptions_settings', 'vetraiz_video_category' );
		register_setting( 'vetraiz_subscriptions_settings', 'vetraiz_video_url_patterns' );
		register_setting( 'vetraiz_subscriptions_settings', 'vetraiz_protect_all_videos' );
		
		// Sanitize URL patterns
		add_filter( 'sanitize_option_vetraiz_video_url_patterns', array( $this, 'sanitize_url_patterns' ) );
	}
	
	/**
	 * Sanitize URL patterns
	 *
	 * @param string $value URL patterns.
	 * @return array
	 */
	public function sanitize_url_patterns( $value ) {
		if ( is_array( $value ) ) {
			return $value;
		}
		
		$patterns = explode( "\n", $value );
		$patterns = array_map( 'trim', $patterns );
		$patterns = array_filter( $patterns );
		
		return $patterns;
	}
	
	/**
	 * Render settings page
	 */
	public function render_settings_page() {
		include VETRAIZ_SUBSCRIPTIONS_PLUGIN_DIR . 'templates/admin/settings.php';
	}
	
	/**
	 * Render subscriptions list
	 */
	public function render_subscriptions_list() {
		global $wpdb;
		$table = $wpdb->prefix . 'vetraiz_subscriptions';
		$payments_table = $wpdb->prefix . 'vetraiz_subscription_payments';
		
		// Get subscriptions with payment counts
		$subscriptions = $wpdb->get_results( 
			"SELECT s.*, 
				COUNT(p.id) as payment_count,
				SUM(CASE WHEN p.status = 'received' THEN 1 ELSE 0 END) as received_count
			FROM $table s
			LEFT JOIN $payments_table p ON s.id = p.subscription_id
			GROUP BY s.id
			ORDER BY s.created_at DESC"
		);
		
		include VETRAIZ_SUBSCRIPTIONS_PLUGIN_DIR . 'templates/admin/subscriptions-list.php';
	}
	
	/**
	 * Render payments list
	 */
	public function render_payments_list() {
		global $wpdb;
		$payments_table = $wpdb->prefix . 'vetraiz_subscription_payments';
		$subscriptions_table = $wpdb->prefix . 'vetraiz_subscriptions';
		
		$subscription_id = isset( $_GET['subscription_id'] ) ? intval( $_GET['subscription_id'] ) : 0;
		
		if ( $subscription_id > 0 ) {
			$payments = $wpdb->get_results( $wpdb->prepare(
				"SELECT p.*, s.plan_name, s.user_id 
				FROM $payments_table p
				INNER JOIN $subscriptions_table s ON p.subscription_id = s.id
				WHERE p.subscription_id = %d
				ORDER BY p.created_at DESC",
				$subscription_id
			) );
			$subscription = $wpdb->get_row( $wpdb->prepare(
				"SELECT * FROM $subscriptions_table WHERE id = %d",
				$subscription_id
			) );
		} else {
			$payments = $wpdb->get_results( 
				"SELECT p.*, s.plan_name, s.user_id 
				FROM $payments_table p
				INNER JOIN $subscriptions_table s ON p.subscription_id = s.id
				ORDER BY p.created_at DESC
				LIMIT 100"
			);
			$subscription = null;
		}
		
		include VETRAIZ_SUBSCRIPTIONS_PLUGIN_DIR . 'templates/admin/payments-list.php';
	}
}

