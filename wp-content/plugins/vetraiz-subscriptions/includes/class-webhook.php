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
		
		// Log raw data
		if ( defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
			error_log( 'VETRAIZ WEBHOOK: Raw data received - Length: ' . strlen( $raw_data ) );
			error_log( 'VETRAIZ WEBHOOK: Raw data: ' . substr( $raw_data, 0, 500 ) );
		}
		
		$data = json_decode( $raw_data, true );
		
		if ( ! $data || ! isset( $data['event'] ) ) {
			if ( defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
				error_log( 'VETRAIZ WEBHOOK: Invalid webhook data or event not found' );
			}
			status_header( 400 );
			die( 'Invalid webhook data' );
		}
		
		// Log event
		if ( defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
			error_log( 'VETRAIZ WEBHOOK: Event received - ' . $data['event'] );
		}
		
		// Validate token (optional - can be configured in Asaas)
		$token = isset( $_SERVER['HTTP_ASAAS_ACCESS_TOKEN'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_ASAAS_ACCESS_TOKEN'] ) ) : '';
		$saved_token = get_option( 'vetraiz_asaas_webhook_token', '' );
		
		// Only validate if token is configured
		if ( $saved_token ) {
			if ( $token !== $saved_token ) {
				if ( defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
					error_log( 'VETRAIZ WEBHOOK: Invalid token' );
				}
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
			default:
				if ( defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
					error_log( 'VETRAIZ WEBHOOK: Unknown event - ' . $data['event'] );
				}
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
			if ( defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
				error_log( 'VETRAIZ WEBHOOK: PAYMENT_RECEIVED - Payment ID not found in data' );
			}
			return;
		}
		
		$payment_id = $data['payment']['id'];
		$payment_date = isset( $data['payment']['paymentDate'] ) ? date( 'Y-m-d H:i:s', strtotime( $data['payment']['paymentDate'] ) ) : null;
		$subscription_id = isset( $data['payment']['subscription'] ) ? $data['payment']['subscription'] : null;
		$external_reference = isset( $data['payment']['externalReference'] ) ? $data['payment']['externalReference'] : null;
		
		// Log webhook received
		if ( defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
			error_log( 'VETRAIZ WEBHOOK: PAYMENT_RECEIVED - Payment ID: ' . $payment_id . ' - Subscription: ' . $subscription_id . ' - External Ref: ' . $external_reference . ' - Date: ' . $payment_date );
		}
		
		// Try to find payment by Asaas payment ID
		$payment = Vetraiz_Subscriptions_Database::get_payment_by_asaas_id( $payment_id );
		
		if ( $payment ) {
			// Payment exists, update status
			$result = Vetraiz_Subscriptions_Payment::update_status( $payment_id, 'received', $payment_date );
			
			// Log update
			if ( defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
				error_log( 'VETRAIZ WEBHOOK: Payment found and status updated to received for payment: ' . $payment_id . ' - Result: ' . ( $result ? 'success' : 'failed' ) );
			}
		} else {
			// Payment not found, try to create it
			global $wpdb;
			$subscription_table = $wpdb->prefix . 'vetraiz_subscriptions';
			$subscription = null;
			
			// Try to find subscription by subscription ID first
			if ( $subscription_id ) {
				$subscription = $wpdb->get_row( $wpdb->prepare(
					"SELECT * FROM $subscription_table WHERE asaas_subscription_id = %s",
					$subscription_id
				) );
			}
			
			// If not found by subscription ID, try by external reference (user_id)
			if ( ! $subscription && $external_reference ) {
				$user_id = intval( $external_reference );
				if ( $user_id > 0 ) {
					$subscription = $wpdb->get_row( $wpdb->prepare(
						"SELECT * FROM $subscription_table WHERE user_id = %d ORDER BY created_at DESC LIMIT 1",
						$user_id
					) );
				}
			}
			
			if ( $subscription ) {
				// Create payment from Asaas data
				$created = Vetraiz_Subscriptions_Payment::create_from_asaas( $subscription->id, $subscription->user_id, $data['payment'] );
				
				if ( $created ) {
					// Update status to received
					Vetraiz_Subscriptions_Payment::update_status( $payment_id, 'received', $payment_date );
					
					// Log creation
					if ( defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
						error_log( 'VETRAIZ WEBHOOK: Payment created and updated to received for payment: ' . $payment_id . ' - Subscription ID: ' . $subscription->id );
					}
				} else {
					if ( defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
						error_log( 'VETRAIZ WEBHOOK: Failed to create payment for payment ID: ' . $payment_id );
					}
				}
			} else {
				// Log error - subscription not found
				if ( defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
					error_log( 'VETRAIZ WEBHOOK: Subscription not found for payment: ' . $payment_id . ' - Subscription ID: ' . $subscription_id . ' - External Ref: ' . $external_reference );
				}
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

