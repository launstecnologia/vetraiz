<?php
/**
 * Database management
 *
 * @package Vetraiz_Subscriptions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Vetraiz_Subscriptions_Database {
	
	/**
	 * Instance
	 *
	 * @var Vetraiz_Subscriptions_Database
	 */
	private static $instance = null;
	
	/**
	 * Get instance
	 *
	 * @return Vetraiz_Subscriptions_Database
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	/**
	 * Create database tables
	 */
	public static function create_tables() {
		global $wpdb;
		
		$charset_collate = $wpdb->get_charset_collate();
		
		// Subscriptions table
		$table_subscriptions = $wpdb->prefix . 'vetraiz_subscriptions';
		$sql_subscriptions = "CREATE TABLE IF NOT EXISTS $table_subscriptions (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			user_id bigint(20) NOT NULL,
			asaas_subscription_id varchar(255) DEFAULT NULL,
			asaas_customer_id varchar(255) DEFAULT NULL,
			plan_name varchar(255) NOT NULL,
			plan_value decimal(10,2) NOT NULL,
			status varchar(50) NOT NULL DEFAULT 'pending',
			start_date datetime DEFAULT NULL,
			end_date datetime DEFAULT NULL,
			next_payment_date datetime DEFAULT NULL,
			created_at datetime DEFAULT CURRENT_TIMESTAMP,
			updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			KEY user_id (user_id),
			KEY asaas_subscription_id (asaas_subscription_id),
			KEY status (status)
		) $charset_collate;";
		
		// Payments/Invoices table
		$table_payments = $wpdb->prefix . 'vetraiz_subscription_payments';
		$sql_payments = "CREATE TABLE IF NOT EXISTS $table_payments (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			subscription_id bigint(20) NOT NULL,
			user_id bigint(20) NOT NULL,
			asaas_payment_id varchar(255) NOT NULL,
			asaas_invoice_number varchar(255) DEFAULT NULL,
			value decimal(10,2) NOT NULL,
			status varchar(50) NOT NULL DEFAULT 'pending',
			due_date date DEFAULT NULL,
			payment_date datetime DEFAULT NULL,
			pix_qr_code text DEFAULT NULL,
			pix_code text DEFAULT NULL,
			created_at datetime DEFAULT CURRENT_TIMESTAMP,
			updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			KEY subscription_id (subscription_id),
			KEY user_id (user_id),
			KEY asaas_payment_id (asaas_payment_id),
			KEY status (status)
		) $charset_collate;";
		
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql_subscriptions );
		dbDelta( $sql_payments );
	}
	
	/**
	 * Get subscription by user ID
	 *
	 * @param int $user_id User ID.
	 * @return object|null
	 */
	public static function get_subscription_by_user( $user_id ) {
		global $wpdb;
		$table = $wpdb->prefix . 'vetraiz_subscriptions';
		
		return $wpdb->get_row( $wpdb->prepare(
			"SELECT * FROM $table WHERE user_id = %d AND status IN ('active', 'pending') ORDER BY created_at DESC LIMIT 1",
			$user_id
		) );
	}
	
	/**
	 * Get payments by user ID
	 *
	 * @param int $user_id User ID.
	 * @return array
	 */
	public static function get_payments_by_user( $user_id ) {
		global $wpdb;
		$table = $wpdb->prefix . 'vetraiz_subscription_payments';
		
		return $wpdb->get_results( $wpdb->prepare(
			"SELECT * FROM $table WHERE user_id = %d ORDER BY created_at DESC",
			$user_id
		) );
	}
	
	/**
	 * Get payment by Asaas payment ID
	 *
	 * @param string $payment_id Asaas payment ID.
	 * @return object|null
	 */
	public static function get_payment_by_asaas_id( $payment_id ) {
		global $wpdb;
		$table = $wpdb->prefix . 'vetraiz_subscription_payments';
		
		return $wpdb->get_row( $wpdb->prepare(
			"SELECT * FROM $table WHERE asaas_payment_id = %s",
			$payment_id
		) );
	}
}

