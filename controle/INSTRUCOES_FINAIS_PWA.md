# ⚠️ INSTRUÇÕES FINAIS - Instalação do PWA

## Problema Identificado

O CodeIgniter está interceptando **todas** as requisições, mesmo com regras no .htaccess.
Os ícones precisam estar **FORA** da pasta `/controle/` para serem acessados diretamente.

## ✅ SOLUÇÃO DEFINITIVA

### **Passo 1: Fazer Upload via FTP**

Faça upload dos seguintes arquivos para o servidor:

```
📁 Origem (seu computador):
/Applications/XAMPP/xamppfiles/htdocs/controle/public/icon-192.png
/Applications/XAMPP/xamppfiles/htdocs/controle/public/icon-512.png

📁 Destino (servidor FTP):
/public_html/icon-192.png
/public_html/icon-512.png
```

**IMPORTANTE:** Os ícones devem estar na **RAIZ** (`/public_html/`), **NÃO** dentro de `/public_html/controle/`!

### **Passo 2: Testar se os Ícones Estão Acessíveis**

Abra no navegador:

```
https://saboresemmovimento.com.br/icon-192.png
https://saboresemmovimento.com.br/icon-512.png
```

✅ **Deve aparecer** um ícone rosa/roxo com estrela branca
❌ **Se der erro 404**, os arquivos não foram colocados no lugar certo

### **Passo 3: Verificar o Manifest**

Abra no navegador:

```
https://saboresemmovimento.com.br/controle/manifest.json
```

Deve aparecer o JSON do manifest apontando para `/icon-192.png` e `/icon-512.png`

### **Passo 4: Instalar o App no Tablet**

**DESINSTALE qualquer versão anterior:**
1. Pressione e segure o ícone do app
2. Toque em "Remover" ou "Desinstalar"

**LIMPE O CACHE:**
1. Chrome → Menu ⋮ → Configurações
2. Privacidade → Limpar dados de navegação
3. Marque: "Cookies" e "Imagens em cache"
4. Clique em "Limpar"

**FECHE O CHROME COMPLETAMENTE** (não deixe em segundo plano)

**INSTALE:**
1. Abra o Chrome novamente
2. Acesse: `https://saboresemmovimento.com.br/controle/avaliador`
3. Faça login com usuário "avaliador"
4. **AGUARDE 5 SEGUNDOS**
5. Toque no menu ⋮ (3 pontinhos)
6. Deve aparecer **"Instalar aplicativo"** ou **"Instalar Avaliador"**
7. Confirme a instalação
8. **FECHE O CHROME**
9. Abra pelo ícone na tela inicial

## 🎯 Como Saber se Funcionou?

**✅ CORRETO** - App abre em TELA CHEIA (sem barra de navegação do Chrome)
**❌ ERRADO** - App abre como página web (com barra de endereço)

## 📂 Estrutura Final de Arquivos no Servidor

```
/public_html/
├── icon-192.png          ← Ícone 192x192 (RAIZ!)
├── icon-512.png          ← Ícone 512x512 (RAIZ!)
│
└── controle/
    ├── .htaccess
    ├── index.php
    │
    └── public/
        ├── manifest.json     ← Aponta para /icon-192.png
        ├── service-worker.js
        ├── icon-192.png      ← Cópia (não usado)
        └── icon-512.png      ← Cópia (não usado)
```

## 🔧 Se AINDA Não Funcionar

Se após seguir todos os passos o Chrome continuar mostrando apenas "Adicionar à tela inicial" ao invés de "Instalar aplicativo", me envie:

1. Print da página: `https://saboresemmovimento.com.br/icon-192.png`
2. Print do console do Chrome (F12 → Console) quando estiver em `/controle/avaliador`
3. Qual dispositivo está usando (modelo do tablet)

## 📝 Arquivos que Foram Atualizados

- ✅ `public/manifest.json` - Ícones apontam para `/icon-192.png` (raiz)
- ✅ `.htaccess` - Regras de redirecionamento
- ✅ `public/icon-192.png` - Ícone gerado (copiar para raiz)
- ✅ `public/icon-512.png` - Ícone gerado (copiar para raiz)

## 🚀 Próximos Passos

Após a instalação funcionar, o app:
- Abre em tela cheia (standalone)
- Funciona offline (após primeira visita)
- Tem ícone personalizado na tela inicial
- Não mostra barra de navegação
