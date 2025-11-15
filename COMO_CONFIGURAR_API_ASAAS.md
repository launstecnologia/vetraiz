# 🔑 Como Configurar o Token/API Key do Asaas

## 📍 Localização no WooCommerce

O Token/API Key do Asaas é configurado no painel administrativo do WooCommerce, na seção de **Pagamentos**.

## 🚀 Passo a Passo Detalhado

### Passo 1: Acessar Configurações de Pagamento

1. Faça login no **WordPress Admin**
2. No menu lateral, vá em: **WooCommerce** → **Configurações**
3. Clique na aba **Pagamentos** (ou **Payments**)
4. Você verá uma lista de todos os gateways de pagamento disponíveis

### Passo 2: Configurar Asaas Cartão de Crédito

1. Na lista de gateways, encontre **"Asaas Credit Card"** ou **"Asaas Cartão de Crédito"**
2. Clique em **"Gerenciar"** ou **"Manage"** ao lado do gateway
3. Você verá as seguintes opções:

#### Configurações Principais:

- ✅ **Enable/Disable** (Habilitar/Desabilitar)
  - Marque esta opção para **habilitar** o gateway

- 🔑 **API Key** (Token do Asaas)
  - **AQUI É ONDE VOCÊ COLOCA O TOKEN!**
  - Cole sua API Key do Asaas neste campo
  - O campo aparece como texto ou senha (mascarado)
  - **Formato**: `$aact_XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX...`

- 🌐 **Endpoint** (URL da API)
  - Padrão: `https://api.asaas.com/v3`
  - **Produção**: `https://api.asaas.com/v3`
  - **Sandbox/Teste**: `https://sandbox.asaas.com/api/v3`

- 📧 **Notification** (Notificações)
  - Marque para receber notificações por email

- 📧 **Email Notification** (Email para notificações)
  - Digite o email que receberá as notificações

### Passo 3: Configurar Asaas PIX

1. Na lista de gateways, encontre **"Asaas Pix"** ou **"Asaas PIX"**
2. Clique em **"Gerenciar"** ou **"Manage"**
3. **IMPORTANTE**: O PIX usa a **MESMA API Key** do Cartão de Crédito
   - Se você já configurou no Cartão, o PIX já terá a mesma chave
   - Caso contrário, cole a mesma API Key aqui

#### Configurações Específicas do PIX:

- ✅ **Enable/Disable** (Habilitar/Desabilitar)
- 🔑 **API Key** (mesma do Cartão)
- ⏰ **Validity Days** (Dias de validade)
  - Padrão: `3d` (3 dias)
  - Formato: `10m` (minutos), `3h` (horas), `3d` (dias)
- 📋 **Copy and Paste** (Copiar e Colar)
  - Habilite para mostrar código PIX para copiar

## 🔑 Onde Obter a API Key do Asaas

### Opção 1: Painel do Asaas (Recomendado)

1. Acesse: https://www.asaas.com/
2. Faça login na sua conta
3. Vá em **Configurações** → **Integrações** → **API**
4. Copie sua **API Key** (Token de Produção ou Sandbox)
5. A API Key tem o formato: `$aact_XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX...`

### Opção 2: Via SQL (Se já estiver configurada)

Se você já tem a API Key configurada em algum lugar, pode verificar no banco:

```sql
-- Ver API Key atual (aparece mascarada)
SELECT option_name, 
       SUBSTRING(option_value, 1, 50) as api_key_preview
FROM FCDchHQs_options
WHERE option_name LIKE 'woocommerce_asaas%settings'
AND option_value LIKE '%api_key%';
```

## ⚙️ Configuração via SQL (Alternativa)

Se preferir configurar diretamente no banco de dados:

```sql
-- Substituir API_KEY_AQUI pela sua API Key real
UPDATE `FCDchHQs_options`
SET `option_value` = REPLACE(
    `option_value`, 
    's:7:"api_key";s:0:""',
    CONCAT('s:7:"api_key";s:', LENGTH('API_KEY_AQUI'), ':"', 'API_KEY_AQUI', '"')
)
WHERE `option_name` IN (
    'woocommerce_asaas-credit-card_settings',
    'woocommerce_asaas-pix_settings'
);
```

**⚠️ ATENÇÃO**: Use este método apenas se souber o que está fazendo!

## ✅ Verificar se Está Configurado Corretamente

### Método 1: Via Painel WooCommerce

1. Vá em **WooCommerce** → **Configurações** → **Pagamentos**
2. Clique em **"Gerenciar"** no Asaas Cartão de Crédito
3. Verifique se o campo **API Key** está preenchido
4. Se estiver mascarado (****), está configurado
5. Se estiver vazio, precisa configurar

### Método 2: Via SQL

```sql
-- Verificar se API Key está configurada
SELECT 
    option_name,
    CASE 
        WHEN option_value LIKE '%"api_key";s:0:""%' THEN 'NÃO CONFIGURADA'
        WHEN option_value LIKE '%"api_key";s:%' THEN 'CONFIGURADA'
        ELSE 'DESCONHECIDO'
    END AS status_api_key
FROM `FCDchHQs_options`
WHERE option_name IN (
    'woocommerce_asaas-credit-card_settings',
    'woocommerce_asaas-pix_settings'
);
```

## 🎯 Resumo Visual

```
WordPress Admin
    └── WooCommerce
        └── Configurações
            └── Pagamentos (aba)
                ├── Asaas Credit Card
                │   └── Gerenciar
                │       ├── ✅ Habilitar
                │       ├── 🔑 API Key ← AQUI!
                │       ├── 🌐 Endpoint
                │       └── 📧 Notificações
                │
                └── Asaas Pix
                    └── Gerenciar
                        ├── ✅ Habilitar
                        ├── 🔑 API Key ← MESMA DO CARTÃO
                        ├── ⏰ Validade
                        └── 📋 Copiar/Colar
```

## 🔒 Segurança

- ✅ A API Key é armazenada de forma segura no banco de dados
- ✅ No painel, ela aparece mascarada (****) após salvar
- ✅ Use sempre HTTPS no seu site
- ✅ Não compartilhe sua API Key publicamente
- ✅ Use API Key de **Sandbox** para testes
- ✅ Use API Key de **Produção** apenas em ambiente real

## 🐛 Problemas Comuns

### Problema: "API Key inválida"
**Solução**: 
- Verifique se copiou a API Key completa
- Certifique-se de que está usando a chave do ambiente correto (Produção/Sandbox)
- Verifique se não há espaços antes ou depois da chave

### Problema: "Não consigo ver o campo API Key"
**Solução**:
- Certifique-se de que o plugin Asaas está instalado e ativo
- Verifique se está na página correta (WooCommerce > Configurações > Pagamentos)
- Tente limpar o cache do navegador

### Problema: "API Key não salva"
**Solução**:
- Verifique permissões do banco de dados
- Tente salvar novamente
- Verifique logs de erro do WordPress

## 📞 Suporte

Se tiver problemas:
1. Verifique a documentação do Asaas: https://docs.asaas.com/
2. Entre em contato com suporte do Asaas
3. Verifique logs do WooCommerce em **WooCommerce > Status > Logs**

---

**Última atualização**: 2025-11-15

