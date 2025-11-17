# Guia: Criar Páginas de Assinatura com Elementor

Este guia explica como criar páginas personalizadas usando Elementor para o sistema de assinaturas Vetraiz.

## Shortcodes Disponíveis

O plugin fornece 3 shortcodes principais:

1. **`[vetraiz_subscribe_form]`** - Formulário de assinatura
2. **`[vetraiz_my_subscription]`** - Minha assinatura (requer login)
3. **`[vetraiz_my_invoices]`** - Minhas faturas (requer login)

## Como Criar as Páginas

### 1. Página de Assinatura (`/conteudo-restrito`)

1. Vá em **Páginas > Adicionar Nova**
2. Dê o título: "Assinar" ou "Conteúdo Restrito"
3. Configure o slug como: `conteudo-restrito` ou `assinar`
4. Clique em **Editar com Elementor**
5. Adicione o widget **Shortcode**
6. No campo do shortcode, cole: `[vetraiz_subscribe_form]`
7. Publique a página

**Configuração no Plugin:**
- Vá em **Vetraiz Subscriptions > Configurações**
- Em "Página de Assinatura", selecione a página criada
- Salve as alterações

### 2. Página "Minha Assinatura" (`/minha-assinatura`)

**Opção A: Usar URL Customizada (Recomendado)**
- A URL `/minha-assinatura` já está configurada automaticamente
- Não precisa criar página, o sistema já renderiza o conteúdo

**Opção B: Criar Página no Elementor**
1. Vá em **Páginas > Adicionar Nova**
2. Título: "Minha Assinatura"
3. Slug: `minha-assinatura`
4. Edite com Elementor
5. Adicione widget **Shortcode**
6. Cole: `[vetraiz_my_subscription]`
7. Publique

### 3. Página "Minhas Faturas" (`/minhas-faturas`)

**Opção A: Usar URL Customizada (Recomendado)**
- A URL `/minhas-faturas` já está configurada automaticamente

**Opção B: Criar Página no Elementor**
1. Vá em **Páginas > Adicionar Nova**
2. Título: "Minhas Faturas"
3. Slug: `minhas-faturas`
4. Edite com Elementor
5. Adicione widget **Shortcode**
6. Cole: `[vetraiz_my_invoices]`
7. Publique

## Personalizando o Layout no Elementor

### Exemplo: Página de Assinatura Melhorada

1. **Seção Principal:**
   - Adicione uma seção com 2 colunas
   - Coluna esquerda: Informações do plano
   - Coluna direita: Widget Shortcode com `[vetraiz_subscribe_form]`

2. **Adicionar Elementos:**
   - Título: "Assine Agora"
   - Texto: Descrição do plano
   - Widget Shortcode: `[vetraiz_subscribe_form]`
   - Imagens, ícones, etc.

3. **Estilização:**
   - Use os widgets do Elementor para criar um layout profissional
   - O formulário já tem CSS próprio, mas você pode adicionar mais estilos

### Exemplo: Página "Minha Assinatura"

1. Adicione seções com:
   - Título: "Minha Assinatura"
   - Widget Shortcode: `[vetraiz_my_subscription]`
   - Botão: Link para "Ver Faturas"

## URLs Disponíveis

- `/conteudo-restrito` - Formulário de assinatura (ou use a página configurada)
- `/minha-assinatura` - Ver assinatura (URL customizada)
- `/minhas-faturas` - Ver faturas (URL customizada)
- `/fatura/{id}` - Detalhes da fatura (URL customizada)

## Dicas

1. **Proteção de Conteúdo:**
   - As páginas `/minha-assinatura` e `/minhas-faturas` já verificam se o usuário está logado
   - Se não estiver, redireciona para login

2. **Cache:**
   - Após criar/editar páginas, limpe o cache do WordPress/WP Rocket

3. **Permalinks:**
   - Se as URLs customizadas não funcionarem, vá em **Configurações > Links Permanentes** e clique em "Salvar"

4. **Teste:**
   - Sempre teste como usuário logado e não logado
   - Verifique se os redirecionamentos funcionam

## Exemplo de Estrutura no Elementor

```
Seção 1: Header
├── Título: "Assine Agora"
└── Subtítulo: "Acesso completo aos vídeos"

Seção 2: Conteúdo Principal (2 colunas)
├── Coluna 1:
│   ├── Ícone
│   ├── Título: "Benefícios"
│   └── Lista de benefícios
└── Coluna 2:
    └── Widget Shortcode: [vetraiz_subscribe_form]

Seção 3: Footer
└── Texto de apoio
```

## Suporte

Se precisar de ajuda, verifique:
- Os shortcodes estão registrados corretamente
- A página está publicada
- O usuário está logado (para páginas que requerem login)
- As URLs customizadas estão funcionando (verifique Permalinks)

