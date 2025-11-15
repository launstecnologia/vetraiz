-- =====================================================
-- Script de Verificação Pós-Migração: Stripe para Asaas
-- Data: 2025-11-15
-- Descrição: Verifica se a migração foi realizada com sucesso
-- =====================================================

-- =====================================================
-- 1. VERIFICAR STATUS DOS GATEWAYS
-- =====================================================

SELECT 
    'Status dos Gateways' AS verificacao,
    option_name AS gateway,
    CASE 
        WHEN option_value LIKE '%"enabled";s:3:"yes"%' THEN 'Habilitado'
        WHEN option_value LIKE '%"enabled";s:2:"no"%' THEN 'Desabilitado'
        ELSE 'Desconhecido'
    END AS status
FROM `FCDchHQs_options`
WHERE option_name IN (
    'woocommerce_stripe_settings',
    'woocommerce_asaas-credit-card_settings',
    'woocommerce_asaas-pix_settings'
)
ORDER BY option_name;

-- =====================================================
-- 2. VERIFICAR ASSINATURAS MIGRADAS
-- =====================================================

SELECT 
    'Assinaturas por Gateway' AS verificacao,
    pm.meta_value AS payment_method,
    p.post_status AS status,
    COUNT(*) AS total
FROM `FCDchHQs_posts` p
INNER JOIN `FCDchHQs_postmeta` pm ON p.ID = pm.post_id
WHERE p.post_type = 'shop_subscription'
AND pm.meta_key = '_payment_method'
GROUP BY pm.meta_value, p.post_status
ORDER BY pm.meta_value, p.post_status;

-- =====================================================
-- 3. VERIFICAR PEDIDOS POR GATEWAY
-- =====================================================

SELECT 
    'Pedidos por Gateway' AS verificacao,
    pm.meta_value AS payment_method,
    p.post_status AS status,
    COUNT(*) AS total
FROM `FCDchHQs_posts` p
INNER JOIN `FCDchHQs_postmeta` pm ON p.ID = pm.post_id
WHERE p.post_type = 'shop_order'
AND pm.meta_key = '_payment_method'
GROUP BY pm.meta_value, p.post_status
ORDER BY pm.meta_value, p.post_status;

-- =====================================================
-- 4. VERIFICAR ASSINATURAS ATIVAS COM ASAAS
-- =====================================================

SELECT 
    'Assinaturas Ativas com Asaas' AS verificacao,
    COUNT(*) AS total_assinaturas_ativas,
    SUM(CASE WHEN pm.meta_value = 'asaas-credit-card' THEN 1 ELSE 0 END) AS cartao_credito,
    SUM(CASE WHEN pm.meta_value = 'asaas-pix' THEN 1 ELSE 0 END) AS pix,
    SUM(CASE WHEN pm.meta_value = 'stripe' THEN 1 ELSE 0 END) AS stripe_restante
FROM `FCDchHQs_posts` p
INNER JOIN `FCDchHQs_postmeta` pm ON p.ID = pm.post_id
WHERE p.post_type = 'shop_subscription'
AND pm.meta_key = '_payment_method'
AND p.post_status = 'wc-active';

-- =====================================================
-- 5. VERIFICAR PEDIDOS PENDENTES
-- =====================================================

SELECT 
    'Pedidos Pendentes por Gateway' AS verificacao,
    pm.meta_value AS payment_method,
    COUNT(*) AS total_pendentes
FROM `FCDchHQs_posts` p
INNER JOIN `FCDchHQs_postmeta` pm ON p.ID = pm.post_id
WHERE p.post_type = 'shop_order'
AND pm.meta_key = '_payment_method'
AND p.post_status IN ('wc-pending', 'wc-on-hold')
GROUP BY pm.meta_value
ORDER BY total_pendentes DESC;

-- =====================================================
-- 6. VERIFICAR CONFIGURAÇÕES DO ASAAS
-- =====================================================

SELECT 
    'Configurações Asaas Cartão' AS verificacao,
    CASE 
        WHEN option_value LIKE '%"enabled";s:3:"yes"%' THEN 'Habilitado'
        ELSE 'Desabilitado'
    END AS gateway_status,
    CASE 
        WHEN option_value LIKE '%"notification";s:3:"yes"%' THEN 'Notificações Ativas'
        ELSE 'Notificações Desativadas'
    END AS notificacoes,
    CASE 
        WHEN option_value LIKE '%"one_click_buy";s:3:"yes"%' THEN 'One-Click Ativo'
        ELSE 'One-Click Desativado'
    END AS one_click
