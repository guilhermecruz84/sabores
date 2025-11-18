# 📱 PWA - Avaliador Sabores

## O que foi criado?

Um **Progressive Web App (PWA)** para o módulo de Avaliador, que transforma o sistema web em um app instalável que funciona em **tela cheia** no tablet, sem barra de navegador.

---

## ✅ Arquivos Criados

### 1. Arquivos Principais do PWA

```
/public/
├── manifest.json           # Configuração do PWA (nome, ícones, modo fullscreen)
├── sw.js                   # Service Worker (cache e funcionamento offline)
├── gerar-icones.html       # Página para gerar os ícones do app
├── instalar-pwa.html       # Instruções de instalação (Android e iOS)
└── icons/                  # Pasta para os ícones (você precisa gerá-los)
    └── .gitkeep
```

### 2. Arquivo Modificado

```
/app/Views/layouts/avaliador.php
```
- Adicionadas meta tags PWA
- Adicionados links para manifest e ícones
- Adicionado CSS para tela cheia e safe areas
- Adicionado JavaScript para registrar Service Worker
- Suporte completo para Android e iOS

---

## 🚀 Como Configurar (Passo a Passo)

### **PASSO 1: Gerar os Ícones**

1. Acesse no navegador:
   ```
   http://seu-servidor/gerar-icones.html
   ```

2. Você verá uma grade com 8 ícones de tamanhos diferentes

3. **Clique em "Baixar"** em cada um dos 8 ícones

4. Salve todos os arquivos na pasta:
   ```
   /Applications/XAMPP/xamppfiles/htdocs/public/icons/
   ```

5. Os arquivos devem ter exatamente estes nomes:
   - `icon-72x72.png`
   - `icon-96x96.png`
   - `icon-128x128.png`
   - `icon-144x144.png`
   - `icon-152x152.png`
   - `icon-192x192.png`
   - `icon-384x384.png`
   - `icon-512x512.png`

### **PASSO 2: Verificar a Instalação**

1. Certifique-se que o Apache está rodando:
   ```bash
   /Applications/XAMPP/xamppfiles/bin/apachectl status
   ```

2. Acesse no navegador do seu computador:
   ```
   http://localhost/avaliador
   ```

3. Abra as **Ferramentas do Desenvolvedor** (F12)

4. Vá na aba **Console** e verifique se aparece:
   ```
   Service Worker registrado com sucesso
   ```

5. Vá na aba **Application** > **Manifest** e veja se carrega sem erros

### **PASSO 3: Instalar no Tablet**

#### 📱 Para Android (Chrome):

1. No tablet, abra o **Google Chrome**

2. Acesse (substitua pelo IP do seu servidor):
   ```
   http://192.168.X.X/avaliador
   ```

3. Faça login com usuário do tipo "avaliador"

4. Toque nos **3 pontinhos** no canto superior direito

5. Selecione **"Instalar app"** ou **"Adicionar à tela inicial"**

6. Confirme a instalação

7. O ícone aparecerá na tela inicial do tablet

#### 🍎 Para iOS/iPadOS (Safari):

1. No iPad, abra o **Safari** (não funciona no Chrome!)

2. Acesse:
   ```
   http://192.168.X.X/avaliador
   ```

3. Faça login com usuário do tipo "avaliador"

4. Toque no ícone de **Compartilhar** (quadrado com seta)

5. Selecione **"Adicionar à Tela de Início"**

6. Confirme tocando em **"Adicionar"**

7. O ícone aparecerá na tela inicial do iPad

---

## 🎨 Características do PWA

### ✓ Modo Fullscreen
- Abre sem barra de navegador
- Usa toda a tela do tablet
- Parece um app nativo

### ✓ Funcionamento Offline
- Após primeira instalação, funciona sem internet
- Service Worker cacheia páginas e recursos
- Sincroniza dados quando voltar online

### ✓ Ícone Personalizado
- Ícone roxo com estrela branca
- Aparece na tela inicial
- Aparece na lista de apps recentes

### ✓ Safe Areas (iOS)
- Respeita notch e áreas seguras do iPad
- Layout adapta automaticamente
- Botões acessíveis em tablets modernos

### ✓ Otimizações Mobile
- Zoom desabilitado
- Duplo toque otimizado
- Sem highlight ao tocar
- Seleção de texto desabilitada (em modo app)

---

## 📋 Como Verificar se Está Funcionando

### O app está correto se:

1. ✅ Abre em **tela cheia** (sem barra de URL)
2. ✅ Tem um **ícone roxo** na tela inicial
3. ✅ Aparece nos **apps recentes** do tablet
4. ✅ Console mostra: "Service Worker registrado com sucesso"
5. ✅ Funciona **offline** após primeira carga

