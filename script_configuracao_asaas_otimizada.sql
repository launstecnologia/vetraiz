-- =====================================================
-- Script de Configuração Otimizada: Asaas
-- Data: 2025-11-15
-- Descrição: Configurações otimizadas para Asaas após migração
-- =====================================================

-- IMPORTANTE: Execute este script APÓS a migração principal
-- Este script otimiza as configurações do Asaas para melhor funcionamento

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- =====================================================
-- 1. CONFIGURAR CARTÃO DE CRÉDITO OTIMIZADO
-- =====================================================

-- Habilitar notificações no cartão
UPDATE `FCDchHQs_options` 
SET `option_value` = REPLACE(`option_value`, '"notification";s:2:"no"', '"notification";s:3:"yes"')
WHERE `option_name` = 'woocommerce_asaas-credit-card_settings'
AND `option_value` LIKE '%"notification";s:2:"no"%';

-- Habilitar One-Click Buy (melhora UX para recorrência)
UPDATE `FCDchHQs_options` 
SET `option_value` = REPLACE(`option_value`, '"one_click_buy";s:2:"no"', '"one_click_buy";s:3:"yes"')
WHERE `option_name` = 'woocommerce_asaas-credit-card_settings'
AND `option_value` LIKE '%"one_click_buy";s:2:"no"%';

-- Configurar status aguardando pagamento
UPDATE `FCDchHQs_options` 
SET `option_value` = REPLACE(`option_value`, '"awaiting_payment_status";s:7:"pending"', '"awaiting_payment_status";s:7:"pending"')
WHERE `option_name` = 'woocommerce_asaas-credit-card_settings';

-- =====================================================
-- 2. CONFIGURAR PIX OTIMIZADO
-- =====================================================

-- Habilitar notificações no PIX (obrigatório)
UPDATE `FCDchHQs_options` 
SET `option_value` = REPLACE(`option_value`, '"notification";s:2:"no"', '"notification";s:3:"yes"')
WHERE `option_name` = 'woocommerce_asaas-pix_settings'
AND `option_value` LIKE '%"notification";s:2:"no"%';

-- Habilitar copiar e colar (melhora UX)
UPDATE `FCDchHQs_options` 
SET `option_value` = REPLACE(`option_value`, '"copy_and_paste";s:2:"no"', '"copy_and_paste";s:3:"yes"')
WHERE `option_name` = 'woocommerce_asaas-pix_settings'
AND `option_value` LIKE '%"copy_and_paste";s:2:"no"%';

-- Configurar validade do PIX para 3 dias (padrão recomendado)
UPDATE `FCDchHQs_options` 
SET `option_value` = REPLACE(`option_value`, '"validity_days";s:1:"3"', '"validity_days";s:1:"3"')
WHERE `option_name` = 'woocommerce_asaas-pix_settings';

-- Configurar status aguardando pagamento
UPDATE `FCDchHQs_options` 
SET `option_value` = REPLACE(`option_value`, '"awaiting_payment_status";s:7:"pending"', '"awaiting_payment_status";s:7:"pending"')
WHERE `option_name` = 'woocommerce_asaas-pix_settings';

-- =====================================================
-- 3. GARANTIR QUE AMBOS OS GATEWAYS ESTÃO HABILITADOS
-- =====================================================

-- Cartão de Crédito
UPDATE `FCDchHQs_options` 
SET `option_value` = REPLACE(`option_value`, '"enabled";s:2:"no"', '"enabled";s:3:"yes"')
WHERE `option_name` = 'woocommerce_asaas-credit-card_settings'
AND `option_value` LIKE '%"enabled";s:2:"no"%';

-- PIX
UPDATE `FCDchHQs_options` 
SET `option_value` = REPLACE(`option_value`, '"enabled";s:2:"no"', '"enabled";s:3:"yes"')
WHERE `option_name` = 'woocommerce_asaas-pix_settings'
AND `option_value` LIKE '%"enabled";s:2:"no"%';

-- =====================================================
-- 4. CONFIGURAR ORDEM DOS GATEWAYS
-- =====================================================

-- Garantir que Asaas aparece primeiro na lista
UPDATE `FCDchHQs_options`
SET `option_value` = CONCAT(
    'woocommerce_asaas-credit-card,woocommerce_asaas-pix,',
    REPLACE(REPLACE(`option_value`, 'woocommerce_asaas-credit-card,', ''), 'woocommerce_asaas-pix,', '')
)
WHERE `option_name` = 'woocommerce_gateway_order'
AND `option_value` NOT LIKE 'woocommerce_asaas-credit-card%';

-- Se não existir a opção, criar
INSERT INTO `FCDchHQs_options` (`option_name`, `option_value`, `autoload`)
SELECT 'woocommerce_gateway_order', 'woocommerce_asaas-credit-card,woocommerce_asaas-pix', 'yes'
WHERE NOT EXISTS (
    SELECT 1 FROM `FCDchHQs_options` 
    WHERE `option_name` = 'woocommerce_gateway_order'
);

-- =====================================================
-- 5. VERIFICAR E CONFIGURAR WEBHOOK
-- =====================================================

-- Verificar se webhook_access_token está configurado
SELECT 
    'Verificação Webhook' AS info,
    CASE 
        WHEN option_value LIKE '%webhook_access_token%' THEN 'Token configurado'
        ELSE 'Token não encontrado - Configure manualmente no painel Asaas'
    END AS status_webhook
FROM `FCDchHQs_options`
WHERE option_name IN ('woocommerce_asaas-credit-card_settings', 'woocommerce_asaas-pix_settings')
LIMIT 1;

-- =====================================================
-- 6. COMMIT DAS ALTERAÇÕES
-- =====================================================

COMMIT;

-- =====================================================
-- VERIFICAÇÃO FINAL
-- =====================================================

SELECT 
    'Configuração Finalizada' AS status,
    'Asaas Cartão e PIX configurados e otimizados' AS descricao,
    NOW() AS data_configuracao;

-- =====================================================
-- FIM DO SCRIPT
-- =====================================================

