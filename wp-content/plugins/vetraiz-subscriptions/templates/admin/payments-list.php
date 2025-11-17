<?php
/**
 * Payments list admin page
 *
 * @package Vetraiz_Subscriptions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<style>
.vetraiz-payments-admin {
	margin-top: 20px;
}
.vetraiz-payments-admin .status {
	padding: 4px 8px;
	border-radius: 3px;
	font-size: 11px;
	font-weight: bold;
	text-transform: uppercase;
}
.vetraiz-payments-admin .status-received {
	background: #00a32a;
	color: #fff;
}
.vetraiz-payments-admin .status-pending {
	background: #f0b849;
	color: #fff;
}
.vetraiz-payments-admin .status-overdue {
	background: #d63638;
	color: #fff;
}
.vetraiz-payments-admin .status-cancelled {
	background: #8a2424;
	color: #fff;
}
</style>

<div class="wrap vetraiz-payments-admin">
	<h1>Pagamentos</h1>
	
	<?php if ( $subscription ) : ?>
		<?php $user = get_userdata( $subscription->user_id ); ?>
		<div class="notice notice-info">
			<p>
				<strong>Assinatura #<?php echo esc_html( $subscription->id ); ?></strong> - 
				<?php if ( $user ) : ?>
					Usuário: <strong><?php echo esc_html( $user->display_name ); ?></strong> (<?php echo esc_html( $user->user_email ); ?>)
				<?php else : ?>
					Usuário #<?php echo esc_html( $subscription->user_id ); ?>
				<?php endif; ?>
				- Plano: <strong><?php echo esc_html( $subscription->plan_name ); ?></strong>
			</p>
		</div>
		<p>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=vetraiz-subscriptions-payments' ) ); ?>" class="button">
				← Ver Todos os Pagamentos
			</a>
		</p>
	<?php endif; ?>
	
	<?php if ( ! empty( $payments ) ) : ?>
		<p class="description">Total de pagamentos: <strong><?php echo count( $payments ); ?></strong></p>
	<?php endif; ?>
	
	<table class="wp-list-table widefat fixed striped">
		<thead>
			<tr>
				<th style="width: 60px;">ID</th>
				<th>Usuário</th>
				<th>Assinatura</th>
				<th>Valor</th>
				<th>Status</th>
				<th>Vencimento</th>
				<th>Data de Pagamento</th>
				<th>Asaas Payment ID</th>
				<th>Data de Criação</th>
			</tr>
		</thead>
		<tbody>
			<?php if ( empty( $payments ) ) : ?>
				<tr>
					<td colspan="9">Nenhum pagamento encontrado.</td>
				</tr>
			<?php else : ?>
				<?php foreach ( $payments as $payment ) : ?>
					<?php $user = get_userdata( $payment->user_id ); ?>
					<tr>
						<td><?php echo esc_html( $payment->id ); ?></td>
						<td>
							<?php if ( $user ) : ?>
								<a href="<?php echo esc_url( admin_url( 'user-edit.php?user_id=' . $payment->user_id ) ); ?>">
									<?php echo esc_html( $user->display_name ); ?>
								</a><br>
								<small style="color: #666;"><?php echo esc_html( $user->user_email ); ?></small>
							<?php else : ?>
								Usuário #<?php echo esc_html( $payment->user_id ); ?>
							<?php endif; ?>
						</td>
						<td>
							<a href="<?php echo esc_url( admin_url( 'admin.php?page=vetraiz-subscriptions-payments&subscription_id=' . $payment->subscription_id ) ); ?>">
								Assinatura #<?php echo esc_html( $payment->subscription_id ); ?>
							</a><br>
							<small style="color: #666;"><?php echo esc_html( $payment->plan_name ); ?></small>
						</td>
						<td><strong>R$ <?php echo esc_html( number_format( $payment->value, 2, ',', '.' ) ); ?></strong></td>
						<td>
							<span class="status status-<?php echo esc_attr( $payment->status ); ?>">
								<?php 
								$status_labels = array(
									'received' => 'Recebido',
									'pending' => 'Pendente',
									'overdue' => 'Vencido',
									'cancelled' => 'Cancelado',
								);
								echo esc_html( isset( $status_labels[ $payment->status ] ) ? $status_labels[ $payment->status ] : ucfirst( $payment->status ) ); 
								?>
							</span>
						</td>
						<td>
							<?php echo $payment->due_date ? esc_html( date_i18n( 'd/m/Y', strtotime( $payment->due_date ) ) ) : '-'; ?>
						</td>
						<td>
							<?php echo $payment->payment_date ? esc_html( date_i18n( 'd/m/Y H:i', strtotime( $payment->payment_date ) ) ) : '-'; ?>
						</td>
						<td>
							<code style="font-size: 11px;"><?php echo esc_html( $payment->asaas_payment_id ); ?></code>
						</td>
						<td><?php echo esc_html( date_i18n( 'd/m/Y H:i', strtotime( $payment->created_at ) ) ); ?></td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
	</table>
</div>

