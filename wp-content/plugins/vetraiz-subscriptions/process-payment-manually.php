<?php
/**
 * Script para processar manualmente um pagamento recebido
 * 
 * Uso: Acesse via URL: /wp-content/plugins/vetraiz-subscriptions/process-payment-manually.php?payment_id=pay_xybrbgm9qgvzy4uq
 * 
 * OU execute via WP-CLI: wp eval-file wp-content/plugins/vetraiz-subscriptions/process-payment-manually.php
 */

// Load WordPress
require_once( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/wp-load.php' );

if ( ! current_user_can( 'manage_options' ) ) {
	die( 'Acesso negado. Você precisa ser administrador.' );
}

$payment_id = isset( $_GET['payment_id'] ) ? sanitize_text_field( $_GET['payment_id'] ) : 'pay_xybrbgm9qgvzy4uq';
$subscription_id = isset( $_GET['subscription_id'] ) ? sanitize_text_field( $_GET['subscription_id'] ) : 'sub_3r5r2n2l3x6qgjzg';
$user_id = isset( $_GET['user_id'] ) ? intval( $_GET['user_id'] ) : 637;

echo "<h1>Processar Pagamento Manualmente</h1>";
echo "<p>Payment ID: <strong>{$payment_id}</strong></p>";
echo "<p>Subscription ID: <strong>{$subscription_id}</strong></p>";
echo "<p>User ID: <strong>{$user_id}</strong></p>";
echo "<hr>";

// Buscar assinatura
global $wpdb;
$subscription_table = $wpdb->prefix . 'vetraiz_subscriptions';
$subscription = $wpdb->get_row( $wpdb->prepare(
	"SELECT * FROM $subscription_table WHERE asaas_subscription_id = %s OR user_id = %d ORDER BY created_at DESC LIMIT 1",
	$subscription_id,
	$user_id
) );

if ( ! $subscription ) {
	die( '<p style="color: red;">Assinatura não encontrada!</p>' );
}

echo "<p>Assinatura encontrada: ID #{$subscription->id} - User ID: {$subscription->user_id}</p>";

// Buscar pagamento
$payments_table = $wpdb->prefix . 'vetraiz_subscription_payments';
$payment = $wpdb->get_row( $wpdb->prepare(
	"SELECT * FROM $payments_table WHERE asaas_payment_id = %s",
	$payment_id
) );

if ( $payment ) {
	echo "<p>Pagamento encontrado: ID #{$payment->id} - Status: {$payment->status}</p>";
	
	// Atualizar status
	$result = Vetraiz_Subscriptions_Payment::update_status( $payment_id, 'received', '2025-11-17 15:20:43' );
	
	if ( $result ) {
		echo "<p style='color: green;'><strong>✓ Pagamento atualizado com sucesso!</strong></p>";
	} else {
		echo "<p style='color: red;'><strong>✗ Erro ao atualizar pagamento</strong></p>";
	}
} else {
	echo "<p>Pagamento não encontrado. Buscando dados do Asaas...</p>";
	
	// Buscar dados do pagamento no Asaas
	$api = new Vetraiz_Subscriptions_Asaas_API();
	$payment_response = $api->get_payment( $payment_id );
	
	if ( is_wp_error( $payment_response ) || 200 !== $payment_response['code'] ) {
		die( '<p style="color: red;">Erro ao buscar pagamento no Asaas: ' . ( is_wp_error( $payment_response ) ? $payment_response->get_error_message() : 'Código ' . $payment_response['code'] ) . '</p>' );
	}
	
	$payment_data = $payment_response['body'];
	
	echo "<p>Dados do pagamento encontrados no Asaas:</p>";
	echo "<pre>" . print_r( $payment_data, true ) . "</pre>";
	
	// Criar pagamento
	$created = Vetraiz_Subscriptions_Payment::create_from_asaas( $subscription->id, $subscription->user_id, $payment_data );
	
	if ( $created ) {
		echo "<p style='color: green;'><strong>✓ Pagamento criado com sucesso! ID: {$created}</strong></p>";
		
		// Atualizar status para received
		Vetraiz_Subscriptions_Payment::update_status( $payment_id, 'received', '2025-11-17 15:20:43' );
		
		echo "<p style='color: green;'><strong>✓ Status atualizado para 'received'</strong></p>";
	} else {
		echo "<p style='color: red;'><strong>✗ Erro ao criar pagamento</strong></p>";
	}
}

// Verificar status final
$payment = $wpdb->get_row( $wpdb->prepare(
	"SELECT * FROM $payments_table WHERE asaas_payment_id = %s",
	$payment_id
) );

if ( $payment ) {
	echo "<hr>";
	echo "<h2>Status Final:</h2>";
	echo "<p>Payment ID: <strong>{$payment->asaas_payment_id}</strong></p>";
	echo "<p>Status: <strong>{$payment->status}</strong></p>";
	echo "<p>Payment Date: <strong>" . ( $payment->payment_date ? $payment->payment_date : 'N/A' ) . "</strong></p>";
	echo "<p>Value: <strong>R$ " . number_format( $payment->value, 2, ',', '.' ) . "</strong></p>";
}

// Verificar assinatura
$subscription = $wpdb->get_row( $wpdb->prepare(
	"SELECT * FROM $subscription_table WHERE id = %d",
	$subscription->id
) );

echo "<hr>";
echo "<h2>Status da Assinatura:</h2>";
echo "<p>Status: <strong>{$subscription->status}</strong></p>";
echo "<p>User ID: <strong>{$subscription->user_id}</strong></p>";

