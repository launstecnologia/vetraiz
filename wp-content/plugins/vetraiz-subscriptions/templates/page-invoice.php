<?php
/**
 * Invoice detail page
 *
 * @package Vetraiz_Subscriptions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$payment = isset( $GLOBALS['vetraiz_payment'] ) ? $GLOBALS['vetraiz_payment'] : null;

if ( ! $payment ) {
	wp_die( 'Fatura não encontrada' );
}
?>

<div class="vetraiz-invoice-detail">
	<h1>Fatura #<?php echo esc_html( $payment->id ); ?></h1>
	
	<div class="invoice-info">
		<div class="info-row">
			<strong>Valor:</strong> R$ <?php echo esc_html( number_format( $payment->value, 2, ',', '.' ) ); ?>
		</div>
		<div class="info-row">
			<strong>Vencimento:</strong> <?php echo $payment->due_date ? esc_html( date_i18n( 'd/m/Y', strtotime( $payment->due_date ) ) ) : '-'; ?>
		</div>
		<div class="info-row">
			<strong>Status:</strong> 
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
		</div>
	</div>
	
	<?php if ( 'pending' === $payment->status && $payment->pix_qr_code ) : ?>
		<div class="pix-payment-section">
			<h2>Pagamento via PIX</h2>
			<p>Escaneie o QR Code com o aplicativo do seu banco:</p>
			
			<?php if ( $payment->pix_qr_code ) : ?>
				<div class="pix-qr-code">
					<img src="data:image/png;base64,<?php echo esc_attr( $payment->pix_qr_code ); ?>" alt="QR Code PIX">
				</div>
			<?php endif; ?>
			
			<?php if ( $payment->pix_code ) : ?>
				<div class="pix-code">
					<p><strong>Ou copie o código PIX:</strong></p>
					<textarea readonly><?php echo esc_textarea( $payment->pix_code ); ?></textarea>
					<button class="button copy-pix-code">Copiar Código</button>
				</div>
			<?php endif; ?>
		</div>
	<?php endif; ?>
	
	<div class="invoice-actions">
		<a href="<?php echo esc_url( home_url( '/minhas-faturas' ) ); ?>" class="button">Voltar para Faturas</a>
	</div>
</div>

<script>
jQuery(document).ready(function($) {
	$('.copy-pix-code').on('click', function() {
		var code = $(this).siblings('textarea').val();
		var temp = $('<input>');
		$('body').append(temp);
		temp.val(code).select();
		document.execCommand('copy');
		temp.remove();
		$(this).text('Código Copiado!');
		setTimeout(function() {
			$('.copy-pix-code').text('Copiar Código');
		}, 2000);
	});
});
</script>

<?php
get_footer();

