# 📦 Pacote Completo de Migração: Stripe para Asaas

## 📋 Arquivos Incluídos

Este pacote contém todos os arquivos necessários para migrar do Stripe para o Asaas:

### 1. Scripts SQL

- **`migracao_stripe_para_asaas.sql`** ⭐ **PRINCIPAL**
  - Script principal de migração
  - Desabilita Stripe
  - Habilita Asaas (Cartão e PIX)
  - Migra assinaturas e pedidos
  - Limpa dados do Stripe
  - Cria log da migração

- **`script_configuracao_asaas_otimizada.sql`** 🔧 **RECOMENDADO**
  - Otimiza configurações do Asaas
  - Habilita One-Click Buy
  - Configura notificações
  - Ajusta ordem dos gateways

- **`script_verificacao_pos_migracao.sql`** ✅ **VERIFICAÇÃO**
  - Verifica status da migração
  - Mostra estatísticas
  - Identifica problemas
  - Gera relatório completo

### 2. Documentação

- **`INSTRUCOES_MIGRACAO.md`**
  - Guia completo passo a passo
  - Configurações recomendadas
  - Troubleshooting
  - Checklist

- **`README_MIGRACAO.md`** (este arquivo)
  - Visão geral do pacote
  - Ordem de execução
  - Resumo rápido

## 🚀 Ordem de Execução

### Passo 1: Preparação
1. ✅ Fazer backup completo do banco de dados
2. ✅ Verificar se plugin Asaas está instalado
3. ✅ Verificar se plugin WooCommerce Subscriptions está instalado
4. ✅ Ter credenciais da API do Asaas em mãos

### Passo 2: Configuração Manual (WooCommerce)
> 📖 **Guia Completo**: Veja `COMO_CONFIGURAR_API_ASAAS.md` para detalhes

1. Acesse: **WooCommerce > Configurações > Pagamentos**
2. Configure **Asaas Cartão de Crédito**:
   - Clique em **"Gerenciar"**
   - ✅ Habilite o gateway
   - 🔑 **Insira a API Key do Asaas** (campo "API Key")
     - Obtenha em: https://www.asaas.com/ → Configurações → API
   - Configure opções de parcelamento
3. Configure **Asaas PIX**:
   - Clique em **"Gerenciar"**
   - ✅ Habilite o gateway
   - 🔑 **Use a MESMA API Key** (já preenchida automaticamente)
   - Configure validade do PIX (`3d` = 3 dias)
   - Ative notificações

### Passo 3: Executar Scripts SQL

```bash
# 1. Migração Principal (OBRIGATÓRIO)
mysql -u usuario -p banco_dados < migracao_stripe_para_asaas.sql

# 2. Configuração Otimizada (RECOMENDADO)
mysql -u usuario -p banco_dados < script_configuracao_asaas_otimizada.sql

# 3. Verificação (OPCIONAL mas útil)
mysql -u usuario -p banco_dados < script_verificacao_pos_migracao.sql
```

**OU** execute via phpMyAdmin:
1. Selecione o banco de dados
2. Vá em "SQL"
3. Cole e execute cada script na ordem

### Passo 4: Configurar Webhook
1. Acesse painel do Asaas
2. Configure webhook: `https://seudominio.com.br/wp-json/asaas/v1/webhook`
3. Selecione eventos de pagamento

### Passo 5: Testar
1. Teste assinatura com cartão
2. Teste assinatura com PIX
3. Verifique notificações

## ⚠️ IMPORTANTE

### Antes de Executar
- [ ] Backup do banco de dados feito
- [ ] Testado em ambiente de desenvolvimento
- [ ] Prefixo das tabelas verificado (`FCDchHQs_` ou outro)
- [ ] API Key do Asaas configurada

### Após Executar
- [ ] Verificar se Stripe foi desabilitado
- [ ] Verificar se Asaas foi habilitado
- [ ] Verificar assinaturas migradas
- [ ] Testar fluxo completo
- [ ] Configurar webhook

## 📊 O Que Cada Script Faz

### migracao_stripe_para_asaas.sql
- ✅ Desabilita gateway Stripe
- ✅ Habilita gateways Asaas (Cartão e PIX)
- ✅ Migra assinaturas ativas do Stripe para Asaas
- ✅ Atualiza pedidos pendentes
- ✅ Remove tokens e metadados do Stripe
- ✅ Configura notificações
- ✅ Cria tabela de log

### script_configuracao_asaas_otimizada.sql
- ✅ Habilita One-Click Buy no cartão
- ✅ Habilita notificações em ambos gateways
- ✅ Habilita copiar/colar no PIX
- ✅ Configura ordem dos gateways
- ✅ Otimiza configurações gerais

### script_verificacao_pos_migracao.sql
- ✅ Verifica status dos gateways
- ✅ Conta assinaturas migradas
- ✅ Conta pedidos por gateway
- ✅ Verifica configurações
- ✅ Mostra resumo completo

## 🔍 Verificação Rápida

Após executar os scripts, verifique:

```sql
-- Assinaturas com Asaas
SELECT COUNT(*) FROM FCDchHQs_posts p
INNER JOIN FCDchHQs_postmeta pm ON p.ID = pm.post_id
WHERE p.post_type = 'shop_subscription'
AND pm.meta_key = '_payment_method'
AND pm.meta_value IN ('asaas-credit-card', 'asaas-pix');

-- Stripe ainda ativo? (deve ser 0)
SELECT COUNT(*) FROM FCDchHQs_options
WHERE option_name = 'woocommerce_stripe_settings'
AND option_value LIKE '%"enabled";s:3:"yes"%';
```

## 📞 Suporte

Em caso de problemas:
1. Verifique os logs do WooCommerce
2. Execute o script de verificação
3. Consulte `INSTRUCOES_MIGRACAO.md` para troubleshooting
4. Entre em contato com suporte do Asaas

## ✅ Checklist Final

- [ ] Backup realizado
- [ ] Scripts SQL executados
- [ ] Webhook configurado
- [ ] Testes realizados
- [ ] Assinaturas funcionando
- [ ] Notificações funcionando
- [ ] Stripe desabilitado
- [ ] Asaas habilitado e funcionando

---

**Versão**: 1.0  
**Data**: 2025-11-15  
**Compatível com**: WooCommerce + WooCommerce Subscriptions + Asaas Gateway