---

## 🔧 Resolução de Problemas

### "Não aparece opção de instalar"

**Causas possíveis:**
- Navegador errado (use Chrome no Android, Safari no iOS)
- Ícones não foram gerados
- Service Worker não registrou
- Acessando via localhost de outro dispositivo

**Solução:**
1. Gere todos os ícones via `/gerar-icones.html`
2. Use o IP da rede local (não "localhost")
3. Limpe o cache do navegador
4. Verifique o console por erros

### "Ícones não aparecem / Erro 404"

**Causa:** Ícones não foram gerados ou salvos na pasta correta

**Solução:**
1. Acesse `/gerar-icones.html`
2. Baixe TODOS os 8 ícones
3. Salve em `/public/icons/` com os nomes exatos
4. Recarregue a página

### "Não funciona offline"

**Android:**
- Aguarde alguns segundos após instalação
- Abra o app pelo menos uma vez online
- Verifique se Service Worker está ativo (F12 > Application)

**iOS:**
- Funcionalidade offline limitada no Safari
- Cache funciona, mas sincronização é restrita
- Necessita conexão para login

### "App abre com barra de navegador"

**Causa:** Não foi instalado corretamente, está abrindo como aba do navegador

**Solução:**
1. Desinstale o app (segure o ícone > Remover)
2. Limpe o cache do navegador
3. Reinstale seguindo os passos acima

---

## 📱 Rotas do Sistema

### Rotas Públicas:
- `/login` - Tela de login
- `/gerar-icones.html` - Gerador de ícones
- `/instalar-pwa.html` - Instruções de instalação
- `/manifest.json` - Manifest do PWA
- `/sw.js` - Service Worker

### Rotas do Avaliador (requer autenticação):
- `/avaliador` - Dashboard
- `/avaliador/avaliar-cardapio` - Pesquisa do cardápio
- `/avaliador/avaliar-colaboradora` - Pesquisa da colaboradora
- `/avaliador/obrigado` - Tela de agradecimento

---

## 🎯 Próximos Passos

### 1. Gerar os Ícones
```bash
# Acesse e baixe todos:
http://seu-servidor/gerar-icones.html
```

### 2. Testar no Desktop
```bash
# Abra e teste:
http://localhost/avaliador
```

### 3. Instalar no Tablet
```bash
# Acesse do tablet:
http://IP-do-servidor/avaliador
```

### 4. Compartilhar Instruções
```bash
# Envie este link para os usuários:
http://seu-servidor/instalar-pwa.html
```

---

## 📊 Compatibilidade

| Plataforma | Navegador | Fullscreen | Offline | Ícones |
|------------|-----------|------------|---------|--------|
| Android 5+ | Chrome    | ✅ Sim     | ✅ Sim  | ✅ Sim |
| iOS 13+    | Safari    | ✅ Sim     | ⚠️ Limitado | ✅ Sim |
| Android    | Firefox   | ✅ Sim     | ✅ Sim  | ✅ Sim |
| iOS        | Chrome    | ❌ Não*    | ❌ Não  | ❌ Não |

\* Chrome no iOS não suporta PWA (usa WebKit do Safari)

---

## 🛠️ Configurações Avançadas

### Alterar Cor do Tema

Edite `/public/manifest.json`:
```json
"theme_color": "#6f42c1",  // Cor da barra superior
"background_color": "#6f42c1"  // Cor do splash screen
```

### Mudar Nome do App

Edite `/public/manifest.json`:
```json
"name": "Avaliador Sabores",  // Nome completo
"short_name": "Avaliador"  // Nome curto (ícone)
```

### Configurar Orientação

Edite `/public/manifest.json`:
```json
"orientation": "portrait"  // Opções: portrait, landscape, any
```

### Modo de Exibição

Edite `/public/manifest.json`:
```json
"display": "fullscreen"  // Opções: fullscreen, standalone, minimal-ui, browser
```

---

## 📞 Suporte

Se tiver problemas:

1. Verifique os logs do console (F12)
2. Teste primeiro no desktop
3. Certifique-se que todos os ícones foram gerados
4. Leia as instruções em `/instalar-pwa.html`

---

## ✨ Pronto!

Agora você tem um PWA totalmente funcional que:
- ✅ Funciona em tela cheia
- ✅ Pode ser instalado no tablet
- ✅ Funciona offline
- ✅ Parece um app nativo

**Próximo passo:** Gere os ícones e teste no tablet! 🚀
