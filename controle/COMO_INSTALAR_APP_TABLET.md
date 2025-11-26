# Como Instalar o App no Tablet

## 📱 Passo a Passo SIMPLIFICADO

### 1. Fazer Upload dos Arquivos para o Servidor ✅

Via FTP ou webhook automático, os seguintes arquivos devem estar no servidor:

```
/public_html/controle/public/manifest.json
/public_html/controle/public/service-worker.js
/public_html/controle/public/testar-pwa.html
/public_html/controle/app/Views/layouts/avaliador.php
```

**✅ NÃO PRECISA MAIS criar ícones!** Os ícones agora são SVG inline (já incluídos no manifest.json)

### 2. Testar se PWA está funcionando

Antes de instalar no tablet, **teste primeiro no computador**:

1. Acesse: `https://saboresemmovimento.com.br/controle/testar-pwa.php`
2. Clique em "Executar Testes"
3. Verifique se todos os itens estão ✅ verdes
4. Se algo estiver ❌ vermelho, leia as soluções na página

### 3. Instalar no Tablet

## ⚠️ IMPORTANTE: Como fazer o app abrir em TELA CHEIA (não como página web)

### Android (Chrome/Edge) - PASSOS CORRETOS:

1. **NO TABLET**, abra o Chrome ou Edge
2. Acesse: `https://saboresemmovimento.com.br/controle/avaliador`
3. Faça login com usuário tipo "avaliador"
4. **AGUARDE 3-5 SEGUNDOS** (importante para o navegador detectar o PWA)
5. Toque no menu ⋮ (3 pontinhos no canto superior direito)
6. Procure por **"Instalar aplicativo"** ou **"Instalar Avaliador"**
   - ✅ Se aparecer "Instalar aplicativo" → CLIQUE AQUI (é o correto!)
   - ❌ Se aparecer só "Adicionar à tela inicial" → Veja solução abaixo
7. Confirme a instalação
8. **FECHE O NAVEGADOR COMPLETAMENTE**
9. Abra o app pelo ícone na tela inicial

### iPad/iPhone (iOS) - Safari:

1. Abra o **Safari** no iPad (não funciona em Chrome/Firefox no iOS)
2. Acesse: `https://saboresemmovimento.com.br/controle/avaliador`
3. Faça login
4. Toque no botão **Compartilhar** (ícone 📤 na parte inferior/superior)
5. Role para baixo e toque em **"Adicionar à Tela de Início"**
6. Confirme
7. **FECHE O SAFARI COMPLETAMENTE**
8. Abra o app pelo ícone na tela inicial

## 🔧 Solução: Se aparecer só "Adicionar à tela inicial" (Android)

Isso significa que o navegador NÃO detectou o PWA. Faça:

1. **Desinstale** qualquer versão anterior (pressione e segure o ícone → Remover)
2. No Chrome, vá em **Configurações** → **Privacidade** → **Limpar dados de navegação**
3. Marque: "Cookies" e "Imagens em cache"
4. Limpe
5. **Feche o Chrome COMPLETAMENTE** (não deixe em segundo plano)
6. Reabra o Chrome
7. Acesse: `https://saboresemmovimento.com.br/controle/testar-pwa.php`
8. Verifique se tudo está ✅ verde
9. Acesse: `https://saboresemmovimento.com.br/controle/avaliador`
10. **AGUARDE 5 SEGUNDOS**
11. Tente instalar novamente

### 5. Usar o App

Após instalado:
- O app abre em **tela cheia** (sem barra de navegação)
- Funciona **offline** para páginas já visitadas
- Fica na tela inicial como um app nativo
- Cor tema: rosa/vermelho (#f5576c)

### 6. Atalhos Disponíveis

Após instalado, ao **pressionar e segurar** o ícone do app, aparecem atalhos:
- 📋 Avaliar Cardápio
- 👩‍🍳 Avaliar Colaboradora

## 🔧 Solução de Problemas

### Opção "Instalar" não aparece?
- Certifique-se de estar usando HTTPS
- Limpe o cache do navegador
- Verifique se os ícones foram criados corretamente

### App não funciona offline?
- Acesse as páginas principais pelo menos uma vez com internet
- O Service Worker precisa ser registrado primeiro

### Desinstalar o app:
- **Android:** Pressione e segure o ícone → "Desinstalar" ou "Remover"
- **iOS:** Pressione e segure o ícone → "Remover do Início"

## 📝 URLs do Sistema

- **Login Avaliador:** https://saboresemmovimento.com.br/controle/login
- **Avaliar Cardápio:** https://saboresemmovimento.com.br/controle/avaliador/avaliar-cardapio
- **Avaliar Colaboradora:** https://saboresemmovimento.com.br/controle/avaliador/avaliar-colaboradora

## ✅ Checklist

- [ ] Criar ícones 192x192 e 512x512
- [ ] Colocar ícones em `/public/assets/icons/`
- [ ] Fazer upload de `manifest.json`
- [ ] Fazer upload de `service-worker.js`
- [ ] Fazer upload do `layouts/avaliador.php` atualizado
- [ ] Acessar URL no tablet
- [ ] Instalar app na tela inicial
- [ ] Testar funcionamento offline
