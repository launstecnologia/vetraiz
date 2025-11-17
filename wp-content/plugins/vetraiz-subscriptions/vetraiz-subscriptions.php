<?php
/**
 * Plugin Name: Vetraiz Subscriptions
 * Plugin URI: https://vetraiz.com.br
 * Description: Sistema de assinaturas customizado com controle de acesso a vídeos
 * Version: 1.0.0
 * Author: Vetraiz
 * Author URI: https://vetraiz.com.br
 * Text Domain: vetraiz-subscriptions
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'VETRAIZ_SUBSCRIPTIONS_VERSION', '1.0.0' );
define( 'VETRAIZ_SUBSCRIPTIONS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'VETRAIZ_SUBSCRIPTIONS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Main plugin class
 */
class Vetraiz_Subscriptions {
	
	/**
	 * Instance
	 *
	 * @var Vetraiz_Subscriptions
	 */
	private static $instance = null;
	
	/**
	 * Get instance
	 *
	 * @return Vetraiz_Subscriptions
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
		$this->init();
	}
	
	/**
	 * Initialize plugin
	 */
	private function init() {
		// Load dependencies
		require_once VETRAIZ_SUBSCRIPTIONS_PLUGIN_DIR . 'includes/class-database.php';
		require_once VETRAIZ_SUBSCRIPTIONS_PLUGIN_DIR . 'includes/class-asaas-api.php';
		require_once VETRAIZ_SUBSCRIPTIONS_PLUGIN_DIR . 'includes/class-subscription.php';
		require_once VETRAIZ_SUBSCRIPTIONS_PLUGIN_DIR . 'includes/class-payment.php';
		require_once VETRAIZ_SUBSCRIPTIONS_PLUGIN_DIR . 'includes/class-access-control.php';
		require_once VETRAIZ_SUBSCRIPTIONS_PLUGIN_DIR . 'includes/class-admin.php';
		require_once VETRAIZ_SUBSCRIPTIONS_PLUGIN_DIR . 'includes/class-frontend.php';
		require_once VETRAIZ_SUBSCRIPTIONS_PLUGIN_DIR . 'includes/class-webhook.php';
		require_once VETRAIZ_SUBSCRIPTIONS_PLUGIN_DIR . 'includes/ajax-handlers.php';
		
		// Activation/Deactivation hooks
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );
		
		// Initialize components
		add_action( 'plugins_loaded', array( $this, 'load_components' ) );
	}
	
	/**
	 * Load components
	 */
	public function load_components() {
		// Initialize database
		Vetraiz_Subscriptions_Database::get_instance();
		
		// Initialize admin
		if ( is_admin() ) {
			Vetraiz_Subscriptions_Admin::get_instance();
		}
		
		// Initialize frontend
		Vetraiz_Subscriptions_Frontend::get_instance();
		
		// Initialize webhook
		Vetraiz_Subscriptions_Webhook::get_instance();
		
		// Initialize access control
		Vetraiz_Subscriptions_Access_Control::get_instance();
	}
	
	/**
	 * Activate plugin
	 */
	public function activate() {
		Vetraiz_Subscriptions_Database::create_tables();
		flush_rewrite_rules();
	}
	
	/**
	 * Deactivate plugin
	 */
	public function deactivate() {
		flush_rewrite_rules();
	}
}

// Initialize plugin
Vetraiz_Subscriptions::get_instance();

