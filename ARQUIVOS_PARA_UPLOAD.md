# 📤 Arquivos para Upload no Servidor

## ✅ Arquivos Modificados Recentemente (Últimos 7 dias)

### 🔧 Controllers (app/Controllers/)
```
app/Controllers/Chamados.php
app/Controllers/AvaliacaoColaboradoraCliente.php
```

### 📊 Models (app/Models/)
```
app/Models/AvaliacaoColaboradoraClienteModel.php
```

### 🎨 Views (app/Views/)
```
app/Views/dashboard/index.php
app/Views/auth/login.php
app/Views/layouts/main.php

# Emails
app/Views/emails/novo_chamado.php

# Avaliação Colaboradora
app/Views/avaliacao_colaboradora_cliente/avaliar.php
app/Views/avaliacao_colaboradora_cliente/dashboard.php
app/Views/avaliacao_colaboradora_cliente/historico.php
app/Views/avaliacao_colaboradora_cliente/obrigado.php
```

### ⚙️ Configurações (app/Config/)
```
app/Config/Routes.php
```

### 🗄️ SQL (Executar no banco de dados)
```
public/install_avaliacao_colaboradora_cliente.sql
```

### 📄 Documentação (Opcional - apenas para referência)
```
CONFIGURAR_EMAIL.md
```

---

## 🚫 Arquivos que NÃO devem ser enviados:

```
.env                          (Cada ambiente tem o seu próprio)
writable/logs/*               (Logs são gerados no servidor)
writable/cache/*              (Cache é gerado no servidor)
writable/session/*            (Sessões são geradas no servidor)
public/test-dashboard.php     (Arquivo de teste local)
```

---

## ⚠️ Arquivo .env - Configuração Manual no Servidor

**NÃO faça upload do .env local!** Em vez disso, edite o .env diretamente no servidor e adicione:

```ini
# Email Configuration
email.fromEmail = noreply@saboresemmovimento.com.br
email.fromName = Sistema Sabores
email.recipients = contato@saboresemmovimento.com.br

# SMTP Configuration (Hostgator)
email.SMTPHost = br404.hostgator.com.br
email.SMTPUser = noreply@saboresemmovimento.com.br
email.SMTPPass = [SENHA_DO_EMAIL_AQUI]
email.SMTPPort = 587
email.SMTPCrypto = tls
email.protocol = smtp
email.mailType = html
email.charset = utf-8
email.newline = \r\n
```

---

## 📝 Passos para Deploy:

### 1️⃣ Fazer Backup do Servidor
```bash
# No servidor, faça backup antes de atualizar
cp -r /caminho/servidor backup_$(date +%Y%m%d_%H%M%S)
```

### 2️⃣ Upload dos Arquivos
Use FTP/SFTP para fazer upload dos arquivos listados acima, mantendo a estrutura de pastas.

### 3️⃣ Executar SQL (Apenas se ainda não foi executado)
```sql
-- Conecte no MySQL do servidor e execute:
SOURCE /caminho/public/install_avaliacao_colaboradora_cliente.sql;
```

OU pelo PHPMyAdmin:
- Abra o arquivo `install_avaliacao_colaboradora_cliente.sql`
- Copie e cole o conteúdo na aba SQL
- Execute

### 4️⃣ Configurar .env no Servidor
Edite o arquivo `.env` no servidor e adicione as configurações de email (veja seção acima).

### 5️⃣ Criar Conta de Email no cPanel
1. Acesse cPanel da Hostgator
2. Vá em "Contas de Email"
3. Crie: **noreply@saboresemmovimento.com.br**
4. Anote a senha
5. Adicione a senha no `.env` do servidor

### 6️⃣ Limpar Cache do Servidor
```bash
# Via SSH ou crie um arquivo PHP temporário:
rm -rf writable/cache/*
rm -rf writable/session/*
```

OU crie um arquivo `limpar-cache.php`:
```php
<?php
exec('rm -rf writable/cache/*');
exec('rm -rf writable/session/*');
echo "Cache limpo!";
?>
```

### 7️⃣ Testar
- ✅ Login funciona
- ✅ Dashboard carrega sem erros
- ✅ Criar novo chamado (cliente)
- ✅ Verificar se email chegou em contato@saboresemmovimento.com.br
- ✅ Acessar Avaliação Colaboradora (menu)
- ✅ Fazer uma avaliação mensal

---

## 🔍 Verificar Permissões no Servidor

Certifique-se que as pastas writable/ têm permissão 775 ou 777:

```bash
chmod -R 775 writable/
chmod -R 775 writable/cache
chmod -R 775 writable/logs
chmod -R 775 writable/session
```

---

## 📊 Resumo das Novidades:

### ✨ Funcionalidades Adicionadas:
1. **Sistema de Email** - Notificações automáticas quando cliente abre chamado
2. **Avaliação Colaboradora Mensal** - Clientes podem avaliar colaboradora 1x por mês
3. **Correções no Dashboard** - Proteção contra erros de arrays undefined
4. **Remoção do Link de Cadastro** - Removido da página de login

### 🔧 Correções Técnicas:
- Proteção contra undefined array keys no dashboard
- Operador null coalescing (??) adicionado em várias views
- Menu atualizado com link para Avaliação Colaboradora

---

## 📞 Suporte

Se houver qualquer erro após o deploy:
1. Verifique os logs: `writable/logs/log-YYYY-MM-DD.php`
2. Verifique se o .env está configurado corretamente
3. Verifique se a tabela `avaliacao_colaboradora_cliente` foi criada
4. Teste o envio de email criando um chamado

---

**Data de geração:** 14/11/2025
**Ambiente local:** /Applications/XAMPP/xamppfiles/htdocs/
**Servidor:** www.saboresemmovimento.com.br/controle/
