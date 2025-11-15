-- =====================================================
-- Script de Migração: Stripe para Asaas
-- Data: 2025-11-15
-- Descrição: Migra configurações e assinaturas do Stripe para Asaas
-- =====================================================

-- IMPORTANTE: Faça backup do banco de dados antes de executar este script!

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- =====================================================
-- 1. DESABILITAR STRIPE
-- =====================================================

-- Desabilitar gateway Stripe
UPDATE `FCDchHQs_options` 
SET `option_value` = 'no' 
WHERE `option_name` = 'woocommerce_stripe_settings' 
AND `option_value` LIKE '%"enabled";s:3:"yes"%';

-- Atualizar configuração do Stripe para desabilitado
UPDATE `FCDchHQs_options` 
SET `option_value` = REPLACE(`option_value`, '"enabled";s:3:"yes"', '"enabled";s:2:"no"')
WHERE `option_name` = 'woocommerce_stripe_settings';

-- =====================================================
-- 2. HABILITAR ASAAS (CARTÃO DE CRÉDITO E PIX)
-- =====================================================

-- Habilitar Cartão de Crédito Asaas
UPDATE `FCDchHQs_options` 
SET `option_value` = REPLACE(`option_value`, '"enabled";s:2:"no"', '"enabled";s:3:"yes"')
WHERE `option_name` = 'woocommerce_asaas-credit-card_settings'
AND `option_value` LIKE '%"enabled";s:2:"no"%';

-- Habilitar PIX Asaas
UPDATE `FCDchHQs_options` 
SET `option_value` = REPLACE(`option_value`, '"enabled";s:2:"no"', '"enabled";s:3:"yes"')
WHERE `option_name` = 'woocommerce_asaas-pix_settings'
AND `option_value` LIKE '%"enabled";s:2:"no"%';

-- =====================================================
-- 3. MIGRAR ASSINATURAS ATIVAS DO STRIPE PARA ASAAS
-- =====================================================

-- Atualizar método de pagamento nas assinaturas ativas que usam Stripe
-- Substitui 'stripe' por 'asaas-credit-card' para assinaturas ativas
UPDATE `FCDchHQs_postmeta` pm
INNER JOIN `FCDchHQs_posts` p ON pm.post_id = p.ID
SET pm.meta_value = 'asaas-credit-card'
WHERE p.post_type = 'shop_subscription'
AND pm.meta_key = '_payment_method'
AND pm.meta_value = 'stripe'
AND p.post_status IN ('wc-active', 'wc-on-hold', 'wc-pending-cancel');

-- Atualizar gateway de pagamento nos pedidos relacionados às assinaturas
UPDATE `FCDchHQs_postmeta` pm
INNER JOIN `FCDchHQs_posts` p ON pm.post_id = p.ID
INNER JOIN `FCDchHQs_postmeta` pm2 ON p.ID = pm2.post_id AND pm2.meta_key = '_subscription_renewal'
INNER JOIN `FCDchHQs_posts` sub ON pm2.meta_value = sub.ID
INNER JOIN `FCDchHQs_postmeta` sub_pm ON sub.ID = sub_pm.post_id AND sub_pm.meta_key = '_payment_method' AND sub_pm.meta_value = 'asaas-credit-card'
SET pm.meta_value = 'asaas-credit-card'
WHERE p.post_type = 'shop_order'
AND pm.meta_key = '_payment_method'
AND pm.meta_value = 'stripe';

-- =====================================================
-- 4. ATUALIZAR PEDIDOS PENDENTES COM STRIPE
-- =====================================================

-- Atualizar pedidos pendentes que usam Stripe para usar Asaas Cartão
UPDATE `FCDchHQs_postmeta` pm
INNER JOIN `FCDchHQs_posts` p ON pm.post_id = p.ID
SET pm.meta_value = 'asaas-credit-card'
WHERE p.post_type = 'shop_order'
AND pm.meta_key = '_payment_method'
AND pm.meta_value = 'stripe'
AND p.post_status IN ('wc-pending', 'wc-on-hold', 'wc-processing');

-- =====================================================
-- 5. LIMPAR METADADOS DO STRIPE DAS ASSINATURAS
-- =====================================================

-- Remover tokens de pagamento do Stripe (se existirem)
-- Nota: Usando subquery separada para evitar erro de subquery na cláusula WHERE
DELETE pm FROM `FCDchHQs_postmeta` pm
INNER JOIN `FCDchHQs_posts` p ON pm.post_id = p.ID
WHERE pm.meta_key LIKE '_stripe_%'
AND p.post_type IN ('shop_subscription', 'shop_order');

-- Remover payment method tokens do Stripe (se a tabela existir)
-- Verificar se a tabela existe antes de deletar
SET @table_exists = (
    SELECT COUNT(*) 
    FROM information_schema.tables 
    WHERE table_schema = DATABASE() 
    AND table_name = 'FCDchHQs_woocommerce_payment_tokens'
);

