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
$expired = isset( $_GET['expired'] ) && '1' === $_GET['expired'];
?>

<div class="vetraiz-subscribe-form-wrapper">
	<div class="vetraiz-subscribe-form">
		<div class="vetraiz-form-header">
			<h2><?php echo esc_html( $plan_name ); ?></h2>
			<?php if ( $expired ) : ?>
				<div class="vetraiz-alert vetraiz-alert-warning">
					<p><strong>Sua assinatura anterior expirou.</strong> Para continuar acessando o conteúdo, assine novamente.</p>
				</div>
			<?php elseif ( $redirect_to ) : ?>
				<div class="vetraiz-alert vetraiz-alert-info">
					<p>Você precisa de uma assinatura ativa para acessar este conteúdo.</p>
				</div>
			<?php endif; ?>
		</div>
		
		<?php if ( ! $is_logged_in ) : ?>
			<div class="vetraiz-login-section">
				<p>Já tem uma conta? <a href="#" id="vetraiz-show-login">Fazer Login</a></p>
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
			
			<div class="vetraiz-form-grid">
				<!-- Coluna Esquerda: Dados Pessoais -->
				<div class="vetraiz-form-column vetraiz-form-left">
					<?php if ( ! $is_logged_in ) : ?>
						<div class="form-section">
							<h3>Dados de Acesso</h3>
							<div class="form-group">
								<label for="user_email">E-mail *</label>
								<input type="email" id="user_email" name="user_email" required>
							</div>
							
							<div class="form-group">
								<label for="user_password">Senha *</label>
								<input type="password" id="user_password" name="user_password" minlength="6" required>
								<small>Mínimo 6 caracteres</small>
							</div>
							
							<div class="form-group">
								<label for="user_password_confirm">Confirmar Senha *</label>
								<input type="password" id="user_password_confirm" name="user_password_confirm" minlength="6" required>
							</div>
						</div>
					<?php endif; ?>
					
					<div class="form-section">
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
						
						<div class="form-row">
							<div class="form-group">
								<label for="user_phone">Telefone *</label>
								<input type="text" id="user_phone" name="user_phone" value="<?php echo esc_attr( get_user_meta( $user->ID, 'billing_phone', true ) ); ?>" required>
							</div>
							
							<div class="form-group">
								<label for="user_cpf">CPF *</label>
								<input type="text" id="user_cpf" name="user_cpf" value="<?php echo esc_attr( get_user_meta( $user->ID, 'billing_cpf', true ) ); ?>" required>
							</div>
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
						
						<div class="form-row">
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
						</div>
					</div>
				</div>
				
				<!-- Coluna Direita: Resumo e Pagamento -->
				<div class="vetraiz-form-column vetraiz-form-right">
					<div class="form-section">
						<h3>Resumo da Assinatura</h3>
						<div class="plan-summary">
							<div class="plan-item">
								<span class="plan-label">Plano:</span>
								<span class="plan-value"><?php echo esc_html( $plan_name ); ?></span>
							</div>
							<div class="plan-item">
								<span class="plan-label">Valor:</span>
								<span class="plan-value plan-price">R$ <?php echo esc_html( number_format( $plan_value, 2, ',', '.' ) ); ?> / mês</span>
							</div>
						</div>
					</div>
					
					<div class="form-section">
						<h3>Forma de Pagamento *</h3>
						<div class="payment-methods">
							<label class="payment-method-option" for="payment_pix">
								<input type="radio" id="payment_pix" name="payment_method" value="PIX" required>
								<div class="payment-method-content">
									<div class="payment-icon">
										<img src="https://geradornv.com.br/wp-content/themes/v1.38.0/assets/images/logos/pix/logo-pix-520x520.png" alt="PIX" style="width: 40px; height: 40px; object-fit: contain;">
									</div>
									<div class="payment-method-info">
										<span class="payment-method-name">PIX</span>
										<span class="payment-method-desc">Fatura mensal para pagar</span>
									</div>
								</div>
							</label>
							
							<label class="payment-method-option" for="payment_card">
								<input type="radio" id="payment_card" name="payment_method" value="CREDIT_CARD" required>
								<div class="payment-method-content">
									<div class="payment-icon">
										<svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
											<rect width="40" height="40" rx="8" fill="#1A73E8"/>
											<rect x="8" y="14" width="24" height="16" rx="2" fill="white"/>
											<rect x="10" y="18" width="20" height="2" rx="1" fill="#1A73E8"/>
											<rect x="10" y="22" width="8" height="2" rx="1" fill="#1A73E8"/>
											<rect x="20" y="22" width="10" height="2" rx="1" fill="#1A73E8"/>
										</svg>
									</div>
									<div class="payment-method-info">
										<span class="payment-method-name">Cartão de Crédito</span>
										<span class="payment-method-desc">Débito automático mensal</span>
									</div>
								</div>
							</label>
						</div>
						
						<div id="vetraiz-card-fields" class="card-fields-section" style="display: none;">
							<h4>Dados do Cartão</h4>
							<div class="form-group">
								<label for="card_holder_name">Nome no Cartão *</label>
								<input type="text" id="card_holder_name" name="card_holder_name" placeholder="Nome como está no cartão">
							</div>
							
							<div class="form-group">
								<label for="card_number">Número do Cartão *</label>
								<input type="text" id="card_number" name="card_number" maxlength="19" placeholder="0000 0000 0000 0000">
							</div>
							
							<div class="form-row">
								<div class="form-group">
									<label for="card_expiry">Validade (MM/AA) *</label>
									<input type="text" id="card_expiry" name="card_expiry" maxlength="5" placeholder="MM/AA">
								</div>
								<div class="form-group">
									<label for="card_cvv">CVV *</label>
									<input type="text" id="card_cvv" name="card_cvv" maxlength="4" placeholder="123">
								</div>
							</div>
						</div>
					</div>
					
					<div class="form-actions">
						<button type="submit" class="button button-primary button-large">Assinar Agora</button>
					</div>
					
					<div id="vetraiz-subscribe-message" style="display: none; margin-top: 20px;"></div>
				</div>
			</div>
		</form>
	</div>
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
			$('#vetraiz-card-fields input').val(''); // Clear card fields when switching to PIX
		}
	});
	
	// Validate payment method selection before submit
	$('#vetraiz-subscribe-form').on('submit', function(e) {
		var paymentMethod = $('input[name="payment_method"]:checked').val();
		if (!paymentMethod) {
			e.preventDefault();
			alert('Por favor, selecione uma forma de pagamento.');
			return false;
		}
		if (paymentMethod === 'CREDIT_CARD') {
			// Validate card fields
			var cardHolder = $('#card_holder_name').val();
			var cardNumber = $('#card_number').val().replace(/\s/g, '');
			var cardExpiry = $('#card_expiry').val();
			var cardCvv = $('#card_cvv').val();
			
			if (!cardHolder || !cardNumber || !cardExpiry || !cardCvv) {
				e.preventDefault();
				alert('Por favor, preencha todos os dados do cartão.');
				return false;
			}
			
			// Validate card number (basic check - should be 13-19 digits)
			if (cardNumber.length < 13 || cardNumber.length > 19) {
				e.preventDefault();
				alert('Número do cartão inválido.');
				return false;
			}
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
