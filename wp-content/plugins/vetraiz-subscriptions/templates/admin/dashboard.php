<?php
/**
 * Dashboard template
 *
 * @package Vetraiz_Subscriptions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="wrap vetraiz-dashboard">
	<h1>Dashboard - Assinaturas</h1>
	
	<div class="vetraiz-stats-grid">
		<!-- Total de Usuários -->
		<div class="vetraiz-stat-card">
			<div class="stat-icon stat-users">
				<span class="dashicons dashicons-groups"></span>
			</div>
			<div class="stat-content">
				<div class="stat-value"><?php echo esc_html( number_format( $total_users, 0, ',', '.' ) ); ?></div>
				<div class="stat-label">Total de Usuários</div>
			</div>
		</div>
		
		<!-- Assinaturas no Mês -->
		<div class="vetraiz-stat-card">
			<div class="stat-icon stat-subscriptions">
				<span class="dashicons dashicons-calendar-alt"></span>
			</div>
			<div class="stat-content">
				<div class="stat-value"><?php echo esc_html( number_format( $subscriptions_this_month, 0, ',', '.' ) ); ?></div>
				<div class="stat-label">Assinaturas no Mês</div>
			</div>
		</div>
		
		<!-- Não Renovaram -->
		<div class="vetraiz-stat-card">
			<div class="stat-icon stat-expired">
				<span class="dashicons dashicons-warning"></span>
			</div>
			<div class="stat-content">
				<div class="stat-value"><?php echo esc_html( number_format( $expired_subscriptions, 0, ',', '.' ) ); ?></div>
				<div class="stat-label">Não Renovaram</div>
			</div>
		</div>
		
		<!-- Total Pago com Cartão -->
		<div class="vetraiz-stat-card">
			<div class="stat-icon stat-card">
				<span class="dashicons dashicons-credit-card"></span>
			</div>
			<div class="stat-content">
				<div class="stat-value">R$ <?php echo esc_html( number_format( $total_card, 2, ',', '.' ) ); ?></div>
				<div class="stat-label">Total Pago com Cartão</div>
			</div>
		</div>
		
		<!-- Total Pago com PIX -->
		<div class="vetraiz-stat-card">
			<div class="stat-icon stat-pix">
				<span class="dashicons dashicons-money-alt"></span>
			</div>
			<div class="stat-content">
				<div class="stat-value">R$ <?php echo esc_html( number_format( $total_pix, 2, ',', '.' ) ); ?></div>
				<div class="stat-label">Total Pago com PIX</div>
			</div>
		</div>
		
		<!-- Assinaturas Ativas -->
		<div class="vetraiz-stat-card">
			<div class="stat-icon stat-active">
				<span class="dashicons dashicons-yes-alt"></span>
			</div>
			<div class="stat-content">
				<div class="stat-value"><?php echo esc_html( number_format( $active_subscriptions, 0, ',', '.' ) ); ?></div>
				<div class="stat-label">Assinaturas Ativas</div>
			</div>
		</div>
		
		<!-- Assinaturas Pendentes -->
		<div class="vetraiz-stat-card">
			<div class="stat-icon stat-pending">
				<span class="dashicons dashicons-clock"></span>
			</div>
			<div class="stat-content">
				<div class="stat-value"><?php echo esc_html( number_format( $pending_subscriptions, 0, ',', '.' ) ); ?></div>
				<div class="stat-label">Assinaturas Pendentes</div>
			</div>
		</div>
		
		<!-- Total Recebido -->
		<div class="vetraiz-stat-card stat-total">
			<div class="stat-icon stat-total-icon">
				<span class="dashicons dashicons-chart-line"></span>
			</div>
			<div class="stat-content">
				<div class="stat-value">R$ <?php echo esc_html( number_format( $total_received, 2, ',', '.' ) ); ?></div>
				<div class="stat-label">Total Recebido</div>
			</div>
		</div>
		
		<!-- Recebido no Mês -->
		<div class="vetraiz-stat-card stat-month">
			<div class="stat-icon stat-month-icon">
				<span class="dashicons dashicons-calendar"></span>
			</div>
			<div class="stat-content">
				<div class="stat-value">R$ <?php echo esc_html( number_format( $payments_this_month, 2, ',', '.' ) ); ?></div>
				<div class="stat-label">Recebido no Mês</div>
			</div>
		</div>
	</div>
	
	<div class="vetraiz-dashboard-actions">
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=vetraiz-subscriptions-list' ) ); ?>" class="button button-primary">
			Ver Todas as Assinaturas
		</a>
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=vetraiz-subscriptions-payments' ) ); ?>" class="button button-primary">
			Ver Todos os Pagamentos
		</a>
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=vetraiz-subscriptions' ) ); ?>" class="button">
			Configurações
		</a>
	</div>
</div>

<style>
.vetraiz-dashboard {
	padding: 20px;
}

.vetraiz-stats-grid {
	display: grid;
	grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
	gap: 20px;
	margin: 30px 0;
}

.vetraiz-stat-card {
	background: #fff;
	border: 1px solid #ddd;
	border-radius: 8px;
	padding: 20px;
	display: flex;
	align-items: center;
	box-shadow: 0 2px 4px rgba(0,0,0,0.1);
	transition: transform 0.2s, box-shadow 0.2s;
}

.vetraiz-stat-card:hover {
	transform: translateY(-2px);
	box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.stat-icon {
	width: 60px;
	height: 60px;
	border-radius: 50%;
	display: flex;
	align-items: center;
	justify-content: center;
	margin-right: 15px;
	flex-shrink: 0;
}

.stat-icon .dashicons {
	font-size: 30px;
	width: 30px;
	height: 30px;
	color: #fff;
}

.stat-users {
	background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.stat-subscriptions {
	background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.stat-expired {
	background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
}

.stat-card {
	background: linear-gradient(135deg, #30cfd0 0%, #330867 100%);
}

.stat-pix {
	background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
}

.stat-pix .dashicons {
	color: #333;
}

.stat-active {
	background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%);
}

.stat-pending {
	background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
}

.stat-pending .dashicons {
	color: #333;
}

.stat-total {
	background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%);
	border: 2px solid #ff6b9d;
}

.stat-total-icon .dashicons {
	color: #333;
}

.stat-month {
	background: linear-gradient(135deg, #a1c4fd 0%, #c2e9fb 100%);
	border: 2px solid #4facfe;
}

.stat-month-icon .dashicons {
	color: #333;
}

.stat-content {
	flex: 1;
}

.stat-value {
	font-size: 28px;
	font-weight: bold;
	color: #333;
	margin-bottom: 5px;
	line-height: 1.2;
}

.stat-label {
	font-size: 14px;
	color: #666;
	text-transform: uppercase;
	letter-spacing: 0.5px;
}

.vetraiz-dashboard-actions {
	margin-top: 30px;
	padding-top: 20px;
	border-top: 1px solid #ddd;
}

.vetraiz-dashboard-actions .button {
	margin-right: 10px;
}

@media (max-width: 768px) {
	.vetraiz-stats-grid {
		grid-template-columns: 1fr;
	}
	
	.stat-value {
		font-size: 24px;
	}
}
</style>

