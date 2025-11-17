# Vetraiz Subscriptions

Sistema de assinaturas customizado com controle de acesso a vídeos, totalmente separado do WooCommerce.

## Funcionalidades

- ✅ Sistema de assinaturas mensais
- ✅ Integração com Asaas (PIX)
- ✅ Formulário de assinatura customizado
- ✅ Visualização de faturas
- ✅ Controle de acesso a vídeos
- ✅ Webhook para atualização automática de status
- ✅ Painel administrativo

## Instalação

1. Ative o plugin no WordPress
2. Vá em **Assinaturas > Configurações**
3. Configure:
   - API Key do Asaas
   - Nome e valor do plano
   - URL do webhook: `https://seusite.com/vetraiz-webhook`

## Uso

### Shortcodes

**Formulário de Assinatura:**
```
[vetraiz_subscribe_form]
```

**Minha Assinatura:**
```
[vetraiz_my_subscription]
```

**Minhas Faturas:**
```
[vetraiz_my_invoices]
```

### Proteger Vídeos

Use o shortcode para proteger conteúdo de vídeo:
```
[vetraiz_video url="https://exemplo.com/video.mp4" title="Conteúdo exclusivo para assinantes"]
```

### URLs Customizadas

- `/minha-assinatura` - Visualizar assinatura
- `/minhas-faturas` - Lista de faturas
- `/fatura/{id}` - Detalhes da fatura

## Estrutura

```
vetraiz-subscriptions/
├── includes/
│   ├── class-database.php          # Gerenciamento de banco de dados
│   ├── class-asaas-api.php         # Integração com API Asaas
│   ├── class-subscription.php       # Gerenciamento de assinaturas
│   ├── class-payment.php            # Gerenciamento de pagamentos
│   ├── class-access-control.php     # Controle de acesso
│   ├── class-admin.php              # Painel administrativo
│   ├── class-frontend.php           # Páginas frontend
│   ├── class-webhook.php            # Processamento de webhooks
│   └── ajax-handlers.php            # Handlers AJAX
├── templates/
│   ├── subscribe-form.php          # Formulário de assinatura
│   ├── my-subscription.php          # Minha assinatura
│   ├── my-invoices.php              # Lista de faturas
│   ├── page-invoice.php             # Detalhes da fatura
│   └── admin/
│       ├── settings.php             # Configurações
│       └── subscriptions-list.php   # Lista de assinaturas
├── assets/
│   ├── css/
│   │   └── frontend.css             # Estilos frontend
│   └── js/
│       └── frontend.js              # JavaScript frontend
└── vetraiz-subscriptions.php        # Arquivo principal
```

## Banco de Dados

O plugin cria duas tabelas:

1. `wp_vetraiz_subscriptions` - Assinaturas
2. `wp_vetraiz_subscription_payments` - Pagamentos/Faturas

## Webhook

Configure no Asaas:
- URL: `https://seusite.com/vetraiz-webhook`
- Eventos: PAYMENT_RECEIVED, PAYMENT_CREATED, PAYMENT_OVERDUE

## Controle de Acesso

O plugin verifica automaticamente se o usuário tem assinatura ativa antes de exibir vídeos protegidos.

