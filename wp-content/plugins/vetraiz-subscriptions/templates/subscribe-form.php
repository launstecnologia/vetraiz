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
$is_logged_in = is_user_logged_in();
$redirect_to = isset( $_GET['redirect_to'] ) ? esc_url_raw( wp_unslash( $_GET['redirect_to'] ) ) : '';
?>

<div class="vetraiz-subscribe-form">
	<h2>Assinar <?php echo esc_html( $plan_name ); ?></h2>
	
	<?php if ( $redirect_to ) : ?>
		<div class="vetraiz-alert vetraiz-alert-info">
			<p>Você precisa de uma assinatura ativa para acessar este conteúdo.</p>
		</div>
	<?php endif; ?>
	
	<?php if ( ! $is_logged_in ) : ?>
		<div class="vetraiz-login-section" style="margin-bottom: 30px; padding: 20px; background: #f9f9f9; border-radius: 8px;">
			<h3>Já tem uma conta? <a href="#" id="vetraiz-show-login" style="color: #0073aa;">Fazer Login</a></h3>
			<div id="vetraiz-login-form" style="display: none; margin-top: 15px;">
				<form id="vetraiz-login-form-inline">
					<div class="form-group">
						<label for="login_email">E-mail</label>
						<input type="email" id="login_email" name="login_email" required>
					</div>
					<div class="form-group">
						<label for="login_password">Senha</label>
						<input type="password" id="login_password" name="login_password" required>
					</div>
					<button type="submit" class="button">Entrar</button>
					<div id="vetraiz-login-message" style="margin-top: 10px;"></div>
				</form>
			</div>
		</div>
	<?php endif; ?>
	
	<form id="vetraiz-subscribe-form" method="post" action="">
		<?php wp_nonce_field( 'vetraiz_subscribe', 'vetraiz_subscribe_nonce' ); ?>
		<?php if ( $redirect_to ) : ?>
			<input type="hidden" name="redirect_to" value="<?php echo esc_attr( $redirect_to ); ?>">
		<?php endif; ?>
		
		<?php if ( ! $is_logged_in ) : ?>
			<h3>Dados de Acesso</h3>
			<div class="form-group">
				<label for="user_email">E-mail *</label>
				<input type="email" id="user_email" name="user_email" required>
			</div>
			
			<div class="form-group">
				<label for="user_password">Senha *</label>
				<input type="password" id="user_password" name="user_password" minlength="6" required>
				<small style="color: #666;">Mínimo 6 caracteres</small>
			</div>
			
			<div class="form-group">
				<label for="user_password_confirm">Confirmar Senha *</label>
				<input type="password" id="user_password_confirm" name="user_password_confirm" minlength="6" required>
			</div>
		<?php endif; ?>
		
		<h3>Dados Pessoais</h3>
		<div class="form-group">
			<label for="user_name">Nome Completo *</label>
			<input type="text" id="user_name" name="user_name" value="<?php echo esc_attr( $user->display_name ); ?>" required>
		</div>
		
		<?php if ( $is_logged_in ) : ?>
			<div class="form-group">
				<label for="user_email">E-mail *</label>
				<input type="email" id="user_email" name="user_email" value="<?php echo esc_attr( $user->user_email ); ?>" required>
			</div>
		<?php endif; ?>
		
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
		
		<h3>Forma de Pagamento</h3>
		<div class="form-group">
			<label>
				<input type="radio" name="payment_method" value="PIX" checked>
				<span style="margin-left: 8px;">PIX - Você receberá uma fatura mensal para pagar</span>
			</label>
		</div>
		<div class="form-group">
			<label>
				<input type="radio" name="payment_method" value="CREDIT_CARD">
				<span style="margin-left: 8px;">Cartão de Crédito - Débito automático mensal</span>
			</label>
		</div>
		
		<div id="vetraiz-card-fields" style="display: none; margin-top: 20px; padding: 20px; background: #f9f9f9; border-radius: 8px;">
			<h4>Dados do Cartão</h4>
			<div class="form-group">
				<label for="card_holder_name">Nome no Cartão *</label>
				<input type="text" id="card_holder_name" name="card_holder_name" placeholder="Nome como está no cartão">
			</div>
			
			<div class="form-group">
				<label for="card_number">Número do Cartão *</label>
				<input type="text" id="card_number" name="card_number" maxlength="19" placeholder="0000 0000 0000 0000">
			</div>
			
			<div class="form-row" style="display: flex; gap: 15px;">
				<div class="form-group" style="flex: 1;">
					<label for="card_expiry">Validade (MM/AA) *</label>
					<input type="text" id="card_expiry" name="card_expiry" maxlength="5" placeholder="MM/AA">
				</div>
				<div class="form-group" style="flex: 1;">
					<label for="card_cvv">CVV *</label>
					<input type="text" id="card_cvv" name="card_cvv" maxlength="4" placeholder="123">
				</div>
			</div>
		</div>
		
		<div class="form-actions">
			<button type="submit" class="button button-primary">Assinar Agora</button>
		</div>
		
		<div id="vetraiz-subscribe-message" style="display: none; margin-top: 20px;"></div>
	</form>
