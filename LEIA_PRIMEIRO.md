# 🚀 INSTRUÇÕES DE INSTALAÇÃO - LEIA PRIMEIRO!

## ⚠️ IMPORTANTE: Seu Mac não tem PHP/Composer

A instalação deve ser feita **DIRETAMENTE NO SERVIDOR**.

---

## 📤 PASSO A PASSO COMPLETO

### 1️⃣ FAZER UPLOAD DOS ARQUIVOS

**Via cPanel (Gerenciador de Arquivos):**
1. Acesse seu cPanel da Hostgator
2. Abra "Gerenciador de Arquivos"
3. Navegue até `public_html/`
4. Crie uma pasta chamada `controle` (ou use outra pasta)
5. Entre na pasta `controle`
6. Clique em "Upload"
7. Selecione TODOS os arquivos desta pasta `controle/`
8. Aguarde o upload completar (pode levar alguns minutos)

**Via FTP (FileZilla):**
1. Conecte no seu servidor FTP
2. Navegue até `public_html/`
3. Arraste a pasta `controle/` completa
4. Aguarde a transferência

**Estrutura no servidor deve ficar:**
```
public_html/
└── controle/
    ├── app/
    ├── public/
    ├── writable/
    ├── .env
    ├── .htaccess
    ├── database.sql
    ├── verificar.php
    └── ... (outros arquivos)
```

---

### 2️⃣ ACESSAR VIA SSH

```bash
ssh seu_usuario@seu_dominio.com.br
# Digite sua senha do cPanel quando solicitado
```

**Não tem acesso SSH?**
- Entre em contato com suporte da Hostgator
- Ou use a opção "Terminal" no cPanel (se disponível)

---

### 3️⃣ INSTALAR CODEIGNITER 4

```bash
# Navegar até a pasta
cd public_html/controle

# Instalar CodeIgniter
composer install

# Se der erro, tente:
composer2 install
```

**Aguarde...** Isso pode levar 1-2 minutos.

---

### 4️⃣ CONFIGURAR PERMISSÕES

```bash
chmod 755 app/
chmod 755 public/
chmod 777 writable/
chmod 777 writable/cache/
chmod 777 writable/logs/
chmod 777 writable/session/
chmod 777 public/uploads/
mkdir -p public/uploads/chamados
chmod 777 public/uploads/chamados
```

---

### 5️⃣ IMPORTAR BANCO DE DADOS

**Opção A - Via phpMyAdmin (Recomendado):**

1. Acesse: cPanel → phpMyAdmin
2. Clique no banco `guil5541_sabores` (à esquerda)
3. Clique na aba "Importar" (no topo)
4. Clique em "Escolher arquivo"
5. Selecione o arquivo `database.sql`
6. Role para baixo e clique em "Executar"
7. Aguarde a mensagem "Importação concluída com êxito"

**Opção B - Via SSH:**

```bash
mysql -h br404.hostgator.com.br -u guil5541_sabores -p guil5541_sabores < database.sql
# Senha quando solicitado: Sm2025.#
```

---

### 6️⃣ CONFIGURAR URL BASE

Edite o arquivo `.env` e coloque a URL correta:

```env
app.baseURL = 'http://seu-dominio.com.br/'
```

Se o sistema estiver em uma subpasta:
```env
app.baseURL = 'http://seu-dominio.com.br/controle/'
```

---

### 7️⃣ VERIFICAR INSTALAÇÃO

Acesse no navegador:
```
http://seu-dominio.com.br/controle/verificar.php
```

Este script vai verificar:
- ✓ Versão do PHP
- ✓ Extensões necessárias
- ✓ Pastas e permissões
- ✓ CodeIgniter instalado
- ✓ Conexão com banco de dados
- ✓ Tabelas criadas

**Tudo OK?** Avance para o próximo passo!

**Algo errado?** O script mostrará o que precisa ser corrigido.

---

### 8️⃣ ACESSAR O SISTEMA

Acesse:
```
http://seu-dominio.com.br/controle/
```

Você verá a tela de **LOGIN**! 🎉

**Usuários de teste:**

| Tipo | Email | Senha |
|------|-------|-------|
| **Administrador** | admin@sabores.com.br | admin123 |
| **Atendente** | atendente@sabores.com.br | atendente123 |
| **Cliente** | cliente@empresa.com.br | cliente123 |

---

## 📋 CHECKLIST DE INSTALAÇÃO

- [ ] Upload de todos os arquivos feito
- [ ] Conectado via SSH
- [ ] Executado `composer install`
- [ ] Permissões configuradas (chmod)
- [ ] Banco de dados importado (database.sql)
- [ ] URL configurada no .env
- [ ] Acessado verificar.php - tudo OK
- [ ] Sistema abre a tela de login
- [ ] Consegui fazer login com usuário de teste

---

## 🐛 PROBLEMAS COMUNS

### Erro 500 - Internal Server Error
```bash
# Verificar permissões
chmod 777 writable/

# Ver o erro específico nos logs
tail -f writable/logs/log-*.php
```

### Erro "Class not found"
```bash
# Reinstalar CodeIgniter
composer install
# ou
composer2 install
```

### Página em branco
```bash
# Ativar exibição de erros
# Edite .env e mude:
CI_ENVIRONMENT = development
```

### Erro de banco de dados
- Verifique se importou o `database.sql`
- Teste conexão no phpMyAdmin
- Verifique credenciais no `.env`

### CSS/JS não carregam
- Verifique se a URL no `.env` está correta
- Verifique se o `.htaccess` existe em `public/`

---

## 📞 SUPORTE

**Logs do Sistema:**
```
writable/logs/log-YYYY-MM-DD.php
```

**Documentação Completa:**
- README.md - Documentação do sistema
- INSTALL.md - Guia de instalação detalhado

**Precisa de Ajuda?**
- Verifique os logs em `writable/logs/`
- Use o Console do navegador (F12) para erros JavaScript
- Entre em contato com o desenvolvedor

---

## ✅ PRONTO!

Após seguir todos os passos, você terá um sistema completo de gestão de chamados funcionando! 🚀

**Próximos passos após instalar:**
1. Criar novos usuários clientes
2. Cadastrar empresas
3. Testar abertura de chamados
4. Personalizar categorias
5. Treinar a equipe

---

**Sistema desenvolvido com CodeIgniter 4 + Bootstrap 5**

**Sabores Refeitório © 2025**
