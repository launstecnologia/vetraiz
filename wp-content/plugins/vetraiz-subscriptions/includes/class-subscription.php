<?php
/**
 * Subscription management
 *
 * @package Vetraiz_Subscriptions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Vetraiz_Subscriptions_Subscription {
	
	/**
	 * Create subscription
	 *
	 * @param int   $user_id User ID.
	 * @param array $data Subscription data.
	 * @return int|WP_Error Subscription ID or error.
	 */
	public static function create( $user_id, $data ) {
		global $wpdb;
		
		$user = get_userdata( $user_id );
		if ( ! $user ) {
			return new WP_Error( 'invalid_user', 'Usuário inválido' );
		}
		
		$api = new Vetraiz_Subscriptions_Asaas_API();
		
		// Get or create customer in Asaas
		$customer_id = get_user_meta( $user_id, '_vetraiz_asaas_customer_id', true );
		
		if ( ! $customer_id ) {
			// Create customer
			$customer_data = array(
				'name'                  => $user->display_name,
				'email'                 => $user->user_email,
				'phone'                 => get_user_meta( $user_id, 'billing_phone', true ) ?: '',
				'mobilePhone'           => get_user_meta( $user_id, 'billing_phone', true ) ?: '',
				'cpfCnpj'               => get_user_meta( $user_id, 'billing_cpf', true ) ?: '',
				'postalCode'            => get_user_meta( $user_id, 'billing_postcode', true ) ?: '',
				'address'               => get_user_meta( $user_id, 'billing_address_1', true ) ?: '',
				'addressNumber'         => get_user_meta( $user_id, 'billing_number', true ) ?: '',
				'complement'            => get_user_meta( $user_id, 'billing_address_2', true ) ?: '',
				'province'              => get_user_meta( $user_id, 'billing_neighborhood', true ) ?: '',
				'city'                  => get_user_meta( $user_id, 'billing_city', true ) ?: '',
				'state'                 => get_user_meta( $user_id, 'billing_state', true ) ?: '',
				'externalReference'     => (string) $user_id,
			);
			
			$customer_response = $api->create_customer( $customer_data );
			
			if ( is_wp_error( $customer_response ) || 200 !== $customer_response['code'] ) {
				return new WP_Error( 'customer_creation_failed', 'Erro ao criar cliente no Asaas' );
			}
			
			$customer_id = $customer_response['body']['id'];
			update_user_meta( $user_id, '_vetraiz_asaas_customer_id', $customer_id );
		}
		
		// Get payment method
		$payment_method = isset( $data['payment_method'] ) ? $data['payment_method'] : 'PIX';
		$card_data = isset( $data['card_data'] ) ? $data['card_data'] : null;
		
		// Create credit card token if needed
		$credit_card_token = null;
		if ( 'CREDIT_CARD' === $payment_method && $card_data ) {
			// Tokenize card
			$token_data = array(
				'customer' => $customer_id,
				'creditCard' => array(
					'holderName' => $card_data['holderName'],
					'number'     => $card_data['number'],
					'expiryMonth' => $card_data['expiryMonth'],
					'expiryYear'  => $card_data['expiryYear'],
					'ccv'         => $card_data['ccv'],
				),
				'creditCardHolderInfo' => array(
					'name'              => $user->display_name,
					'email'             => $user->user_email,
					'cpfCnpj'           => get_user_meta( $user_id, 'billing_cpf', true ) ?: '',
					'postalCode'        => get_user_meta( $user_id, 'billing_postcode', true ) ?: '',
					'addressNumber'     => get_user_meta( $user_id, 'billing_number', true ) ?: '',
					'addressComplement' => get_user_meta( $user_id, 'billing_address_2', true ) ?: '',
					'phone'             => get_user_meta( $user_id, 'billing_phone', true ) ?: '',
				),
			);
			
			$token_response = $api->create_credit_card_token( $token_data );
			
			if ( is_wp_error( $token_response ) || 200 !== $token_response['code'] ) {
				$error_message = isset( $token_response['body']['errors'] ) ? json_encode( $token_response['body']['errors'] ) : 'Erro ao processar cartão';
				return new WP_Error( 'card_tokenization_failed', 'Erro ao processar cartão: ' . $error_message );
			}
			
			$credit_card_token = $token_response['body']['creditCardToken'];
		}
		
		// Create subscription in Asaas
		$subscription_data = array(
			'customer'          => $customer_id,
			'billingType'       => $payment_method,
			'value'             => floatval( $data['value'] ),
			'nextDueDate'       => date( 'Y-m-d', strtotime( '+1 month' ) ),
			'cycle'             => 'MONTHLY',
			'description'       => $data['plan_name'],
			'externalReference' => (string) $user_id,
		);
		
		// Add credit card token if credit card
		if ( 'CREDIT_CARD' === $payment_method && $credit_card_token ) {
			$subscription_data['creditCardToken'] = $credit_card_token;
		}
		
		$subscription_response = $api->create_subscription( $subscription_data );
		
		if ( is_wp_error( $subscription_response ) || 200 !== $subscription_response['code'] ) {
			$error_message = isset( $subscription_response['body']['errors'] ) ? json_encode( $subscription_response['body']['errors'] ) : 'Erro ao criar assinatura no Asaas';
			return new WP_Error( 'subscription_creation_failed', $error_message );
		}
		
		$asaas_subscription = $subscription_response['body'];
		
		// Save subscription in database
		$table = $wpdb->prefix . 'vetraiz_subscriptions';
		$wpdb->insert(
			$table,
			array(
				'user_id'              => $user_id,
				'asaas_subscription_id' => $asaas_subscription['id'],
				'asaas_customer_id'     => $customer_id,
				'plan_name'             => $data['plan_name'],
				'plan_value'            => $data['value'],
				'payment_method'        => $payment_method,
				'status'                => 'pending',
				'start_date'            => current_time( 'mysql' ),
				'next_payment_date'     => $asaas_subscription['nextDueDate'] ? date( 'Y-m-d H:i:s', strtotime( $asaas_subscription['nextDueDate'] ) ) : null,
			),
			array( '%d', '%s', '%s', '%s', '%f', '%s', '%s', '%s', '%s' )
		);
		
		$subscription_id = $wpdb->insert_id;
		
		// Get first payment and save it
		$payments_response = $api->get_subscription_payments( $asaas_subscription['id'] );
		if ( ! is_wp_error( $payments_response ) && 200 === $payments_response['code'] && ! empty( $payments_response['body']['data'] ) ) {
			$first_payment = $payments_response['body']['data'][0];
			Vetraiz_Subscriptions_Payment::create_from_asaas( $subscription_id, $user_id, $first_payment );
		}
		
		return $subscription_id;
	}
	
	/**
	 * Get user subscription
	 *
	 * @param int $user_id User ID.
	 * @return object|null
	 */
	public static function get_user_subscription( $user_id ) {
		// Try to get from cache first
		$cache_key = 'vetraiz_subscription_user_' . $user_id;
		$subscription = wp_cache_get( $cache_key, 'vetraiz_subscriptions' );
		
		if ( false === $subscription ) {
			$subscription = Vetraiz_Subscriptions_Database::get_subscription_by_user( $user_id );
			wp_cache_set( $cache_key, $subscription, 'vetraiz_subscriptions', 3600 );
		}
		
		return $subscription;
	}
	
	/**
	 * Check if user has active subscription
	 *
	 * @param int $user_id User ID.
	 * @return bool
	 */
	public static function user_has_active_subscription( $user_id ) {
		$subscription = self::get_user_subscription( $user_id );
		
		if ( ! $subscription ) {
			return false;
		}
		
		// Check if subscription is active
		if ( 'active' === $subscription->status ) {
			return true;
		}
		
		// If status is pending, check if there's a received payment
		if ( 'pending' === $subscription->status ) {
			global $wpdb;
			$payments_table = $wpdb->prefix . 'vetraiz_subscription_payments';
			$received_payment = $wpdb->get_var( $wpdb->prepare(
				"SELECT COUNT(*) FROM $payments_table WHERE subscription_id = %d AND status = 'received'",
				$subscription->id
			) );
			
			if ( $received_payment > 0 ) {
				// Update subscription to active
				$subscription_table = $wpdb->prefix . 'vetraiz_subscriptions';
				$wpdb->update(
					$subscription_table,
					array( 
						'status' => 'active',
						'updated_at' => current_time( 'mysql' ),
					),
					array( 'id' => $subscription->id ),
					array( '%s', '%s' ),
					array( '%d' )
				);
				
				// Clear cache
				wp_cache_delete( 'vetraiz_subscription_user_' . $user_id, 'vetraiz_subscriptions' );
				
				return true;
			}
		}
		
		return false;
	}
}

