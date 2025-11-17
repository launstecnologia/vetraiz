<?php
/**
 * Payment management
 *
 * @package Vetraiz_Subscriptions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Vetraiz_Subscriptions_Payment {
	
	/**
	 * Create payment from Asaas data
	 *
	 * @param int   $subscription_id Subscription ID.
	 * @param int   $user_id User ID.
	 * @param array $payment_data Payment data from Asaas.
	 * @return int|WP_Error Payment ID or error.
	 */
	public static function create_from_asaas( $subscription_id, $user_id, $payment_data ) {
		global $wpdb;
		
		$table = $wpdb->prefix . 'vetraiz_subscription_payments';
		
		// Check if payment already exists
		$existing = $wpdb->get_row( $wpdb->prepare(
			"SELECT id FROM $table WHERE asaas_payment_id = %s",
			$payment_data['id']
		) );
		
		if ( $existing ) {
			// Update existing payment
			$wpdb->update(
				$table,
				array(
					'value'       => floatval( $payment_data['value'] ),
					'status'      => strtolower( $payment_data['status'] ),
					'due_date'    => $payment_data['dueDate'] ? date( 'Y-m-d', strtotime( $payment_data['dueDate'] ) ) : null,
					'payment_date' => $payment_data['paymentDate'] ? date( 'Y-m-d H:i:s', strtotime( $payment_data['paymentDate'] ) ) : null,
					'updated_at'  => current_time( 'mysql' ),
				),
				array( 'id' => $existing->id ),
				array( '%f', '%s', '%s', '%s', '%s' ),
				array( '%d' )
			);
			
			return $existing->id;
		}
		
		// Get PIX info if payment is PIX
		$pix_qr_code = null;
		$pix_code = null;
		
		if ( 'PIX' === $payment_data['billingType'] && 'PENDING' === $payment_data['status'] ) {
			$api = new Vetraiz_Subscriptions_Asaas_API();
			$pix_response = $api->get_pix_info( $payment_data['id'] );
			
			if ( ! is_wp_error( $pix_response ) && 200 === $pix_response['code'] ) {
				$pix_data = $pix_response['body'];
				$pix_qr_code = isset( $pix_data['encodedImage'] ) ? $pix_data['encodedImage'] : null;
				$pix_code = isset( $pix_data['payload'] ) ? $pix_data['payload'] : null;
			}
		}
		
		// Insert payment
		$wpdb->insert(
			$table,
			array(
				'subscription_id'      => $subscription_id,
				'user_id'             => $user_id,
				'asaas_payment_id'     => $payment_data['id'],
				'asaas_invoice_number' => isset( $payment_data['invoiceNumber'] ) ? $payment_data['invoiceNumber'] : null,
				'value'                => floatval( $payment_data['value'] ),
				'status'               => strtolower( $payment_data['status'] ),
				'due_date'             => $payment_data['dueDate'] ? date( 'Y-m-d', strtotime( $payment_data['dueDate'] ) ) : null,
				'payment_date'         => $payment_data['paymentDate'] ? date( 'Y-m-d H:i:s', strtotime( $payment_data['paymentDate'] ) ) : null,
				'pix_qr_code'          => $pix_qr_code,
				'pix_code'             => $pix_code,
			),
			array( '%d', '%d', '%s', '%s', '%f', '%s', '%s', '%s', '%s', '%s' )
		);
		
		return $wpdb->insert_id;
	}
	
	/**
	 * Update payment status
	 *
	 * @param string $asaas_payment_id Asaas payment ID.
	 * @param string $status New status.
	 * @param string $payment_date Payment date (optional).
	 * @return bool
	 */
	public static function update_status( $asaas_payment_id, $status, $payment_date = null ) {
		global $wpdb;
		$table = $wpdb->prefix . 'vetraiz_subscription_payments';
		
		$update_data = array(
			'status'     => strtolower( $status ),
			'updated_at' => current_time( 'mysql' ),
		);
		
		// Set payment date when payment is received
		if ( 'received' === strtolower( $status ) ) {
			$update_data['payment_date'] = $payment_date ? $payment_date : current_time( 'mysql' );
		}
		
		$result = $wpdb->update(
			$table,
			$update_data,
			array( 'asaas_payment_id' => $asaas_payment_id ),
			array( '%s', '%s', '%s' ),
			array( '%s' )
		);
		
		// Update subscription status if payment is received
		if ( 'received' === strtolower( $status ) ) {
			$payment = $wpdb->get_row( $wpdb->prepare(
				"SELECT subscription_id, user_id FROM $table WHERE asaas_payment_id = %s",
				$asaas_payment_id
			) );
			
			if ( $payment ) {
				$subscription_table = $wpdb->prefix . 'vetraiz_subscriptions';
				$update_result = $wpdb->update(
					$subscription_table,
					array( 
						'status' => 'active',
						'updated_at' => current_time( 'mysql' ),
					),
					array( 'id' => $payment->subscription_id ),
					array( '%s', '%s' ),
					array( '%d' )
				);
				
				// Log update
				if ( defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
					error_log( 'VETRAIZ: Subscription #' . $payment->subscription_id . ' updated to active for user #' . $payment->user_id );
				}
				
				// Clear any caches
				wp_cache_delete( 'vetraiz_subscription_user_' . $payment->user_id, 'vetraiz_subscriptions' );
			}
		}
		
		return false !== $result;
	}
	
	/**
	 * Get user payments
	 *
	 * @param int $user_id User ID.
	 * @return array
	 */
	public static function get_user_payments( $user_id ) {
		return Vetraiz_Subscriptions_Database::get_payments_by_user( $user_id );
	}
}

