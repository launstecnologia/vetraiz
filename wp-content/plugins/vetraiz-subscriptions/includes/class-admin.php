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
		add_action( 'admin_post_vetraiz_subscription_action', array( $this, 'handle_subscription_action' ) );
		add_action( 'admin_post_vetraiz_payment_action', array( $this, 'handle_payment_action' ) );
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
	
	/**
	 * Handle subscription actions
	 */
	public function handle_subscription_action() {
		// Check nonce
		if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'vetraiz_subscription_action' ) ) {
			wp_die( 'Ação não autorizada.' );
		}
		
		// Check permissions
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'Você não tem permissão para executar esta ação.' );
		}
		
		$action = isset( $_POST['action_type'] ) ? sanitize_text_field( $_POST['action_type'] ) : '';
		$subscription_id = isset( $_POST['subscription_id'] ) ? intval( $_POST['subscription_id'] ) : 0;
		
		if ( ! $subscription_id ) {
			wp_die( 'ID da assinatura não fornecido.' );
		}
		
		global $wpdb;
		$table = $wpdb->prefix . 'vetraiz_subscriptions';
		$subscription = $wpdb->get_row( $wpdb->prepare(
			"SELECT * FROM $table WHERE id = %d",
			$subscription_id
		) );
		
		if ( ! $subscription ) {
			wp_die( 'Assinatura não encontrada.' );
		}
		
		$api = new Vetraiz_Subscriptions_Asaas_API();
		$success = false;
		$message = '';
		
		switch ( $action ) {
			case 'delete':
				if ( ! empty( $subscription->asaas_subscription_id ) ) {
					$response = $api->delete_subscription( $subscription->asaas_subscription_id );
					if ( ! is_wp_error( $response ) && in_array( $response['code'], array( 200, 204 ), true ) ) {
						$wpdb->delete( $table, array( 'id' => $subscription_id ), array( '%d' ) );
						$success = true;
						$message = 'Assinatura excluída com sucesso no Asaas e no sistema.';
					} else {
						$error_message = is_wp_error( $response ) ? $response->get_error_message() : 'Erro ao excluir no Asaas.';
						$message = 'Erro ao excluir assinatura: ' . $error_message;
					}
				} else {
					// Delete local only if no Asaas ID
					$wpdb->delete( $table, array( 'id' => $subscription_id ), array( '%d' ) );
					$success = true;
					$message = 'Assinatura excluída localmente (não havia ID do Asaas).';
				}
				break;
				
			case 'cancel':
				if ( ! empty( $subscription->asaas_subscription_id ) ) {
					$response = $api->cancel_subscription( $subscription->asaas_subscription_id );
					if ( ! is_wp_error( $response ) && in_array( $response['code'], array( 200, 204 ), true ) ) {
						$wpdb->update(
							$table,
							array( 'status' => 'cancelled', 'updated_at' => current_time( 'mysql' ) ),
							array( 'id' => $subscription_id ),
							array( '%s', '%s' ),
							array( '%d' )
						);
						$success = true;
						$message = 'Assinatura cancelada com sucesso no Asaas e no sistema.';
					} else {
						$error_message = is_wp_error( $response ) ? $response->get_error_message() : 'Erro ao cancelar no Asaas.';
						$message = 'Erro ao cancelar assinatura: ' . $error_message;
					}
				} else {
					// Cancel local only
					$wpdb->update(
						$table,
						array( 'status' => 'cancelled', 'updated_at' => current_time( 'mysql' ) ),
						array( 'id' => $subscription_id ),
						array( '%s', '%s' ),
						array( '%d' )
					);
					$success = true;
					$message = 'Assinatura cancelada localmente (não havia ID do Asaas).';
				}
				break;
		}
		
		$redirect_url = admin_url( 'admin.php?page=vetraiz-subscriptions-list' );
		if ( $success ) {
			$redirect_url = add_query_arg( 'message', 'success', $redirect_url );
		} else {
			$redirect_url = add_query_arg( array(
				'message' => 'error',
				'error_msg' => urlencode( $message ),
			), $redirect_url );
		}
		
		wp_safe_redirect( $redirect_url );
		exit;
	}
	
	/**
	 * Handle payment actions
	 */
	public function handle_payment_action() {
		// Check nonce
		if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'vetraiz_payment_action' ) ) {
			wp_die( 'Ação não autorizada.' );
		}
		
		// Check permissions
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'Você não tem permissão para executar esta ação.' );
		}
		
		$action = isset( $_POST['action_type'] ) ? sanitize_text_field( $_POST['action_type'] ) : '';
		$payment_id = isset( $_POST['payment_id'] ) ? intval( $_POST['payment_id'] ) : 0;
		
		if ( ! $payment_id ) {
			wp_die( 'ID do pagamento não fornecido.' );
		}
		
		global $wpdb;
		$payments_table = $wpdb->prefix . 'vetraiz_subscription_payments';
		$payment = $wpdb->get_row( $wpdb->prepare(
			"SELECT * FROM $payments_table WHERE id = %d",
			$payment_id
		) );
		
		if ( ! $payment ) {
			wp_die( 'Pagamento não encontrado.' );
		}
		
		$api = new Vetraiz_Subscriptions_Asaas_API();
		$success = false;
		$message = '';
		
		switch ( $action ) {
			case 'refund':
				if ( ! empty( $payment->asaas_payment_id ) ) {
					$response = $api->refund_payment( $payment->asaas_payment_id );
					if ( ! is_wp_error( $response ) && in_array( $response['code'], array( 200, 204 ), true ) ) {
						$wpdb->update(
							$payments_table,
							array( 'status' => 'refunded', 'updated_at' => current_time( 'mysql' ) ),
							array( 'id' => $payment_id ),
							array( '%s', '%s' ),
							array( '%d' )
						);
						$success = true;
						$message = 'Reembolso processado com sucesso no Asaas e no sistema.';
					} else {
						$error_message = is_wp_error( $response ) ? $response->get_error_message() : 'Erro ao processar reembolso no Asaas.';
						$message = 'Erro ao processar reembolso: ' . $error_message;
					}
				} else {
					$message = 'Pagamento não possui ID do Asaas para reembolso.';
				}
				break;
				
			case 'confirm':
				if ( ! empty( $payment->asaas_payment_id ) ) {
					$response = $api->confirm_payment( $payment->asaas_payment_id );
					if ( ! is_wp_error( $response ) && in_array( $response['code'], array( 200, 204 ), true ) ) {
						$wpdb->update(
							$payments_table,
							array( 
								'status' => 'received',
								'payment_date' => current_time( 'mysql' ),
								'updated_at' => current_time( 'mysql' ),
							),
							array( 'id' => $payment_id ),
							array( '%s', '%s', '%s' ),
							array( '%d' )
						);
						
						// Update subscription status
						$subscription_table = $wpdb->prefix . 'vetraiz_subscriptions';
						$wpdb->update(
							$subscription_table,
							array( 
								'status' => 'active',
								'updated_at' => current_time( 'mysql' ),
							),
							array( 'id' => $payment->subscription_id ),
							array( '%s', '%s' ),
							array( '%d' )
						);
						
						$success = true;
						$message = 'Pagamento confirmado com sucesso no Asaas e no sistema.';
					} else {
						$error_message = is_wp_error( $response ) ? $response->get_error_message() : 'Erro ao confirmar pagamento no Asaas.';
						$message = 'Erro ao confirmar pagamento: ' . $error_message;
					}
				} else {
					$message = 'Pagamento não possui ID do Asaas para confirmação.';
				}
				break;
		}
		
		$subscription_id_param = isset( $_POST['subscription_id'] ) ? intval( $_POST['subscription_id'] ) : 0;
		$redirect_url = admin_url( 'admin.php?page=vetraiz-subscriptions-payments' );
		if ( $subscription_id_param > 0 ) {
			$redirect_url = add_query_arg( 'subscription_id', $subscription_id_param, $redirect_url );
		}
		
		if ( $success ) {
			$redirect_url = add_query_arg( 'message', 'success', $redirect_url );
		} else {
			$redirect_url = add_query_arg( array(
				'message' => 'error',
				'error_msg' => urlencode( $message ),
			), $redirect_url );
		}
		
		wp_safe_redirect( $redirect_url );
		exit;
	}
}

