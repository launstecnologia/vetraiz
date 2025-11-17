<?php
/**
 * Subscribe form template
 *
 * @package Vetraiz_Subscriptions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$plan_name = get_option( 'vetraiz_plan_name', 'Assinatura Mensal' );
$plan_value = get_option( 'vetraiz_plan_value', '14.99' );
$user = wp_get_current_user();
?>

<div class="vetraiz-subscribe-form">
	<h2>Assinar <?php echo esc_html( $plan_name ); ?></h2>
	
	<form id="vetraiz-subscribe-form" method="post" action="">
		<?php wp_nonce_field( 'vetraiz_subscribe', 'vetraiz_subscribe_nonce' ); ?>
		
		<div class="form-group">
			<label for="user_name">Nome Completo *</label>
			<input type="text" id="user_name" name="user_name" value="<?php echo esc_attr( $user->display_name ); ?>" required>
		</div>
		
		<div class="form-group">
			<label for="user_email">E-mail *</label>
			<input type="email" id="user_email" name="user_email" value="<?php echo esc_attr( $user->user_email ); ?>" required>
		</div>
		
		<div class="form-group">
			<label for="user_phone">Telefone *</label>
			<input type="text" id="user_phone" name="user_phone" value="<?php echo esc_attr( get_user_meta( $user->ID, 'billing_phone', true ) ); ?>" required>
		</div>
		
		<div class="form-group">
			<label for="user_cpf">CPF *</label>
			<input type="text" id="user_cpf" name="user_cpf" value="<?php echo esc_attr( get_user_meta( $user->ID, 'billing_cpf', true ) ); ?>" required>
		</div>
		
		<div class="form-group">
			<label for="user_birthdate">Data de Nascimento *</label>
			<input type="date" id="user_birthdate" name="user_birthdate" required>
		</div>
		
		<div class="form-group">
			<label for="user_postcode">CEP *</label>
			<input type="text" id="user_postcode" name="user_postcode" value="<?php echo esc_attr( get_user_meta( $user->ID, 'billing_postcode', true ) ); ?>" required>
		</div>
		
		<div class="form-group">
			<label for="user_address">Endereço *</label>
			<input type="text" id="user_address" name="user_address" value="<?php echo esc_attr( get_user_meta( $user->ID, 'billing_address_1', true ) ); ?>" required>
		</div>
		
		<div class="form-group">
			<label for="user_city">Cidade *</label>
			<input type="text" id="user_city" name="user_city" value="<?php echo esc_attr( get_user_meta( $user->ID, 'billing_city', true ) ); ?>" required>
		</div>
		
		<div class="form-group">
			<label for="user_state">Estado *</label>
			<select id="user_state" name="user_state" required>
				<option value="">Selecione</option>
				<?php
				$states = array( 'AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO' );
				foreach ( $states as $state ) {
					$selected = selected( get_user_meta( $user->ID, 'billing_state', true ), $state, false );
					echo '<option value="' . esc_attr( $state ) . '" ' . $selected . '>' . esc_html( $state ) . '</option>';
				}
				?>
			</select>
		</div>
		
		<div class="plan-summary">
			<h3>Resumo da Assinatura</h3>
			<p><strong>Plano:</strong> <?php echo esc_html( $plan_name ); ?></p>
			<p><strong>Valor:</strong> R$ <?php echo esc_html( number_format( $plan_value, 2, ',', '.' ) ); ?> / mês</p>
		</div>
		
		<div class="form-actions">
			<button type="submit" class="button button-primary">Assinar Agora</button>
		</div>
		
		<div id="vetraiz-subscribe-message" style="display: none; margin-top: 20px;"></div>
	</form>
</div>

<script>
jQuery(document).ready(function($) {
	$('#vetraiz-subscribe-form').on('submit', function(e) {
		e.preventDefault();
		
		var form = $(this);
		var button = form.find('button[type="submit"]');
		var message = $('#vetraiz-subscribe-message');
		
		button.prop('disabled', true).text('Processando...');
		message.hide();
		
		$.ajax({
			url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
			type: 'POST',
			data: {
				action: 'vetraiz_create_subscription',
				nonce: '<?php echo wp_create_nonce( 'vetraiz_subscribe' ); ?>',
				form_data: form.serialize()
			},
			success: function(response) {
				if (response.success) {
					message.removeClass('error').addClass('success').html(response.data.message).show();
					if (response.data.redirect) {
						setTimeout(function() {
							window.location.href = response.data.redirect;
						}, 2000);
					}
				} else {
					message.removeClass('success').addClass('error').html(response.data.message).show();
					button.prop('disabled', false).text('Assinar Agora');
				}
			},
			error: function() {
				message.removeClass('success').addClass('error').html('Erro ao processar. Tente novamente.').show();
				button.prop('disabled', false).text('Assinar Agora');
			}
		});
	});
});
</script>

