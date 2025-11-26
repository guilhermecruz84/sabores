# Como Instalar o App no Tablet

## 📱 Passo a Passo para Instalação

### 1. Criar os Ícones do App

Você precisa criar 2 ícones PNG e colocá-los na pasta `public/assets/icons/`:

- `icon-192x192.png` (192x192 pixels)
- `icon-512x512.png` (512x512 pixels)

**Opções para criar:**

**A) Usar um gerador online:**
1. Acesse: https://www.favicon-generator.org/
2. Faça upload do logo da empresa
3. Baixe os ícones gerados
4. Renomeie para os tamanhos corretos

**B) Usar Photoshop/GIMP:**
1. Abra o logo da empresa
2. Redimensione para 192x192 e 512x512
3. Salve como PNG
4. Coloque em `/public/assets/icons/`

**C) Criar via terminal (se tiver ImageMagick):**
```bash
# No diretório do projeto
cd /Applications/XAMPP/xamppfiles/htdocs/controle/public
mkdir -p assets/icons

# Criar ícone 192x192 (substitua 'logo.png' pelo seu logo)
convert logo.png -resize 192x192 assets/icons/icon-192x192.png

# Criar ícone 512x512
convert logo.png -resize 512x512 assets/icons/icon-512x512.png
```

### 2. Fazer Upload dos Arquivos para o Servidor

Via FTP, faça upload dos seguintes arquivos para o servidor:

```
/public_html/controle/public/manifest.json
/public_html/controle/public/service-worker.js
/public_html/controle/public/assets/icons/icon-192x192.png
/public_html/controle/public/assets/icons/icon-512x512.png
/public_html/controle/app/Views/layouts/avaliador.php
```

### 3. Instalar no Tablet (Android)

#### Chrome/Edge:
1. Abra o navegador no tablet
2. Acesse: `https://saboresemmovimento.com.br/controle/avaliador`
3. Faça login com usuário tipo "avaliador"
4. Toque no menu (3 pontinhos) ⋮
5. Selecione **"Adicionar à tela inicial"** ou **"Instalar aplicativo"**
6. Confirme a instalação
7. O ícone aparecerá na tela inicial

#### Firefox:
1. Abra o Firefox no tablet
2. Acesse: `https://saboresemmovimento.com.br/controle/avaliador`
3. Toque no menu (3 pontinhos) ⋮
4. Selecione **"Adicionar à tela inicial"**
5. Confirme

### 4. Instalar no iPad/iPhone (iOS)

#### Safari:
1. Abra o Safari no iPad
2. Acesse: `https://saboresemmovimento.com.br/controle/avaliador`
3. Faça login
4. Toque no botão **Compartilhar** (quadrado com seta para cima)
5. Role para baixo e toque em **"Adicionar à Tela de Início"**
6. Ajuste o nome se desejar
7. Toque em **"Adicionar"**
8. O ícone aparecerá na tela inicial

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
