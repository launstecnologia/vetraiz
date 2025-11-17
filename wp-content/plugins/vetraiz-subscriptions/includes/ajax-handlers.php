<?php
/**
 * AJAX handlers
 *
 * @package Vetraiz_Subscriptions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Inline login
add_action( 'wp_ajax_vetraiz_inline_login', 'vetraiz_handle_inline_login' );
add_action( 'wp_ajax_nopriv_vetraiz_inline_login', 'vetraiz_handle_inline_login' );

// Create subscription
add_action( 'wp_ajax_vetraiz_create_subscription', 'vetraiz_handle_create_subscription' );
add_action( 'wp_ajax_nopriv_vetraiz_create_subscription', 'vetraiz_handle_create_subscription' );

// Check payment status
add_action( 'wp_ajax_vetraiz_check_payment_status', 'vetraiz_handle_check_payment_status' );
add_action( 'wp_ajax_nopriv_vetraiz_check_payment_status', 'vetraiz_handle_check_payment_status' );

/**
 * Handle inline login
 */
function vetraiz_handle_inline_login() {
	check_ajax_referer( 'vetraiz_login', 'nonce' );
	
	$email = isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';
	$password = isset( $_POST['password'] ) ? $_POST['password'] : '';
	
	if ( empty( $email ) || empty( $password ) ) {
		wp_send_json_error( array( 'message' => 'E-mail e senha são obrigatórios.' ) );
	}
	
	$user = wp_authenticate( $email, $password );
	
	if ( is_wp_error( $user ) ) {
		wp_send_json_error( array( 'message' => 'E-mail ou senha incorretos.' ) );
	}
	
	wp_set_current_user( $user->ID );
	wp_set_auth_cookie( $user->ID );
	
	wp_send_json_success( array( 'message' => 'Login realizado com sucesso!' ) );
}

/**
 * Handle create subscription
 */
