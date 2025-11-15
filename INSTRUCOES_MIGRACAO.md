# Instruções de Migração: Stripe para Asaas

## 📋 Visão Geral

Este documento contém as instruções para migrar o sistema de pagamento do **Stripe** para o **Asaas**, mantendo:
- ✅ Pagamento mensal recorrente com cartão de crédito (débito automático)
- ✅ Pagamento por PIX com notificações
- ✅ Liberação de assinatura ao assinar

## ⚠️ IMPORTANTE - ANTES DE COMEÇAR

1. **FAÇA BACKUP COMPLETO DO BANCO DE DADOS**
2. **TESTE EM AMBIENTE DE DESENVOLVIMENTO PRIMEIRO**
3. **VERIFIQUE SE O PLUGIN ASAAS ESTÁ INSTALADO E ATIVO**
4. **CONFIGURE A API KEY DO ASAAS NO WOOCOMMERCE**

## 📦 Pré-requisitos

- Plugin `woo-asaas` instalado e ativo
- Plugin `woocommerce-subscriptions` instalado e ativo
- Acesso ao banco de dados MySQL/MariaDB
- Credenciais da API do Asaas configuradas

## 🔧 Passo a Passo

### 1. Configurar API do Asaas

> 📖 **Guia Detalhado**: Consulte o arquivo `COMO_CONFIGURAR_API_ASAAS.md` para instruções completas com imagens

1. Acesse: **WooCommerce > Configurações > Pagamentos**
2. Configure o **Asaas Cartão de Crédito**:
   - Clique em **"Gerenciar"** ao lado do gateway
   - ✅ **Habilite** o gateway (marque "Enable/Disable")
   - 🔑 **Insira a API Key do Asaas** no campo "API Key"
     - Formato: `$aact_XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX...`
     - Obtenha em: https://www.asaas.com/ → Configurações → Integrações → API
   - Configure as opções de parcelamento (se necessário)
   - Ative "One-Click Buy" (opcional, para compras rápidas)
   - Configure email para notificações

3. Configure o **Asaas PIX**:
   - Clique em **"Gerenciar"** ao lado do gateway
   - ✅ **Habilite** o gateway
   - 🔑 **Use a MESMA API Key** do Cartão de Crédito (já será preenchida automaticamente)
   - Configure dias de validade do PIX (padrão: `3d` = 3 dias)
   - Ative "Copy and Paste" (copiar código PIX)
   - Ative notificações por email
   - Configure webhook (importante para notificações)

### 2. Executar Scripts SQL

**IMPORTANTE**: Verifique o prefixo das tabelas no seu banco de dados
- Os scripts usam `FCDchHQs_` como prefixo padrão
- Se seu prefixo for diferente, substitua todas as ocorrências

#### Passo 2.1: Migração Principal
1. Abra o arquivo `migracao_stripe_para_asaas.sql`
2. Execute o script no phpMyAdmin ou cliente MySQL
3. Verifique se não houve erros

#### Passo 2.2: Configuração Otimizada (Opcional mas Recomendado)
1. Abra o arquivo `script_configuracao_asaas_otimizada.sql`
2. Execute para otimizar as configurações do Asaas
3. Este script habilita One-Click Buy, notificações e outras otimizações

#### Passo 2.3: Verificação Pós-Migração
1. Abra o arquivo `script_verificacao_pos_migracao.sql`
2. Execute para verificar se tudo foi migrado corretamente
3. Revise os resultados das consultas

### 3. Verificar Migração

Após executar o script, verifique:

```sql
-- Verificar assinaturas migradas
SELECT COUNT(*) as total_assinaturas_asaas
FROM FCDchHQs_posts p
INNER JOIN FCDchHQs_postmeta pm ON p.ID = pm.post_id
WHERE p.post_type = 'shop_subscription'
AND pm.meta_key = '_payment_method'
AND pm.meta_value = 'asaas-credit-card';

-- Verificar se ainda há Stripe ativo
SELECT COUNT(*) as total_stripe_restante
FROM FCDchHQs_posts p
INNER JOIN FCDchHQs_postmeta pm ON p.ID = pm.post_id
WHERE pm.meta_key = '_payment_method'
AND pm.meta_value = 'stripe';
```

