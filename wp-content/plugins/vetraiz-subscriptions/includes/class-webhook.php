<?php
/**
 * Webhook handler
 *
 * @package Vetraiz_Subscriptions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Vetraiz_Subscriptions_Webhook {
	
	/**
	 * Instance
	 *
	 * @var Vetraiz_Subscriptions_Webhook
	 */
	private static $instance = null;
	
	/**
	 * Get instance
	 *
	 * @return Vetraiz_Subscriptions_Webhook
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
		add_action( 'template_redirect', array( $this, 'handle_webhook' ) );
	}
	
	/**
	 * Add rewrite rules
	 */
	public function add_rewrite_rules() {
		add_rewrite_rule( '^vetraiz-webhook/?$', 'index.php?vetraiz_webhook=1', 'top' );
		add_rewrite_tag( '%vetraiz_webhook%', '([^&]+)' );
	}
	
	/**
	 * Handle webhook
	 */
	public function handle_webhook() {
		if ( '1' !== get_query_var( 'vetraiz_webhook' ) ) {
			return;
		}
		
		$raw_data = file_get_contents( 'php://input' );
		$data = json_decode( $raw_data, true );
		
		if ( ! $data || ! isset( $data['event'] ) ) {
			status_header( 400 );
			die( 'Invalid webhook data' );
		}
		
		// Validate token (optional - can be configured in Asaas)
		$token = isset( $_SERVER['HTTP_ASAAS_ACCESS_TOKEN'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_ASAAS_ACCESS_TOKEN'] ) ) : '';
		$saved_token = get_option( 'vetraiz_asaas_webhook_token', '' );
		
		// Only validate if token is configured
		if ( $saved_token ) {
			if ( $token !== $saved_token ) {
				status_header( 401 );
				die( 'Invalid token' );
			}
		}
		
		// Process event
		switch ( $data['event'] ) {
			case 'PAYMENT_RECEIVED':
				$this->handle_payment_received( $data );
				break;
			case 'PAYMENT_CREATED':
				$this->handle_payment_created( $data );
				break;
			case 'PAYMENT_OVERDUE':
				$this->handle_payment_overdue( $data );
				break;
		}
		
		status_header( 200 );
		die( 'OK' );
	}
	
	/**
	 * Handle payment received
	 *
	 * @param array $data Webhook data.
	 */
	private function handle_payment_received( $data ) {
		if ( ! isset( $data['payment']['id'] ) ) {
			return;
		}
		
		$payment_id = $data['payment']['id'];
		$payment = Vetraiz_Subscriptions_Database::get_payment_by_asaas_id( $payment_id );
		
		if ( $payment ) {
			Vetraiz_Subscriptions_Payment::update_status( $payment_id, 'received' );
		} else {
			// Payment not found, try to create it
			global $wpdb;
			$subscription_table = $wpdb->prefix . 'vetraiz_subscriptions';
			$subscription = $wpdb->get_row( $wpdb->prepare(
				"SELECT * FROM $subscription_table WHERE asaas_subscription_id = %s",
				$data['payment']['subscription']
			) );
			
			if ( $subscription ) {
				Vetraiz_Subscriptions_Payment::create_from_asaas( $subscription->id, $subscription->user_id, $data['payment'] );
				Vetraiz_Subscriptions_Payment::update_status( $payment_id, 'received' );
			}
		}
	}
	
	/**
	 * Handle payment created
	 *
	 * @param array $data Webhook data.
	 */
	private function handle_payment_created( $data ) {
		if ( ! isset( $data['payment']['subscription'] ) ) {
			return;
		}
		
		global $wpdb;
		$subscription_table = $wpdb->prefix . 'vetraiz_subscriptions';
		$subscription = $wpdb->get_row( $wpdb->prepare(
			"SELECT * FROM $subscription_table WHERE asaas_subscription_id = %s",
			$data['payment']['subscription']
		) );
		
		if ( $subscription ) {
			Vetraiz_Subscriptions_Payment::create_from_asaas( $subscription->id, $subscription->user_id, $data['payment'] );
		}
	}
	
	/**
	 * Handle payment overdue
	 *
	 * @param array $data Webhook data.
	 */
	private function handle_payment_overdue( $data ) {
		if ( ! isset( $data['payment']['id'] ) ) {
			return;
		}
		
		$payment_id = $data['payment']['id'];
		Vetraiz_Subscriptions_Payment::update_status( $payment_id, 'overdue' );
	}
}

