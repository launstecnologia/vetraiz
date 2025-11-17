<?php
/**
 * AJAX handlers
 *
 * @package Vetraiz_Subscriptions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Create subscription
add_action( 'wp_ajax_vetraiz_create_subscription', 'vetraiz_handle_create_subscription' );

function vetraiz_handle_create_subscription() {
	check_ajax_referer( 'vetraiz_subscribe', 'nonce' );
	
	if ( ! is_user_logged_in() ) {
		wp_send_json_error( array( 'message' => 'Você precisa estar logado.' ) );
	}
	
	$user_id = get_current_user_id();
	
	// Get form data
	parse_str( $_POST['form_data'], $form_data );
	
	// Update user meta
	if ( isset( $form_data['user_name'] ) ) {
		wp_update_user( array( 'ID' => $user_id, 'display_name' => sanitize_text_field( $form_data['user_name'] ) ) );
	}
	
	if ( isset( $form_data['user_phone'] ) ) {
		update_user_meta( $user_id, 'billing_phone', sanitize_text_field( $form_data['user_phone'] ) );
	}
	
	if ( isset( $form_data['user_cpf'] ) ) {
		update_user_meta( $user_id, 'billing_cpf', sanitize_text_field( $form_data['user_cpf'] ) );
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
	
	// Create subscription
	$plan_name = get_option( 'vetraiz_plan_name', 'Assinatura Mensal' );
	$plan_value = get_option( 'vetraiz_plan_value', '14.99' );
	
	$subscription_id = Vetraiz_Subscriptions_Subscription::create( $user_id, array(
		'plan_name' => $plan_name,
		'value'     => $plan_value,
	) );
	
	if ( is_wp_error( $subscription_id ) ) {
		wp_send_json_error( array( 'message' => $subscription_id->get_error_message() ) );
	}
	
	// Get first payment
	$payments = Vetraiz_Subscriptions_Payment::get_user_payments( $user_id );
	$first_payment = ! empty( $payments ) ? $payments[0] : null;
	
	if ( $first_payment ) {
		wp_send_json_success( array(
			'message'  => 'Assinatura criada com sucesso! Redirecionando para a fatura...',
			'redirect' => home_url( '/fatura/' . $first_payment->id ),
		) );
	} else {
		wp_send_json_success( array(
			'message'  => 'Assinatura criada com sucesso!',
			'redirect' => home_url( '/minha-assinatura' ),
		) );
	}
}