function vetraiz_handle_create_subscription() {
	check_ajax_referer( 'vetraiz_subscribe', 'nonce' );
	
	// Get form data
	parse_str( $_POST['form_data'], $form_data );
	
	$user_id = null;
	$is_new_user = false;
	
	// Check if user is logged in
	if ( is_user_logged_in() ) {
		$user_id = get_current_user_id();
	} else {
		// Create new user
		$email = isset( $form_data['user_email'] ) ? sanitize_email( $form_data['user_email'] ) : '';
		$password = isset( $form_data['user_password'] ) ? $form_data['user_password'] : '';
		$password_confirm = isset( $form_data['user_password_confirm'] ) ? $form_data['user_password_confirm'] : '';
		
		if ( empty( $email ) || empty( $password ) ) {
			wp_send_json_error( array( 'message' => 'E-mail e senha são obrigatórios.' ) );
		}
		
		if ( $password !== $password_confirm ) {
			wp_send_json_error( array( 'message' => 'As senhas não coincidem.' ) );
		}
		
		if ( email_exists( $email ) ) {
			wp_send_json_error( array( 'message' => 'Este e-mail já está cadastrado. Faça login para continuar.' ) );
		}
		
		if ( strlen( $password ) < 6 ) {
			wp_send_json_error( array( 'message' => 'A senha deve ter no mínimo 6 caracteres.' ) );
		}
		
		$user_id = wp_create_user( $email, $password, $email );
		
		if ( is_wp_error( $user_id ) ) {
			wp_send_json_error( array( 'message' => $user_id->get_error_message() ) );
		}
		
		$is_new_user = true;
		
		// Auto login
		wp_set_current_user( $user_id );
		wp_set_auth_cookie( $user_id );
	}
	
	// Update user data
	if ( isset( $form_data['user_name'] ) ) {
		wp_update_user( array( 'ID' => $user_id, 'display_name' => sanitize_text_field( $form_data['user_name'] ) ) );
	}
	
	if ( isset( $form_data['user_phone'] ) ) {
		update_user_meta( $user_id, 'billing_phone', sanitize_text_field( $form_data['user_phone'] ) );
	}
	
	if ( isset( $form_data['user_cpf'] ) ) {
		update_user_meta( $user_id, 'billing_cpf', sanitize_text_field( $form_data['user_cpf'] ) );
	}
	
	if ( isset( $form_data['user_birthdate'] ) ) {
		update_user_meta( $user_id, 'billing_birthdate', sanitize_text_field( $form_data['user_birthdate'] ) );
	}
	
	if ( isset( $form_data['user_postcode'] ) ) {
		update_user_meta( $user_id, 'billing_postcode', sanitize_text_field( $form_data['user_postcode'] ) );
	}
	
	if ( isset( $form_data['user_address'] ) ) {
		update_user_meta( $user_id, 'billing_address_1', sanitize_text_field( $form_data['user_address'] ) );
	}
	
	if ( isset( $form_data['user_city'] ) ) {
		update_user_meta( $user_id, 'billing_city', sanitize_text_field( $form_data['user_city'] ) );
	}
	
	if ( isset( $form_data['user_state'] ) ) {
		update_user_meta( $user_id, 'billing_state', sanitize_text_field( $form_data['user_state'] ) );
	}
	
	// Get payment method
	$payment_method = isset( $form_data['payment_method'] ) ? sanitize_text_field( $form_data['payment_method'] ) : 'PIX';
	
	// Get card data if credit card
	$card_data = null;
	if ( 'CREDIT_CARD' === $payment_method ) {
		$card_data = array(
			'holderName' => isset( $form_data['card_holder_name'] ) ? sanitize_text_field( $form_data['card_holder_name'] ) : '',
			'number'     => isset( $form_data['card_number'] ) ? preg_replace( '/\s+/', '', sanitize_text_field( $form_data['card_number'] ) ) : '',
			'expiryMonth' => isset( $form_data['card_expiry'] ) ? substr( sanitize_text_field( $form_data['card_expiry'] ), 0, 2 ) : '',
			'expiryYear'  => '20' . ( isset( $form_data['card_expiry'] ) ? substr( sanitize_text_field( $form_data['card_expiry'] ), 3, 2 ) : '' ),
			'ccv'         => isset( $form_data['card_cvv'] ) ? sanitize_text_field( $form_data['card_cvv'] ) : '',
		);
		
		if ( empty( $card_data['holderName'] ) || empty( $card_data['number'] ) || empty( $card_data['expiryMonth'] ) || empty( $card_data['ccv'] ) ) {
			wp_send_json_error( array( 'message' => 'Preencha todos os dados do cartão.' ) );
		}
	}
	
	// Create subscription
	$plan_name = get_option( 'vetraiz_plan_name', 'Assinatura Mensal' );
	$plan_value = get_option( 'vetraiz_plan_value', '14.99' );
	
	// Log subscription creation attempt
	if ( defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
		error_log( 'VETRAIZ AJAX: Creating subscription for user ID: ' . $user_id . ' - Payment method: ' . $payment_method );
	}
	
	$subscription_id = Vetraiz_Subscriptions_Subscription::create( $user_id, array(
		'plan_name'     => $plan_name,
		'value'          => $plan_value,
		'payment_method' => $payment_method,
		'card_data'      => $card_data,
	) );
	
	if ( is_wp_error( $subscription_id ) ) {
		$error_message = $subscription_id->get_error_message();
		if ( defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
			error_log( 'VETRAIZ AJAX: Subscription creation failed - ' . $error_message );
		}
		wp_send_json_error( array( 'message' => $error_message ) );
	}
	
	// Log success
	if ( defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
		error_log( 'VETRAIZ AJAX: Subscription created successfully - ID: ' . $subscription_id );
	}
	
	// Get first payment
	$payments = Vetraiz_Subscriptions_Payment::get_user_payments( $user_id );
	$first_payment = ! empty( $payments ) ? $payments[0] : null;
	
	// Check if there's a redirect parameter
	$redirect_to = isset( $_POST['redirect_to'] ) ? esc_url_raw( wp_unslash( $_POST['redirect_to'] ) ) : '';
	
	// Determine redirect URL
	if ( 'CREDIT_CARD' === $payment_method ) {
		// Credit card - redirect to subscription page
		$redirect_url = $redirect_to ? $redirect_to : home_url( '/minha-assinatura' );
		$message = 'Assinatura criada com sucesso! O pagamento será processado automaticamente.';
	} elseif ( $first_payment ) {
		// PIX - redirect to invoice
		$redirect_url = $redirect_to ? $redirect_to : home_url( '/fatura/' . $first_payment->id );
		$message = 'Assinatura criada com sucesso! Redirecionando para a fatura...';
	} else {
		$redirect_url = $redirect_to ? $redirect_to : home_url( '/minha-assinatura' );
		$message = 'Assinatura criada com sucesso!';
	}
	
	wp_send_json_success( array(
		'message'  => $message,
		'redirect' => $redirect_url,
	) );
}

/**
 * Handle check payment status
 */
function vetraiz_handle_check_payment_status() {
	check_ajax_referer( 'vetraiz_check_payment', 'nonce' );
	
	$payment_id = isset( $_POST['payment_id'] ) ? intval( $_POST['payment_id'] ) : 0;
	
	if ( ! $payment_id ) {
		wp_send_json_error( array( 'message' => 'ID do pagamento não fornecido.' ) );
	}
	
	global $wpdb;
	$payments_table = $wpdb->prefix . 'vetraiz_subscription_payments';
	$payment = $wpdb->get_row( $wpdb->prepare(
		"SELECT id, status, payment_date FROM $payments_table WHERE id = %d",
		$payment_id
	) );
	
	if ( ! $payment ) {
		wp_send_json_error( array( 'message' => 'Pagamento não encontrado.' ) );
	}
	
	// Check if user owns this payment
	if ( ! is_user_logged_in() ) {
		wp_send_json_error( array( 'message' => 'Usuário não autenticado.' ) );
	}
	
	$user_id = get_current_user_id();
	$payment_owner = $wpdb->get_var( $wpdb->prepare(
		"SELECT user_id FROM $payments_table WHERE id = %d",
		$payment_id
	) );
	
	if ( intval( $payment_owner ) !== $user_id ) {
		wp_send_json_error( array( 'message' => 'Acesso negado.' ) );
	}
	
	wp_send_json_success( array(
		'status' => $payment->status,
		'payment_date' => $payment->payment_date,
	) );
}

