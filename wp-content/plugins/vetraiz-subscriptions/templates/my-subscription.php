<?php
/**
 * My subscription template
 *
 * @package Vetraiz_Subscriptions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$user_id = get_current_user_id();
$subscription = Vetraiz_Subscriptions_Subscription::get_user_subscription( $user_id );
?>

<div class="vetraiz-my-subscription">
	<h2>Minha Assinatura</h2>
	
	<?php if ( ! $subscription ) : ?>
		<p>Você ainda não possui uma assinatura.</p>
		<a href="<?php echo esc_url( get_permalink( get_option( 'vetraiz_subscribe_page_id' ) ) ); ?>" class="button">Assinar Agora</a>
	<?php else : ?>
		<div class="subscription-info">
			<div class="info-row">
				<strong>Plano:</strong> <?php echo esc_html( $subscription->plan_name ); ?>
			</div>
			<div class="info-row">
				<strong>Valor:</strong> R$ <?php echo esc_html( number_format( $subscription->plan_value, 2, ',', '.' ) ); ?> / mês
			</div>
			<div class="info-row">
				<strong>Status:</strong> 
				<span class="status status-<?php echo esc_attr( $subscription->status ); ?>">
					<?php
					$status_labels = array(
						'pending' => 'Pendente',
						'active' => 'Ativa',
						'cancelled' => 'Cancelada',
					);
					echo esc_html( isset( $status_labels[ $subscription->status ] ) ? $status_labels[ $subscription->status ] : ucfirst( $subscription->status ) );
					?>
				</span>
			</div>
			<?php if ( $subscription->next_payment_date ) : ?>
				<div class="info-row">
					<strong>Próximo Pagamento:</strong> <?php echo esc_html( date_i18n( 'd/m/Y', strtotime( $subscription->next_payment_date ) ) ); ?>
				</div>
			<?php endif; ?>
		</div>
		
		<div class="subscription-actions">
			<a href="<?php echo esc_url( home_url( '/minhas-faturas' ) ); ?>" class="button">Ver Faturas</a>
		</div>
	<?php endif; ?>
</div>

