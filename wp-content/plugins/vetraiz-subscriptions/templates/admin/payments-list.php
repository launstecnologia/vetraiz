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
	
	<?php
	// Show success/error messages
	if ( isset( $_GET['message'] ) ) {
		if ( 'success' === $_GET['message'] ) {
			echo '<div class="notice notice-success is-dismissible"><p>Ação executada com sucesso!</p></div>';
		} elseif ( 'error' === $_GET['message'] && isset( $_GET['error_msg'] ) ) {
			echo '<div class="notice notice-error is-dismissible"><p>' . esc_html( urldecode( $_GET['error_msg'] ) ) . '</p></div>';
		}
	}
	?>
	
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
				<th style="width: 150px;">Ações</th>
			</tr>
		</thead>
		<tbody>
			<?php if ( empty( $payments ) ) : ?>
				<tr>
					<td colspan="10">Nenhum pagamento encontrado.</td>
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
									'refunded' => 'Reembolsado',
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
						<td>
							<div style="display: flex; gap: 5px; flex-wrap: wrap;">
								<?php if ( 'pending' === $payment->status && ! empty( $payment->asaas_payment_id ) ) : ?>
									<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="display: inline;">
										<?php wp_nonce_field( 'vetraiz_payment_action' ); ?>
										<input type="hidden" name="action" value="vetraiz_payment_action">
										<input type="hidden" name="action_type" value="confirm">
										<input type="hidden" name="payment_id" value="<?php echo esc_attr( $payment->id ); ?>">
										<?php if ( $subscription ) : ?>
											<input type="hidden" name="subscription_id" value="<?php echo esc_attr( $subscription->id ); ?>">
										<?php endif; ?>
										<button type="submit" class="button button-small" onclick="return confirm('Tem certeza que deseja confirmar este pagamento?');" style="background: #00a32a; color: #fff; border-color: #00a32a;">
											Confirmar
										</button>
									</form>
								<?php endif; ?>
								<?php if ( 'received' === $payment->status && ! empty( $payment->asaas_payment_id ) ) : ?>
									<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="display: inline;">
										<?php wp_nonce_field( 'vetraiz_payment_action' ); ?>
										<input type="hidden" name="action" value="vetraiz_payment_action">
										<input type="hidden" name="action_type" value="refund">
										<input type="hidden" name="payment_id" value="<?php echo esc_attr( $payment->id ); ?>">
										<?php if ( $subscription ) : ?>
											<input type="hidden" name="subscription_id" value="<?php echo esc_attr( $subscription->id ); ?>">
										<?php endif; ?>
										<button type="submit" class="button button-small" onclick="return confirm('Tem certeza que deseja reembolsar este pagamento?');" style="background: #d63638; color: #fff; border-color: #d63638;">
											Reembolsar
										</button>
									</form>
								<?php endif; ?>
							</div>
						</td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
	</table>
</div>

