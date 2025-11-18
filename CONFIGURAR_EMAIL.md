# 📧 Configuração de Email - Sistema Sabores

## ✅ O que foi implementado:

Quando um **cliente** abre um chamado ou solicitação, o sistema **envia automaticamente** um email para:

📨 **contato@saboresemmovimento.com.br**

---

## 📋 Informações incluídas no email:

O email contém:
- ✅ Nome do cliente que abriu o chamado
- ✅ Empresa do cliente
- ✅ Email do cliente
- ✅ Título/Assunto do chamado
- ✅ Descrição completa
- ✅ Prioridade (se disponível)
- ✅ Data e hora de abertura
- ✅ Número do chamado
- ✅ **Link direto para visualizar o chamado no sistema**

---

## ⚙️ Configuração Necessária

### **PASSO 1: Criar conta de email no cPanel**

1. Acesse o **cPanel da Hostgator**
2. Vá em **"Contas de Email"**
3. Crie o email: **noreply@saboresemmovimento.com.br**
4. Anote a senha criada

### **PASSO 2: Configurar a senha no sistema**

1. Abra o arquivo `.env` em:
   ```
   /Applications/XAMPP/xamppfiles/htdocs/.env
   ```

2. Localize a linha:
   ```
   email.SMTPPass =
   ```

3. Adicione a senha do email:
   ```
   email.SMTPPass = SUA_SENHA_AQUI
   ```

4. Salve o arquivo

---

## 🔧 Configurações atuais do email:

```
Servidor SMTP: br404.hostgator.com.br
Porta: 587
Criptografia: TLS
Email remetente: noreply@saboresemmovimento.com.br
Email destinatário: contato@saboresemmovimento.com.br
```

---

## 🧪 Como testar:

1. Faça login como **cliente** no sistema
2. Acesse **"Chamados"** → **"Novo Chamado"**
3. Preencha o formulário e crie um chamado
4. Verifique se o email chegou em **contato@saboresemmovimento.com.br**

---

## 📊 Logs de email:

Os logs de envio de email são salvos em:

```
/Applications/XAMPP/xamppfiles/htdocs/writable/logs/
```

Se houver erro no envio, você verá mensagens como:
- `Email de novo chamado enviado com sucesso. ID: X`
- `Erro ao enviar email de novo chamado. ID: X`

---

## ⚠️ Problemas Comuns:

### Email não está sendo enviado?

**1. Verifique a senha no .env:**
   - Certifique-se que a senha está correta
   - Não deve ter espaços em branco

**2. Verifique o servidor SMTP:**
   - Hostgator pode bloquear SMTP se houver muitos emails
   - Entre em contato com suporte da Hostgator se necessário

**3. Verifique os logs:**
   ```bash
   tail -f /Applications/XAMPP/xamppfiles/htdocs/writable/logs/log-*.php
   ```

**4. Teste manualmente:**
   - Tente enviar um email de teste usando um cliente de email
   - Use as mesmas credenciais SMTP configuradas no .env

---

## 🔐 Segurança:

- ✅ O email `noreply@saboresemmovimento.com.br` é usado apenas para **envio**
- ✅ Não é necessário monitorar essa caixa de entrada
- ✅ As notificações chegam em `contato@saboresemmovimento.com.br`
- ✅ A senha fica protegida no arquivo `.env` (nunca comite no Git)

---

## 📝 Alterar email de destino:

Para mudar o email que recebe as notificações, edite no `.env`:

```
email.recipients = seu-novo-email@exemplo.com
```

Você pode adicionar múltiplos emails separados por vírgula:

```
email.recipients = email1@exemplo.com,email2@exemplo.com
```

---

## ✅ Pronto!

Após configurar a senha, o sistema enviará emails automaticamente sempre que um novo chamado for aberto! 🚀