FROM `FCDchHQs_options`
WHERE option_name = 'woocommerce_asaas-credit-card_settings';

SELECT 
    'Configurações Asaas PIX' AS verificacao,
    CASE 
        WHEN option_value LIKE '%"enabled";s:3:"yes"%' THEN 'Habilitado'
        ELSE 'Desabilitado'
    END AS gateway_status,
    CASE 
        WHEN option_value LIKE '%"notification";s:3:"yes"%' THEN 'Notificações Ativas'
        ELSE 'Notificações Desativadas'
    END AS notificacoes,
    CASE 
        WHEN option_value LIKE '%"copy_and_paste";s:3:"yes"%' THEN 'Copiar/Colar Ativo'
        ELSE 'Copiar/Colar Desativado'
    END AS copy_paste
FROM `FCDchHQs_options`
WHERE option_name = 'woocommerce_asaas-pix_settings';

-- =====================================================
-- 7. VERIFICAR LOG DE MIGRAÇÃO
-- =====================================================

SELECT 
    'Log de Migração' AS verificacao,
    tipo,
    descricao,
    quantidade,
    data_migracao
FROM `FCDchHQs_migracao_stripe_asaas`
ORDER BY data_migracao DESC;

-- =====================================================
-- 8. VERIFICAR TOKENS DE PAGAMENTO
-- =====================================================

-- Verificar se ainda existem tokens do Stripe
SELECT 
    'Tokens de Pagamento' AS verificacao,
    gateway_id,
    COUNT(*) AS total_tokens
FROM `FCDchHQs_woocommerce_payment_tokens`
GROUP BY gateway_id
ORDER BY total_tokens DESC;

-- =====================================================
-- 9. VERIFICAR METADADOS DO STRIPE RESTANTES
-- =====================================================

SELECT 
    'Metadados Stripe Restantes' AS verificacao,
    meta_key,
    COUNT(*) AS total_ocorrencias
FROM `FCDchHQs_postmeta`
WHERE meta_key LIKE '_stripe_%'
GROUP BY meta_key
ORDER BY total_ocorrencias DESC;

-- =====================================================
-- 10. RESUMO GERAL
-- =====================================================

SELECT 
    'RESUMO GERAL DA MIGRAÇÃO' AS verificacao,
    (SELECT COUNT(*) FROM `FCDchHQs_posts` p
     INNER JOIN `FCDchHQs_postmeta` pm ON p.ID = pm.post_id
     WHERE p.post_type = 'shop_subscription'
     AND pm.meta_key = '_payment_method'
     AND pm.meta_value = 'asaas-credit-card') AS assinaturas_cartao_asaas,
    (SELECT COUNT(*) FROM `FCDchHQs_posts` p
     INNER JOIN `FCDchHQs_postmeta` pm ON p.ID = pm.post_id
     WHERE p.post_type = 'shop_subscription'
     AND pm.meta_key = '_payment_method'
     AND pm.meta_value = 'asaas-pix') AS assinaturas_pix_asaas,
    (SELECT COUNT(*) FROM `FCDchHQs_posts` p
     INNER JOIN `FCDchHQs_postmeta` pm ON p.ID = pm.post_id
     WHERE p.post_type = 'shop_subscription'
     AND pm.meta_key = '_payment_method'
     AND pm.meta_value = 'stripe') AS assinaturas_stripe_restantes,
    (SELECT COUNT(*) FROM `FCDchHQs_posts` p
     INNER JOIN `FCDchHQs_postmeta` pm ON p.ID = pm.post_id
     WHERE p.post_type = 'shop_order'
     AND pm.meta_key = '_payment_method'
     AND pm.meta_value IN ('asaas-credit-card', 'asaas-pix')) AS pedidos_asaas,
    (SELECT COUNT(*) FROM `FCDchHQs_posts` p
     INNER JOIN `FCDchHQs_postmeta` pm ON p.ID = pm.post_id
     WHERE p.post_type = 'shop_order'
     AND pm.meta_key = '_payment_method'
     AND pm.meta_value = 'stripe') AS pedidos_stripe_restantes;

-- =====================================================
-- FIM DO SCRIPT DE VERIFICAÇÃO
-- =====================================================