SET @sql = IF(@table_exists > 0,
    'DELETE FROM `FCDchHQs_woocommerce_payment_tokens` WHERE gateway_id = ''stripe''',
    'SELECT ''Tabela woocommerce_payment_tokens não existe'' AS info'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- =====================================================
-- 6. CONFIGURAR NOTIFICAÇÕES PARA PIX
-- =====================================================

-- Garantir que as notificações estão habilitadas no PIX
UPDATE `FCDchHQs_options` 
SET `option_value` = REPLACE(`option_value`, '"notification";s:2:"no"', '"notification";s:3:"yes"')
WHERE `option_name` = 'woocommerce_asaas-pix_settings'
AND `option_value` LIKE '%"notification";s:2:"no"%';

-- =====================================================
-- 7. CONFIGURAR RECORRÊNCIA AUTOMÁTICA PARA CARTÃO
-- =====================================================

-- O plugin Asaas já suporta recorrência automática por padrão
-- Apenas garantimos que está habilitado
UPDATE `FCDchHQs_options` 
SET `option_value` = REPLACE(`option_value`, '"enabled";s:2:"no"', '"enabled";s:3:"yes"')
WHERE `option_name` = 'woocommerce_asaas-credit-card_settings'
AND `option_value` LIKE '%"enabled";s:2:"no"%';

-- =====================================================
-- 8. ATUALIZAR ORDEM DOS GATEWAYS (OPCIONAL)
-- =====================================================

-- Garantir que Asaas aparece antes do Stripe na lista de gateways
-- Isso é feito automaticamente pelo WooCommerce, mas podemos forçar
UPDATE `FCDchHQs_options`
SET `option_value` = REPLACE(
    REPLACE(`option_value`, 'woocommerce_stripe', 'woocommerce_asaas-credit-card'),
    'woocommerce_asaas-credit-cardwoocommerce_stripe',
    'woocommerce_asaas-credit-card,woocommerce_stripe'
)
WHERE `option_name` = 'woocommerce_gateway_order'
AND `option_value` LIKE '%woocommerce_stripe%'
AND `option_value` NOT LIKE '%woocommerce_asaas%';

-- =====================================================
-- 9. VERIFICAÇÃO E LOG
-- =====================================================

-- Criar tabela de log da migração (se não existir)
CREATE TABLE IF NOT EXISTS `FCDchHQs_migracao_stripe_asaas` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tipo` varchar(50) NOT NULL,
  `descricao` text,
  `quantidade` int(11) DEFAULT 0,
  `data_migracao` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Registrar migração
INSERT INTO `FCDchHQs_migracao_stripe_asaas` (`tipo`, `descricao`, `quantidade`, `data_migracao`)
VALUES 
('configuracao', 'Stripe desabilitado e Asaas habilitado', 1, NOW()),
('assinaturas', 'Assinaturas migradas do Stripe para Asaas', 
    (SELECT COUNT(*) FROM `FCDchHQs_postmeta` pm
     INNER JOIN `FCDchHQs_posts` p ON pm.post_id = p.ID
     WHERE p.post_type = 'shop_subscription'
     AND pm.meta_key = '_payment_method'
     AND pm.meta_value = 'asaas-credit-card'), NOW()),
('pedidos', 'Pedidos atualizados para usar Asaas', 
    (SELECT COUNT(*) FROM `FCDchHQs_postmeta` pm
     INNER JOIN `FCDchHQs_posts` p ON pm.post_id = p.ID
     WHERE p.post_type = 'shop_order'
     AND pm.meta_key = '_payment_method'
     AND pm.meta_value = 'asaas-credit-card'), NOW());

-- =====================================================
-- 10. COMMIT DAS ALTERAÇÕES
-- =====================================================

COMMIT;

-- =====================================================
-- VERIFICAÇÕES PÓS-MIGRAÇÃO
-- =====================================================

-- Verificar assinaturas ativas com Asaas
SELECT 
    COUNT(*) as total_assinaturas_asaas,
    p.post_status,
    pm.meta_value as payment_method
FROM `FCDchHQs_posts` p
INNER JOIN `FCDchHQs_postmeta` pm ON p.ID = pm.post_id
WHERE p.post_type = 'shop_subscription'
AND pm.meta_key = '_payment_method'
AND pm.meta_value = 'asaas-credit-card'
GROUP BY p.post_status, pm.meta_value;

-- Verificar pedidos com Asaas
SELECT 
    COUNT(*) as total_pedidos_asaas,
    p.post_status,
    pm.meta_value as payment_method
FROM `FCDchHQs_posts` p
INNER JOIN `FCDchHQs_postmeta` pm ON p.ID = pm.post_id
WHERE p.post_type = 'shop_order'
AND pm.meta_key = '_payment_method'
AND pm.meta_value IN ('asaas-credit-card', 'asaas-pix')
GROUP BY p.post_status, pm.meta_value;

-- Verificar se Stripe ainda está sendo usado
SELECT 
    COUNT(*) as total_stripe_restante,
    p.post_type,
    pm.meta_value as payment_method
FROM `FCDchHQs_posts` p
INNER JOIN `FCDchHQs_postmeta` pm ON p.ID = pm.post_id
WHERE pm.meta_key = '_payment_method'
AND pm.meta_value = 'stripe'
GROUP BY p.post_type, pm.meta_value;

-- =====================================================
-- FIM DO SCRIPT
-- =====================================================

