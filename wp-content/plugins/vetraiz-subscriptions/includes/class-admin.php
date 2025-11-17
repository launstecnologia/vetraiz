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
		$subscriptions = $wpdb->get_results( "SELECT * FROM $table ORDER BY created_at DESC" );
		
		include VETRAIZ_SUBSCRIPTIONS_PLUGIN_DIR . 'templates/admin/subscriptions-list.php';
	}
}

