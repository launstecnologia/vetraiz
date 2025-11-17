<?php
/**
 * Script de debug para verificar status de assinatura
 * 
 * Uso: Acesse via URL: /wp-content/plugins/vetraiz-subscriptions/debug-subscription.php?user_id=637
 */

// Load WordPress
require_once( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/wp-load.php' );

if ( ! current_user_can( 'manage_options' ) ) {
	die( 'Acesso negado. Você precisa ser administrador.' );
}

$user_id = isset( $_GET['user_id'] ) ? intval( $_GET['user_id'] ) : get_current_user_id();

echo "<h1>Debug de Assinatura - User ID: {$user_id}</h1>";
echo "<hr>";

// Get user info
$user = get_userdata( $user_id );
if ( ! $user ) {
	die( '<p style="color: red;">Usuário não encontrado!</p>' );
}

echo "<h2>Informações do Usuário:</h2>";
echo "<p><strong>Nome:</strong> {$user->display_name}</p>";
echo "<p><strong>Email:</strong> {$user->user_email}</p>";
echo "<p><strong>ID:</strong> {$user_id}</p>";
echo "<hr>";

// Get subscription
global $wpdb;
$subscription_table = $wpdb->prefix . 'vetraiz_subscriptions';
$subscription = $wpdb->get_row( $wpdb->prepare(
	"SELECT * FROM $subscription_table WHERE user_id = %d ORDER BY created_at DESC LIMIT 1",
	$user_id
) );

if ( ! $subscription ) {
	echo "<p style='color: red;'><strong>✗ Nenhuma assinatura encontrada!</strong></p>";
} else {
	echo "<h2>Assinatura:</h2>";
	echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
	echo "<tr><th>Campo</th><th>Valor</th></tr>";
	echo "<tr><td>ID</td><td>{$subscription->id}</td></tr>";
	echo "<tr><td>Asaas Subscription ID</td><td>{$subscription->asaas_subscription_id}</td></tr>";
	echo "<tr><td>Plano</td><td>{$subscription->plan_name}</td></tr>";
	echo "<tr><td>Valor</td><td>R$ " . number_format( $subscription->plan_value, 2, ',', '.' ) . "</td></tr>";
	echo "<tr><td>Método de Pagamento</td><td>" . ( isset( $subscription->payment_method ) ? $subscription->payment_method : 'N/A' ) . "</td></tr>";
	echo "<tr><td><strong>Status</strong></td><td><strong style='color: " . ( 'active' === $subscription->status ? 'green' : 'red' ) . ";'>{$subscription->status}</strong></td></tr>";
	echo "<tr><td>Data de Criação</td><td>{$subscription->created_at}</td></tr>";
	echo "<tr><td>Próximo Pagamento</td><td>" . ( $subscription->next_payment_date ? $subscription->next_payment_date : 'N/A' ) . "</td></tr>";
	echo "</table>";
}

// Get payments
$payments_table = $wpdb->prefix . 'vetraiz_subscription_payments';
$payments = $wpdb->get_results( $wpdb->prepare(
	"SELECT * FROM $payments_table WHERE user_id = %d ORDER BY created_at DESC",
	$user_id
) );

echo "<hr>";
echo "<h2>Pagamentos (" . count( $payments ) . "):</h2>";

if ( empty( $payments ) ) {
	echo "<p style='color: orange;'>Nenhum pagamento encontrado.</p>";
} else {
	echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>";
	echo "<tr>";
	echo "<th>ID</th>";
	echo "<th>Asaas Payment ID</th>";
	echo "<th>Valor</th>";
	echo "<th>Status</th>";
	echo "<th>Vencimento</th>";
	echo "<th>Data de Pagamento</th>";
	echo "<th>Assinatura ID</th>";
	echo "</tr>";
	
	foreach ( $payments as $payment ) {
		$status_color = 'received' === $payment->status ? 'green' : ( 'pending' === $payment->status ? 'orange' : 'red' );
		echo "<tr>";
		echo "<td>{$payment->id}</td>";
		echo "<td><code>{$payment->asaas_payment_id}</code></td>";
		echo "<td>R$ " . number_format( $payment->value, 2, ',', '.' ) . "</td>";
		echo "<td style='color: {$status_color};'><strong>{$payment->status}</strong></td>";
		echo "<td>" . ( $payment->due_date ? $payment->due_date : 'N/A' ) . "</td>";
		echo "<td>" . ( $payment->payment_date ? $payment->payment_date : 'N/A' ) . "</td>";
		echo "<td>{$payment->subscription_id}</td>";
		echo "</tr>";
	}
	
	echo "</table>";
}

// Check access
echo "<hr>";
echo "<h2>Verificação de Acesso:</h2>";

$has_access = Vetraiz_Subscriptions_Subscription::user_has_active_subscription( $user_id );

if ( $has_access ) {
	echo "<p style='color: green; font-size: 18px;'><strong>✓ USUÁRIO TEM ACESSO</strong></p>";
} else {
	echo "<p style='color: red; font-size: 18px;'><strong>✗ USUÁRIO NÃO TEM ACESSO</strong></p>";
}

// Check if subscription has received payment
if ( $subscription ) {
	$received_count = $wpdb->get_var( $wpdb->prepare(
		"SELECT COUNT(*) FROM $payments_table WHERE subscription_id = %d AND status = 'received'",
		$subscription->id
	) );
	
	echo "<p><strong>Pagamentos recebidos para esta assinatura:</strong> {$received_count}</p>";
	
	if ( $received_count > 0 && 'pending' === $subscription->status ) {
		echo "<p style='color: orange;'><strong>⚠ ATENÇÃO: Assinatura tem pagamento recebido mas status ainda está como 'pending'!</strong></p>";
		echo "<p><a href='?user_id={$user_id}&fix=1' style='background: #0073aa; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;'>Corrigir Status da Assinatura</a></p>";
	}
}

// Fix subscription status if requested
if ( isset( $_GET['fix'] ) && $_GET['fix'] == '1' && $subscription ) {
	$received_count = $wpdb->get_var( $wpdb->prepare(
		"SELECT COUNT(*) FROM $payments_table WHERE subscription_id = %d AND status = 'received'",
		$subscription->id
	) );
	
	if ( $received_count > 0 ) {
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
		
		wp_cache_delete( 'vetraiz_subscription_user_' . $user_id, 'vetraiz_subscriptions' );
		
		echo "<p style='color: green;'><strong>✓ Status da assinatura atualizado para 'active'!</strong></p>";
		echo "<script>setTimeout(function(){ location.reload(); }, 2000);</script>";
	}
}

