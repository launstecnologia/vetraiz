<?php
/**
 * My invoices template
 *
 * @package Vetraiz_Subscriptions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$user_id = get_current_user_id();
$payments = Vetraiz_Subscriptions_Payment::get_user_payments( $user_id );
?>

<div class="vetraiz-my-invoices">
	<h2>Minhas Faturas</h2>
	
	<?php if ( empty( $payments ) ) : ?>
		<p>Você ainda não possui faturas.</p>
	<?php else : ?>
		<table class="vetraiz-invoices-table">
			<thead>
				<tr>
					<th>Fatura</th>
					<th>Valor</th>
					<th>Vencimento</th>
					<th>Status</th>
					<th>Ações</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $payments as $payment ) : ?>
					<tr>
						<td>#<?php echo esc_html( $payment->id ); ?></td>
						<td>R$ <?php echo esc_html( number_format( $payment->value, 2, ',', '.' ) ); ?></td>
						<td><?php echo $payment->due_date ? esc_html( date_i18n( 'd/m/Y', strtotime( $payment->due_date ) ) ) : '-'; ?></td>
						<td>
							<span class="status status-<?php echo esc_attr( $payment->status ); ?>">
								<?php
								$status_labels = array(
									'pending' => 'Pendente',
									'received' => 'Pago',
									'overdue' => 'Vencido',
								);
								echo esc_html( isset( $status_labels[ $payment->status ] ) ? $status_labels[ $payment->status ] : ucfirst( $payment->status ) );
								?>
							</span>
						</td>
						<td>
							<a href="<?php echo esc_url( home_url( '/fatura/' . $payment->id ) ); ?>" class="button">Ver Detalhes</a>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php endif; ?>
</div>