### 4. Configurar Webhook do Asaas

1. Acesse o painel do Asaas
2. Vá em **Configurações > Webhooks**
3. Configure a URL do webhook:
   ```
   https://seudominio.com.br/wp-json/asaas/v1/webhook
   ```
4. Selecione os eventos:
   - ✅ PAYMENT_CREATED
   - ✅ PAYMENT_CONFIRMED
   - ✅ PAYMENT_RECEIVED
   - ✅ PAYMENT_OVERDUE
   - ✅ PAYMENT_REFUNDED

### 5. Testar Funcionalidades

#### Teste 1: Assinatura com Cartão de Crédito
1. Crie um produto de assinatura
2. Faça um pedido de teste com cartão
3. Verifique se:
   - ✅ A assinatura é criada no Asaas
   - ✅ O pagamento é processado
   - ✅ A assinatura fica ativa
   - ✅ O débito automático está configurado

#### Teste 2: Assinatura com PIX
1. Crie um produto de assinatura
2. Faça um pedido de teste com PIX
3. Verifique se:
   - ✅ O código PIX é gerado
   - ✅ A notificação é enviada
   - ✅ Ao pagar, a assinatura é ativada
   - ✅ As notificações de renovação funcionam

## 🔄 Como Funciona Após a Migração

### Cartão de Crédito (Recorrência Automática)
- ✅ Ao assinar, o cartão é salvo no Asaas
- ✅ Todo mês, o Asaas debita automaticamente
- ✅ Se o pagamento falhar, o cliente recebe notificação
- ✅ A assinatura continua ativa enquanto houver pagamentos

### PIX (Com Notificações)
- ✅ Ao assinar, um código PIX é gerado
- ✅ Cliente recebe notificação por email
- ✅ Cliente pode ver o PIX na área "Minha Conta"
- ✅ Ao pagar, a assinatura é ativada automaticamente
- ✅ A cada renovação, novo PIX é gerado e notificado

## 📝 O Que o Script Faz

1. **Desabilita o Stripe** como gateway de pagamento
2. **Habilita o Asaas** (Cartão e PIX)
3. **Migra assinaturas ativas** do Stripe para Asaas
4. **Atualiza pedidos pendentes** para usar Asaas
5. **Remove tokens do Stripe** (limpeza)
6. **Configura notificações** para PIX
7. **Cria log da migração** na tabela `FCDchHQs_migracao_stripe_asaas`

## ⚙️ Configurações Recomendadas

### Asaas Cartão de Crédito
- **Status aguardando pagamento**: `pending`
- **Parcelamento máximo**: Conforme sua necessidade
- **One-Click Buy**: `Sim` (melhora UX)
- **Notificações**: `Sim`

### Asaas PIX
- **Dias de validade**: `3` (padrão)
- **Copiar e colar**: `Sim` (facilita para cliente)
- **Notificações**: `Sim` (obrigatório)
- **Email de notificação**: Configure seu email

## 🐛 Troubleshooting

### Problema: Assinaturas não estão sendo renovadas
**Solução**: 
- Verifique se o webhook está configurado corretamente
- Verifique os logs do WooCommerce
- Confirme que a API Key está correta

### Problema: PIX não está gerando código
**Solução**:
- Verifique se o gateway PIX está habilitado
- Confirme a API Key do Asaas
- Verifique os logs de erro

### Problema: Notificações não estão sendo enviadas
**Solução**:
- Configure o webhook no painel do Asaas
- Verifique o email de notificação nas configurações
- Teste o webhook manualmente

## 📞 Suporte

Em caso de dúvidas:
1. Consulte a documentação do plugin Asaas
2. Verifique os logs do WooCommerce
3. Entre em contato com o suporte do Asaas

## ✅ Checklist Pós-Migração

- [ ] Backup do banco realizado
- [ ] Script SQL executado com sucesso
- [ ] Stripe desabilitado
- [ ] Asaas habilitado (Cartão e PIX)
- [ ] Webhook configurado
- [ ] Teste de assinatura com cartão realizado
- [ ] Teste de assinatura com PIX realizado
- [ ] Notificações funcionando
- [ ] Assinaturas antigas migradas
- [ ] Logs verificados

---

**Data de criação**: 2025-11-15  
**Versão**: 1.0

