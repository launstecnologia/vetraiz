-- =====================================================
-- QUERIES SQL: Assinaturas Vencendo Hoje
-- =====================================================
-- Substitua 'wp_' pelo prefixo da sua tabela WordPress
-- Substitua '2025-11-18' pela data de hoje (formato: YYYY-MM-DD)
-- =====================================================

-- 1. ASSINATURAS com próximo pagamento vencendo HOJE
-- Retorna assinaturas ativas/pendentes cujo next_payment_date é hoje
SELECT 
	s.id,
	s.user_id,
	u.user_email,
	u.display_name as nome_usuario,
	s.plan_name as plano,
	s.plan_value as valor,
	s.payment_method as metodo_pagamento,
	s.status as status_assinatura,
	s.next_payment_date as proximo_pagamento,
	s.asaas_subscription_id,
	s.asaas_customer_id,
	s.start_date as data_inicio,
	COUNT(p.id) as total_pagamentos,
	SUM(CASE WHEN p.status = 'received' THEN 1 ELSE 0 END) as pagamentos_pagos
FROM wp_vetraiz_subscriptions s
LEFT JOIN wp_users u ON s.user_id = u.ID
LEFT JOIN wp_vetraiz_subscription_payments p ON s.id = p.subscription_id
WHERE DATE(s.next_payment_date) = CURDATE()
	AND s.status IN ('active', 'pending')
GROUP BY s.id
ORDER BY s.next_payment_date ASC;

-- 2. PAGAMENTOS PENDENTES vencendo HOJE
-- Retorna pagamentos com status 'pending' cujo due_date é hoje
SELECT 
	p.id as payment_id,
	p.subscription_id,
	p.user_id,
	u.user_email,
	u.display_name as nome_usuario,
	s.plan_name as plano,
	p.value as valor,
	p.status as status_pagamento,
	p.due_date as data_vencimento,
	p.asaas_payment_id,
	p.asaas_invoice_number as numero_fatura,
	s.status as status_assinatura,
	s.payment_method as metodo_pagamento
FROM wp_vetraiz_subscription_payments p
LEFT JOIN wp_vetraiz_subscriptions s ON p.subscription_id = s.id
LEFT JOIN wp_users u ON p.user_id = u.ID
WHERE DATE(p.due_date) = CURDATE()
	AND p.status = 'pending'
ORDER BY p.due_date ASC;

-- 3. RESUMO ESTATÍSTICO (contagem)
SELECT 
	'Assinaturas vencendo hoje' as tipo,
	COUNT(*) as total
FROM wp_vetraiz_subscriptions
WHERE DATE(next_payment_date) = CURDATE()
	AND status IN ('active', 'pending')
UNION ALL
SELECT 
	'Pagamentos pendentes vencendo hoje' as tipo,
	COUNT(*) as total
FROM wp_vetraiz_subscription_payments
WHERE DATE(due_date) = CURDATE()
	AND status = 'pending';

-- 4. ASSINATURAS que VENCERÃO nos próximos 7 dias
-- Útil para planejamento e follow-up
SELECT 
	s.id,
	s.user_id,
	u.user_email,
	u.display_name as nome_usuario,
	s.plan_name as plano,
	s.plan_value as valor,
	s.payment_method as metodo_pagamento,
	s.status as status_assinatura,
	s.next_payment_date as proximo_pagamento,
	DATEDIFF(s.next_payment_date, CURDATE()) as dias_para_vencer,
	s.asaas_subscription_id
FROM wp_vetraiz_subscriptions s
LEFT JOIN wp_users u ON s.user_id = u.ID
WHERE DATE(s.next_payment_date) BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
	AND s.status IN ('active', 'pending')
ORDER BY s.next_payment_date ASC;

-- 5. ASSINATURAS VENCIDAS (next_payment_date no passado)
SELECT 
	s.id,
	s.user_id,
	u.user_email,
	u.display_name as nome_usuario,
	s.plan_name as plano,
	s.plan_value as valor,
	s.status as status_assinatura,
	s.next_payment_date as data_vencimento,
	DATEDIFF(CURDATE(), s.next_payment_date) as dias_vencido,
	s.asaas_subscription_id
FROM wp_vetraiz_subscriptions s
LEFT JOIN wp_users u ON s.user_id = u.ID
WHERE DATE(s.next_payment_date) < CURDATE()
	AND s.status IN ('active', 'pending')
ORDER BY s.next_payment_date ASC;

-- =====================================================
-- NOTAS:
-- =====================================================
-- - CURDATE() retorna a data atual do servidor MySQL
-- - Para usar uma data específica, substitua CURDATE() por '2025-11-18'
-- - Ajuste o prefixo 'wp_' conforme sua instalação WordPress
-- - As queries usam LEFT JOIN para incluir assinaturas mesmo sem pagamentos
-- =====================================================

