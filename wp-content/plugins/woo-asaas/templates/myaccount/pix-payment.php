<?php
/**
 * PIX Payment Template for My Account
 *
 * @package WooAsaas
 * @var WC_Order $order
 * @var object    $pix_info
 * @var string    $pix_qr_code
 * @var string    $pix_copy_paste
 * @var bool      $is_paid
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="woocommerce-asaas-pix-payment">
	<?php if ( $is_paid ) : ?>
		<div class="woocommerce-message woocommerce-message--success">
			<?php esc_html_e( 'Este pagamento já foi realizado.', 'woo-asaas' ); ?>
		</div>
	<?php else : ?>
		<div class="woocommerce-info">
			<p><strong><?php esc_html_e( 'Pedido #', 'woo-asaas' ); ?><?php echo esc_html( $order->get_order_number() ); ?></strong></p>
			<p><?php esc_html_e( 'Valor:', 'woo-asaas' ); ?> <strong><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></strong></p>
			<?php if ( isset( $pix_info->dueDate ) ) : ?>
				<p><?php esc_html_e( 'Vencimento:', 'woo-asaas' ); ?> <strong><?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $pix_info->dueDate ) ) ); ?></strong></p>
			<?php endif; ?>
		</div>

		<?php if ( isset( $pix_info->payload ) && ! empty( $pix_info->payload ) ) : ?>
			<div class="asaas-pix-payment-instructions">
				<h3><?php esc_html_e( 'Como pagar com PIX', 'woo-asaas' ); ?></h3>
				
				<?php if ( isset( $pix_info->encodedImage ) && ! empty( $pix_info->encodedImage ) ) : ?>
					<div class="asaas-pix-qr-code">
						<p><?php esc_html_e( 'Escaneie o QR Code com o aplicativo do seu banco:', 'woo-asaas' ); ?></p>
						<img src="data:image/png;base64,<?php echo esc_attr( $pix_info->encodedImage ); ?>" alt="<?php esc_attr_e( 'QR Code PIX', 'woo-asaas' ); ?>" style="max-width: 300px; height: auto; display: block; margin: 20px auto;" />
					</div>
				<?php endif; ?>

				<?php if ( isset( $pix_info->payload ) && ! empty( $pix_info->payload ) ) : ?>
					<div class="asaas-pix-copy-paste">
						<p><strong><?php esc_html_e( 'Ou copie o código PIX:', 'woo-asaas' ); ?></strong></p>
						<div class="asaas-pix-code-wrapper" style="position: relative; margin: 20px 0;">
							<textarea 
								id="asaas-pix-code" 
								readonly 
								style="width: 100%; min-height: 100px; padding: 10px; font-family: monospace; font-size: 12px; border: 1px solid #ddd; border-radius: 4px; resize: vertical;"
							><?php echo esc_textarea( $pix_info->payload ); ?></textarea>
							<button 
								type="button" 
								id="asaas-copy-pix-code" 
								class="button"
								style="margin-top: 10px;"
								data-copied-text="<?php esc_attr_e( 'Copiado!', 'woo-asaas' ); ?>"
							>
								<?php esc_html_e( 'Copiar código PIX', 'woo-asaas' ); ?>
							</button>
						</div>
					</div>
				<?php endif; ?>

				<div class="asaas-pix-instructions-text" style="margin-top: 30px; padding: 15px; background: #f5f5f5; border-radius: 4px;">
					<p><strong><?php esc_html_e( 'Instruções:', 'woo-asaas' ); ?></strong></p>
					<ol style="margin-left: 20px;">
						<li><?php esc_html_e( 'Abra o aplicativo do seu banco e selecione a opção PIX', 'woo-asaas' ); ?></li>
						<li><?php esc_html_e( 'Escaneie o QR Code ou cole o código copiado', 'woo-asaas' ); ?></li>
						<li><?php esc_html_e( 'Confirme os dados e finalize o pagamento', 'woo-asaas' ); ?></li>
						<li><?php esc_html_e( 'O pagamento será confirmado automaticamente em alguns minutos', 'woo-asaas' ); ?></li>
					</ol>
				</div>
			</div>
		<?php else : ?>
			<div class="woocommerce-error">
				<p><?php esc_html_e( 'Informações do PIX não disponíveis. Por favor, entre em contato conosco.', 'woo-asaas' ); ?></p>
			</div>
		<?php endif; ?>

		<div class="asaas-pix-payment-actions" style="margin-top: 30px;">
			<a href="<?php echo esc_url( wc_get_endpoint_url( 'orders', '', wc_get_page_permalink( 'myaccount' ) ) ); ?>" class="button">
				<?php esc_html_e( 'Voltar para meus pedidos', 'woo-asaas' ); ?>
			</a>
		</div>

		<script type="text/javascript">
		(function() {
			var copyButton = document.getElementById('asaas-copy-pix-code');
			var pixCode = document.getElementById('asaas-pix-code');
			
			if (copyButton && pixCode) {
				copyButton.addEventListener('click', function() {
					pixCode.select();
					pixCode.setSelectionRange(0, 99999); // For mobile devices
					
					try {
						document.execCommand('copy');
						var originalText = copyButton.textContent;
						copyButton.textContent = copyButton.getAttribute('data-copied-text');
						copyButton.style.backgroundColor = '#46b450';
						
						setTimeout(function() {
							copyButton.textContent = originalText;
							copyButton.style.backgroundColor = '';
						}, 2000);
					} catch (err) {
						alert('<?php esc_html_e( 'Erro ao copiar. Por favor, copie manualmente.', 'woo-asaas' ); ?>');
					}
				});
			}
		})();
		</script>
	<?php endif; ?>
</div>

