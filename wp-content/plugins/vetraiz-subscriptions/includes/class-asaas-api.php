<?php
/**
 * Asaas API integration
 *
 * @package Vetraiz_Subscriptions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Vetraiz_Subscriptions_Asaas_API {
	
	/**
	 * API Key
	 *
	 * @var string
	 */
	private $api_key;
	
	/**
	 * API URL
	 *
	 * @var string
	 */
	private $api_url = 'https://api.asaas.com/v3';
	
	/**
	 * Constructor
	 */
	public function __construct() {
		$this->api_key = get_option( 'vetraiz_asaas_api_key', '' );
		$sandbox = get_option( 'vetraiz_asaas_sandbox', false );
		
		if ( $sandbox ) {
			$this->api_url = 'https://sandbox.asaas.com/api/v3';
		}
	}
	
	/**
	 * Make API request
	 *
	 * @param string $endpoint Endpoint.
	 * @param array  $data Data.
	 * @param string $method Method.
	 * @return array|WP_Error
	 */
	private function request( $endpoint, $data = array(), $method = 'GET' ) {
		$url = $this->api_url . '/' . ltrim( $endpoint, '/' );
		
		$headers = array(
			'Content-Type' => 'application/json',
		);
		
		// Asaas API v3 uses access_token as header
		if ( ! empty( $this->api_key ) ) {
			$headers['access_token'] = $this->api_key;
		}
		
		$args = array(
			'method'  => $method,
			'headers' => $headers,
			'timeout' => 30,
		);
		
		if ( ! empty( $data ) && in_array( $method, array( 'POST', 'PUT', 'PATCH' ), true ) ) {
			$args['body'] = wp_json_encode( $data );
		}
		
		$response = wp_remote_request( $url, $args );
		
		if ( is_wp_error( $response ) ) {
			return $response;
		}
		
		$body = wp_remote_retrieve_body( $response );
		$code = wp_remote_retrieve_response_code( $response );
		$decoded_body = json_decode( $body, true );
		
		// Log errors for debugging
		if ( $code >= 400 ) {
			if ( defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
				error_log( 'VETRAIZ ASAAS API ERROR: ' . $endpoint . ' - Code: ' . $code . ' - Response: ' . $body );
			}
		}
		
		return array(
			'code' => $code,
			'body' => $decoded_body,
		);
	}
	
	/**
	 * Create customer
	 *
	 * @param array $data Customer data.
	 * @return array|WP_Error
	 */
	public function create_customer( $data ) {
		return $this->request( 'customers', $data, 'POST' );
	}
	
	/**
	 * Create subscription
	 *
	 * @param array $data Subscription data.
	 * @return array|WP_Error
	 */
	public function create_subscription( $data ) {
		return $this->request( 'subscriptions', $data, 'POST' );
	}
	
	/**
	 * Get subscription
	 *
	 * @param string $subscription_id Subscription ID.
	 * @return array|WP_Error
	 */
	public function get_subscription( $subscription_id ) {
		return $this->request( "subscriptions/{$subscription_id}", array(), 'GET' );
	}
	
	/**
	 * Get subscription payments
	 *
	 * @param string $subscription_id Subscription ID.
	 * @return array|WP_Error
	 */
	public function get_subscription_payments( $subscription_id ) {
		return $this->request( "subscriptions/{$subscription_id}/payments", array(), 'GET' );
	}
	
	/**
	 * Get payment
	 *
	 * @param string $payment_id Payment ID.
	 * @return array|WP_Error
	 */
	public function get_payment( $payment_id ) {
		return $this->request( "payments/{$payment_id}", array(), 'GET' );
	}
	
	/**
	 * Get PIX info
	 *
	 * @param string $payment_id Payment ID.
	 * @return array|WP_Error
	 */
	public function get_pix_info( $payment_id ) {
		return $this->request( "payments/{$payment_id}/pixQrCode", array(), 'GET' );
	}
	
	/**
	 * Create credit card token
	 *
	 * @param array $data Card data.
	 * @return array|WP_Error
	 */
	public function create_credit_card_token( $data ) {
		return $this->request( 'creditCard/tokenize', $data, 'POST' );
	}
	
	/**
	 * Cancel subscription
	 *
	 * @param string $subscription_id Subscription ID.
	 * @return array|WP_Error
	 */
	public function cancel_subscription( $subscription_id ) {
		return $this->request( "subscriptions/{$subscription_id}", array(), 'DELETE' );
	}
	
	/**
	 * Delete subscription
	 *
	 * @param string $subscription_id Subscription ID.
	 * @return array|WP_Error
	 */
	public function delete_subscription( $subscription_id ) {
		return $this->request( "subscriptions/{$subscription_id}", array(), 'DELETE' );
	}
	
	/**
	 * Refund payment
	 *
	 * @param string $payment_id Payment ID.
	 * @return array|WP_Error
	 */
	public function refund_payment( $payment_id ) {
		return $this->request( "payments/{$payment_id}/refund", array(), 'DELETE' );
	}
	
	/**
	 * Confirm payment
	 *
	 * @param string $payment_id Payment ID.
	 * @return array|WP_Error
	 */
	public function confirm_payment( $payment_id ) {
		// Update payment status to received
		return $this->request( "payments/{$payment_id}", array( 'status' => 'RECEIVED' ), 'POST' );
	}
	
	/**
	 * Update subscription
	 *
	 * @param string $subscription_id Subscription ID.
	 * @param array  $data Data to update.
	 * @return array|WP_Error
	 */
	public function update_subscription( $subscription_id, $data ) {
		return $this->request( "subscriptions/{$subscription_id}", $data, 'POST' );
	}
}

