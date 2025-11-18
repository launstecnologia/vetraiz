<?php
/**
 * Script para listar assinaturas que estão vencendo hoje
 * 
 * Acesse via: https://vetraiz.com.br/wp-content/plugins/vetraiz-subscriptions/list-assinaturas-vencendo-hoje.php
 */

// Carregar WordPress
require_once( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/wp-load.php' );

// Verificar se é admin
if ( ! current_user_can( 'manage_options' ) ) {
	die( 'Acesso negado. Você precisa ser administrador.' );
}

global $wpdb;

$today = current_time( 'Y-m-d' );
$table_subscriptions = $wpdb->prefix . 'vetraiz_subscriptions';
$table_payments = $wpdb->prefix . 'vetraiz_subscription_payments';

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Assinaturas Vencendo Hoje - <?php echo esc_html( date_i18n( 'd/m/Y' ) ); ?></title>
	<style>
		* {
			margin: 0;
			padding: 0;
			box-sizing: border-box;
		}
		body {
			font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
			background: #f0f0f1;
			padding: 20px;
			color: #1d2327;
		}
		.container {
			max-width: 1400px;
			margin: 0 auto;
			background: #fff;
			padding: 30px;
			border-radius: 8px;
			box-shadow: 0 1px 3px rgba(0,0,0,0.1);
		}
		h1 {
			font-size: 28px;
			margin-bottom: 10px;
			color: #1d2327;
		}
		.date-info {
			color: #646970;
			margin-bottom: 30px;
			font-size: 14px;
		}
		.stats {
			display: grid;
			grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
			gap: 20px;
			margin-bottom: 30px;
		}
		.stat-box {
			background: #f6f7f7;
			padding: 20px;
			border-radius: 6px;
			border-left: 4px solid #2271b1;
		}
		.stat-label {
			font-size: 12px;
			color: #646970;
			text-transform: uppercase;
			margin-bottom: 8px;
		}
		.stat-value {
			font-size: 32px;
			font-weight: 600;
			color: #1d2327;
		}
		table {
			width: 100%;
			border-collapse: collapse;
			margin-top: 20px;
			background: #fff;
		}
		thead {
			background: #f6f7f7;
		}
		th {
			text-align: left;
			padding: 12px;
			font-weight: 600;
			font-size: 13px;
			color: #1d2327;
			border-bottom: 2px solid #dcdcde;
		}
		td {
			padding: 12px;
			border-bottom: 1px solid #dcdcde;
			font-size: 14px;
		}
		tr:hover {
			background: #f6f7f7;
		}
		.status-badge {
			display: inline-block;
			padding: 4px 8px;
			border-radius: 3px;
			font-size: 12px;
			font-weight: 600;
			text-transform: uppercase;
		}
		.status-active {
			background: #00a32a;
			color: #fff;
		}
		.status-pending {
			background: #dba617;
			color: #fff;
		}
		.status-overdue {
			background: #d63638;
			color: #fff;
		}
		.no-results {
			text-align: center;
			padding: 40px;
			color: #646970;
		}
		.export-btn {
			display: inline-block;
			margin-bottom: 20px;
			padding: 10px 20px;
			background: #2271b1;
			color: #fff;
			text-decoration: none;
			border-radius: 4px;
			font-size: 14px;
		}
		.export-btn:hover {
			background: #135e96;
		}
	</style>
</head>
<body>
	<div class="container">
		<h1>📅 Assinaturas Vencendo Hoje</h1>
		<div class="date-info">
			Data: <strong><?php echo esc_html( date_i18n( 'd/m/Y' ) ); ?></strong> | 
			Total de registros encontrados abaixo
		</div>

		<?php
		// Query 1: Assinaturas com next_payment_date vencendo hoje
		$subscriptions_due_today = $wpdb->get_results( $wpdb->prepare( "
			SELECT 
				s.*,
				u.user_email,
				u.display_name,
				COUNT(p.id) as total_payments,
				SUM(CASE WHEN p.status = 'received' THEN 1 ELSE 0 END) as paid_payments
			FROM {$table_subscriptions} s
			LEFT JOIN {$wpdb->users} u ON s.user_id = u.ID
			LEFT JOIN {$table_payments} p ON s.id = p.subscription_id
			WHERE DATE(s.next_payment_date) = %s
				AND s.status IN ('active', 'pending')
			GROUP BY s.id
			ORDER BY s.next_payment_date ASC
		", $today ) );

		// Query 2: Pagamentos pendentes com due_date vencendo hoje
		$payments_due_today = $wpdb->get_results( $wpdb->prepare( "
			SELECT 
				p.*,
				s.plan_name,
				s.status as subscription_status,
				u.user_email,
				u.display_name
			FROM {$table_payments} p
			LEFT JOIN {$table_subscriptions} s ON p.subscription_id = s.id
			LEFT JOIN {$wpdb->users} u ON p.user_id = u.ID
			WHERE DATE(p.due_date) = %s
				AND p.status = 'pending'
			ORDER BY p.due_date ASC
		", $today ) );

		$total_subscriptions = count( $subscriptions_due_today );
		$total_payments = count( $payments_due_today );
		?>

		<div class="stats">
			<div class="stat-box">
				<div class="stat-label">Assinaturas Vencendo</div>
				<div class="stat-value"><?php echo esc_html( $total_subscriptions ); ?></div>
			</div>
			<div class="stat-box">
				<div class="stat-label">Pagamentos Pendentes</div>
				<div class="stat-value"><?php echo esc_html( $total_payments ); ?></div>
			</div>
		</div>

		<?php if ( $total_subscriptions > 0 || $total_payments > 0 ) : ?>

			<?php if ( $total_subscriptions > 0 ) : ?>
				<h2 style="margin-top: 30px; margin-bottom: 15px; font-size: 20px;">Assinaturas com Próximo Pagamento Hoje</h2>
				<table>
					<thead>
						<tr>
							<th>ID</th>
							<th>Usuário</th>
							<th>Email</th>
							<th>Plano</th>
							<th>Valor</th>
							<th>Método</th>
							<th>Status</th>
							<th>Próximo Pagamento</th>
							<th>Pagamentos</th>
							<th>ID Asaas</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $subscriptions_due_today as $sub ) : 
							$status_class = 'status-' . $sub->status;
							$payment_method_label = $sub->payment_method === 'CREDIT_CARD' ? 'Cartão' : 'PIX';
						?>
							<tr>
								<td><?php echo esc_html( $sub->id ); ?></td>
								<td><?php echo esc_html( $sub->display_name ?: 'N/A' ); ?></td>
								<td><?php echo esc_html( $sub->user_email ); ?></td>
								<td><?php echo esc_html( $sub->plan_name ); ?></td>
								<td>R$ <?php echo esc_html( number_format( $sub->plan_value, 2, ',', '.' ) ); ?></td>
								<td><?php echo esc_html( $payment_method_label ); ?></td>
								<td><span class="status-badge <?php echo esc_attr( $status_class ); ?>"><?php echo esc_html( $sub->status ); ?></span></td>
								<td><?php echo esc_html( date_i18n( 'd/m/Y H:i', strtotime( $sub->next_payment_date ) ) ); ?></td>
								<td><?php echo esc_html( $sub->paid_payments ); ?>/<?php echo esc_html( $sub->total_payments ); ?></td>
								<td><?php echo esc_html( $sub->asaas_subscription_id ?: 'N/A' ); ?></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>

			<?php if ( $total_payments > 0 ) : ?>
				<h2 style="margin-top: 40px; margin-bottom: 15px; font-size: 20px;">Pagamentos Pendentes Vencendo Hoje</h2>
				<table>
					<thead>
						<tr>
							<th>ID</th>
							<th>Usuário</th>
							<th>Email</th>
							<th>Plano</th>
							<th>Valor</th>
							<th>Status</th>
							<th>Vencimento</th>
							<th>Status Assinatura</th>
							<th>ID Asaas</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $payments_due_today as $payment ) : 
							$status_class = 'status-' . $payment->status;
						?>
							<tr>
								<td><?php echo esc_html( $payment->id ); ?></td>
								<td><?php echo esc_html( $payment->display_name ?: 'N/A' ); ?></td>
								<td><?php echo esc_html( $payment->user_email ); ?></td>
								<td><?php echo esc_html( $payment->plan_name ?: 'N/A' ); ?></td>
								<td>R$ <?php echo esc_html( number_format( $payment->value, 2, ',', '.' ) ); ?></td>
								<td><span class="status-badge status-overdue"><?php echo esc_html( $payment->status ); ?></span></td>
								<td><?php echo esc_html( date_i18n( 'd/m/Y', strtotime( $payment->due_date ) ) ); ?></td>
								<td><span class="status-badge status-<?php echo esc_attr( $payment->subscription_status ); ?>"><?php echo esc_html( $payment->subscription_status ?: 'N/A' ); ?></span></td>
								<td><?php echo esc_html( $payment->asaas_payment_id ); ?></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>

		<?php else : ?>
			<div class="no-results">
				<p style="font-size: 18px; margin-bottom: 10px;">✅ Nenhuma assinatura ou pagamento vencendo hoje!</p>
				<p style="color: #646970;">Todas as assinaturas estão em dia.</p>
			</div>
		<?php endif; ?>

		<div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #dcdcde; color: #646970; font-size: 12px;">
			<p><strong>Query SQL utilizada:</strong></p>
			<pre style="background: #f6f7f7; padding: 15px; border-radius: 4px; overflow-x: auto; margin-top: 10px; font-size: 11px;"><?php 
				echo esc_html( "
-- Assinaturas com próximo pagamento vencendo hoje:
SELECT 
	s.*,
	u.user_email,
	u.display_name,
	COUNT(p.id) as total_payments,
	SUM(CASE WHEN p.status = 'received' THEN 1 ELSE 0 END) as paid_payments
FROM {$table_subscriptions} s
LEFT JOIN {$wpdb->users} u ON s.user_id = u.ID
LEFT JOIN {$table_payments} p ON s.id = p.subscription_id
WHERE DATE(s.next_payment_date) = '{$today}'
	AND s.status IN ('active', 'pending')
GROUP BY s.id
ORDER BY s.next_payment_date ASC;

-- Pagamentos pendentes vencendo hoje:
SELECT 
	p.*,
	s.plan_name,
	s.status as subscription_status,
	u.user_email,
	u.display_name
FROM {$table_payments} p
LEFT JOIN {$table_subscriptions} s ON p.subscription_id = s.id
LEFT JOIN {$wpdb->users} u ON p.user_id = u.ID
WHERE DATE(p.due_date) = '{$today}'
	AND p.status = 'pending'
ORDER BY p.due_date ASC;
				" );
			?></pre>
		</div>
	</div>
</body>
</html>

