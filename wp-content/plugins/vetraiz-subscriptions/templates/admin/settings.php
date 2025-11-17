<?php
/**
 * Admin settings page
 *
 * @package Vetraiz_Subscriptions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="wrap">
	<h1>Configurações - Vetraiz Assinaturas</h1>
	
	<form method="post" action="options.php">
		<?php settings_fields( 'vetraiz_subscriptions_settings' ); ?>
		
		<table class="form-table">
			<tr>
				<th scope="row">
					<label for="vetraiz_asaas_api_key">API Key do Asaas</label>
				</th>
				<td>
					<input type="text" id="vetraiz_asaas_api_key" name="vetraiz_asaas_api_key" value="<?php echo esc_attr( get_option( 'vetraiz_asaas_api_key', '' ) ); ?>" class="regular-text" />
					<p class="description">Sua API Key do Asaas (formato: $aact_...)</p>
				</td>
			</tr>
			
			<tr>
				<th scope="row">
					<label for="vetraiz_asaas_sandbox">Modo Sandbox</label>
				</th>
				<td>
					<input type="checkbox" id="vetraiz_asaas_sandbox" name="vetraiz_asaas_sandbox" value="1" <?php checked( get_option( 'vetraiz_asaas_sandbox', false ) ); ?> />
					<p class="description">Marque para usar o ambiente de testes do Asaas</p>
				</td>
			</tr>
			
			<tr>
				<th scope="row">
					<label for="vetraiz_plan_name">Nome do Plano</label>
				</th>
				<td>
					<input type="text" id="vetraiz_plan_name" name="vetraiz_plan_name" value="<?php echo esc_attr( get_option( 'vetraiz_plan_name', 'Assinatura Mensal' ) ); ?>" class="regular-text" />
				</td>
			</tr>
			
			<tr>
				<th scope="row">
					<label for="vetraiz_plan_value">Valor do Plano (R$)</label>
				</th>
				<td>
					<input type="number" id="vetraiz_plan_value" name="vetraiz_plan_value" value="<?php echo esc_attr( get_option( 'vetraiz_plan_value', '14.99' ) ); ?>" step="0.01" min="0" />
				</td>
			</tr>
			
			<tr>
				<th scope="row">
					<label for="vetraiz_asaas_webhook_token">Token do Webhook (Opcional)</label>
				</th>
				<td>
					<input type="text" id="vetraiz_asaas_webhook_token" name="vetraiz_asaas_webhook_token" value="<?php echo esc_attr( get_option( 'vetraiz_asaas_webhook_token', '' ) ); ?>" class="regular-text" />
					<p class="description">Token de segurança para o webhook (opcional)</p>
				</td>
			</tr>
			
			<tr>
				<th scope="row">
					<label>URL do Webhook</label>
				</th>
				<td>
					<code><?php echo esc_url( home_url( '/vetraiz-webhook' ) ); ?></code>
					<p class="description">Configure esta URL no painel do Asaas</p>
				</td>
			</tr>
		</table>
		
		<?php submit_button(); ?>
	</form>
</div>

