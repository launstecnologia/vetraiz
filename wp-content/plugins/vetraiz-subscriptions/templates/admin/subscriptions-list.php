<?php
/**
 * Subscriptions list admin page
 *
 * @package Vetraiz_Subscriptions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $wpdb;
$payments_table = $wpdb->prefix . 'vetraiz_subscription_payments';
?>

<style>
.vetraiz-subscriptions-admin {
	margin-top: 20px;
}
.vetraiz-subscriptions-admin .status {
	padding: 4px 8px;
	border-radius: 3px;
	font-size: 11px;
	font-weight: bold;
	text-transform: uppercase;
}
.vetraiz-subscriptions-admin .status-active {
	background: #00a32a;
	color: #fff;
}
.vetraiz-subscriptions-admin .status-pending {
	background: #f0b849;
	color: #fff;
}
.vetraiz-subscriptions-admin .status-cancelled {
	background: #d63638;
	color: #fff;
}
.vetraiz-subscriptions-admin .status-failed {
	background: #8a2424;
	color: #fff;
}
.vetraiz-subscriptions-admin .subscription-details {
	margin-top: 20px;
}
.vetraiz-subscriptions-admin .payment-method {
	display: inline-block;
	padding: 2px 6px;
	background: #f0f0f1;
	border-radius: 3px;
	font-size: 11px;
	text-transform: uppercase;
}
.vetraiz-subscriptions-admin .payment-method-pix {
	background: #00a32a;
	color: #fff;
}
.vetraiz-subscriptions-admin .payment-method-credit_card {
	background: #2271b1;
	color: #fff;
}
.vetraiz-subscriptions-admin .view-payments {
	margin-left: 10px;
}
</style>

<div class="wrap vetraiz-subscriptions-admin">
	<h1>Todas as Assinaturas</h1>
	
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
	
	<?php if ( ! empty( $subscriptions ) ) : ?>
		<p class="description">Total de assinaturas: <strong><?php echo count( $subscriptions ); ?></strong></p>
	<?php endif; ?>
	
	<table class="wp-list-table widefat fixed striped">
		<thead>
			<tr>
				<th style="width: 60px;">ID</th>
				<th>Usuário / Email</th>
				<th>Plano</th>
				<th>Valor</th>
				<th>Método</th>
				<th>Status</th>
				<th>Pagamentos</th>
				<th>Data de Criação</th>
				<th>Próximo Pagamento</th>
				<th style="width: 100px;">Ações</th>
			</tr>
		</thead>
		<tbody>
			<?php if ( empty( $subscriptions ) ) : ?>
				<tr>
					<td colspan="10">Nenhuma assinatura encontrada.</td>
				</tr>
			<?php else : ?>
				<?php foreach ( $subscriptions as $subscription ) : ?>
					<?php 
					$user = get_userdata( $subscription->user_id );
					$payment_method = isset( $subscription->payment_method ) ? $subscription->payment_method : 'PIX';
					$payments = $wpdb->get_results( $wpdb->prepare(
						"SELECT * FROM $payments_table WHERE subscription_id = %d ORDER BY created_at DESC LIMIT 5",
						$subscription->id
					) );
					?>
					<tr>
						<td><?php echo esc_html( $subscription->id ); ?></td>
						<td>
							<?php if ( $user ) : ?>
								<strong>
									<a href="<?php echo esc_url( admin_url( 'user-edit.php?user_id=' . $subscription->user_id ) ); ?>">
										<?php echo esc_html( $user->display_name ); ?>
									</a>
								</strong><br>
								<small style="color: #666;"><?php echo esc_html( $user->user_email ); ?></small>
							<?php else : ?>
								Usuário #<?php echo esc_html( $subscription->user_id ); ?>
							<?php endif; ?>
						</td>
						<td><?php echo esc_html( $subscription->plan_name ); ?></td>
						<td><strong>R$ <?php echo esc_html( number_format( $subscription->plan_value, 2, ',', '.' ) ); ?></strong></td>
						<td>
							<span class="payment-method payment-method-<?php echo esc_attr( strtolower( str_replace( '_', '-', $payment_method ) ) ); ?>">
								<?php echo esc_html( 'CREDIT_CARD' === $payment_method ? 'Cartão' : 'PIX' ); ?>
							</span>
						</td>
						<td>
							<span class="status status-<?php echo esc_attr( $subscription->status ); ?>">
								<?php 
								$status_labels = array(
									'pending' => 'Pendente',
									'active' => 'Ativa',
									'cancelled' => 'Cancelada',
									'failed' => 'Falhou',
								);
								echo esc_html( isset( $status_labels[ $subscription->status ] ) ? $status_labels[ $subscription->status ] : ucfirst( $subscription->status ) ); 
								?>
							</span>
						</td>
						<td>
							<?php if ( isset( $subscription->payment_count ) && $subscription->payment_count > 0 ) : ?>
								<strong><?php echo esc_html( $subscription->received_count ); ?></strong> pago(s) de <strong><?php echo esc_html( $subscription->payment_count ); ?></strong>
							<?php else : ?>
								-
							<?php endif; ?>
						</td>
						<td><?php echo esc_html( date_i18n( 'd/m/Y H:i', strtotime( $subscription->created_at ) ) ); ?></td>
						<td>
							<?php echo $subscription->next_payment_date ? esc_html( date_i18n( 'd/m/Y', strtotime( $subscription->next_payment_date ) ) ) : '-'; ?>
						</td>
						<td>
							<div style="display: flex; gap: 5px; flex-wrap: wrap;">
								<a href="<?php echo esc_url( admin_url( 'admin.php?page=vetraiz-subscriptions-payments&subscription_id=' . $subscription->id ) ); ?>" class="button button-small">
									Ver Pagamentos
								</a>
								<?php if ( 'active' === $subscription->status || 'pending' === $subscription->status ) : ?>
									<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="display: inline;">
										<?php wp_nonce_field( 'vetraiz_subscription_action' ); ?>
										<input type="hidden" name="action" value="vetraiz_subscription_action">
										<input type="hidden" name="action_type" value="cancel">
										<input type="hidden" name="subscription_id" value="<?php echo esc_attr( $subscription->id ); ?>">
										<button type="submit" class="button button-small" onclick="return confirm('Tem certeza que deseja cancelar esta assinatura?');" style="background: #d63638; color: #fff; border-color: #d63638;">
											Cancelar
										</button>
									</form>
								<?php endif; ?>
								<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="display: inline;">
									<?php wp_nonce_field( 'vetraiz_subscription_action' ); ?>
									<input type="hidden" name="action" value="vetraiz_subscription_action">
									<input type="hidden" name="action_type" value="delete">
									<input type="hidden" name="subscription_id" value="<?php echo esc_attr( $subscription->id ); ?>">
									<button type="submit" class="button button-small" onclick="return confirm('Tem certeza que deseja EXCLUIR esta assinatura? Esta ação não pode ser desfeita!');" style="background: #8a2424; color: #fff; border-color: #8a2424;">
										Excluir
									</button>
								</form>
							</div>
						</td>
					</tr>
					<?php if ( ! empty( $payments ) ) : ?>
						<tr class="subscription-details-row" style="display: none;">
							<td colspan="10" class="subscription-details">
								<h4>Últimos Pagamentos:</h4>
								<table class="wp-list-table widefat" style="margin-top: 10px;">
									<thead>
										<tr>
											<th>ID</th>
											<th>Valor</th>
											<th>Status</th>
											<th>Vencimento</th>
											<th>Pagamento</th>
										</tr>
									</thead>
									<tbody>
										<?php foreach ( $payments as $payment ) : ?>
											<tr>
												<td>#<?php echo esc_html( $payment->id ); ?></td>
												<td>R$ <?php echo esc_html( number_format( $payment->value, 2, ',', '.' ) ); ?></td>
												<td>
													<span class="status status-<?php echo esc_attr( $payment->status ); ?>">
														<?php echo esc_html( ucfirst( $payment->status ) ); ?>
													</span>
												</td>
												<td><?php echo $payment->due_date ? esc_html( date_i18n( 'd/m/Y', strtotime( $payment->due_date ) ) ) : '-'; ?></td>
												<td><?php echo $payment->payment_date ? esc_html( date_i18n( 'd/m/Y H:i', strtotime( $payment->payment_date ) ) ) : '-'; ?></td>
											</tr>
										<?php endforeach; ?>
									</tbody>
								</table>
							</td>
						</tr>
					<?php endif; ?>
				<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
	</table>
</div>

