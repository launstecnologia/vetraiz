<?php
/**
 * Subscriptions list admin page
 *
 * @package Vetraiz_Subscriptions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="wrap">
	<h1>Todas as Assinaturas</h1>
	
	<table class="wp-list-table widefat fixed striped">
		<thead>
			<tr>
				<th>ID</th>
				<th>Usuário</th>
				<th>Plano</th>
				<th>Valor</th>
				<th>Status</th>
				<th>Data de Criação</th>
				<th>Próximo Pagamento</th>
			</tr>
		</thead>
		<tbody>
			<?php if ( empty( $subscriptions ) ) : ?>
				<tr>
					<td colspan="7">Nenhuma assinatura encontrada.</td>
				</tr>
			<?php else : ?>
				<?php foreach ( $subscriptions as $subscription ) : ?>
					<?php $user = get_userdata( $subscription->user_id ); ?>
					<tr>
						<td><?php echo esc_html( $subscription->id ); ?></td>
						<td>
							<?php if ( $user ) : ?>
								<a href="<?php echo esc_url( admin_url( 'user-edit.php?user_id=' . $subscription->user_id ) ); ?>">
									<?php echo esc_html( $user->display_name ); ?>
								</a>
							<?php else : ?>
								Usuário #<?php echo esc_html( $subscription->user_id ); ?>
							<?php endif; ?>
						</td>
						<td><?php echo esc_html( $subscription->plan_name ); ?></td>
						<td>R$ <?php echo esc_html( number_format( $subscription->plan_value, 2, ',', '.' ) ); ?></td>
						<td>
							<span class="status status-<?php echo esc_attr( $subscription->status ); ?>">
								<?php echo esc_html( ucfirst( $subscription->status ) ); ?>
							</span>
						</td>
						<td><?php echo esc_html( date_i18n( 'd/m/Y H:i', strtotime( $subscription->created_at ) ) ); ?></td>
						<td>
							<?php echo $subscription->next_payment_date ? esc_html( date_i18n( 'd/m/Y', strtotime( $subscription->next_payment_date ) ) ) : '-'; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
	</table>
</div>