</div>

<script>
jQuery(document).ready(function($) {
	// Show/hide login form
	$('#vetraiz-show-login').on('click', function(e) {
		e.preventDefault();
		$('#vetraiz-login-form').slideToggle();
	});
	
	// Handle inline login
	$('#vetraiz-login-form-inline').on('submit', function(e) {
		e.preventDefault();
		var form = $(this);
		var message = $('#vetraiz-login-message');
		
		$.ajax({
			url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
			type: 'POST',
			data: {
				action: 'vetraiz_inline_login',
				nonce: '<?php echo wp_create_nonce( 'vetraiz_login' ); ?>',
				email: $('#login_email').val(),
				password: $('#login_password').val(),
				redirect_to: '<?php echo esc_js( $redirect_to ); ?>'
			},
			success: function(response) {
				if (response.success) {
					message.removeClass('error').addClass('success').html('Login realizado! Recarregando...').show();
					setTimeout(function() {
						window.location.reload();
					}, 1000);
				} else {
					message.removeClass('success').addClass('error').html(response.data.message || 'Erro ao fazer login').show();
				}
			},
			error: function() {
				message.removeClass('success').addClass('error').html('Erro ao processar. Tente novamente.').show();
			}
		});
	});
	
	// Show/hide card fields
	$('input[name="payment_method"]').on('change', function() {
		if ($(this).val() === 'CREDIT_CARD') {
			$('#vetraiz-card-fields').slideDown();
			$('#vetraiz-card-fields input').prop('required', true);
		} else {
			$('#vetraiz-card-fields').slideUp();
			$('#vetraiz-card-fields input').prop('required', false);
		}
	});
	
	// Format card number
	$('#card_number').on('input', function() {
		var value = $(this).val().replace(/\s/g, '');
		var formatted = value.match(/.{1,4}/g)?.join(' ') || value;
		$(this).val(formatted);
	});
	
	// Format expiry
	$('#card_expiry').on('input', function() {
		var value = $(this).val().replace(/\D/g, '');
		if (value.length >= 2) {
			value = value.substring(0, 2) + '/' + value.substring(2, 4);
		}
		$(this).val(value);
	});
	
	// Validate password match
	$('#user_password_confirm').on('blur', function() {
		if ($(this).val() !== $('#user_password').val()) {
			$(this).addClass('error');
			alert('As senhas não coincidem');
		} else {
			$(this).removeClass('error');
		}
	});
	
	// Handle subscription form
	$('#vetraiz-subscribe-form').on('submit', function(e) {
		e.preventDefault();
		
		var form = $(this);
		var button = form.find('button[type="submit"]');
		var message = $('#vetraiz-subscribe-message');
		
		// Validate password match if not logged in
		<?php if ( ! $is_logged_in ) : ?>
		if ($('#user_password').val() !== $('#user_password_confirm').val()) {
			message.removeClass('success').addClass('error').html('As senhas não coincidem').show();
			return;
		}
		<?php endif; ?>
		
		button.prop('disabled', true).text('Processando...');
		message.hide();
		
		$.ajax({
			url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
			type: 'POST',
			data: {
				action: 'vetraiz_create_subscription',
				nonce: '<?php echo wp_create_nonce( 'vetraiz_subscribe' ); ?>',
				form_data: form.serialize(),
				redirect_to: '<?php echo esc_js( $redirect_to ); ?>'
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
