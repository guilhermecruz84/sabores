# 🚀 Guia Rápido de Instalação

## Passo 1: Instalar CodeIgniter 4

### Opção A: Via Composer (Recomendado)

```bash
cd /Users/guilhermecruz/Documents/Sabores/controle
composer install
```

### Opção B: Download Manual

Se não tiver o Composer:
1. Baixe o CodeIgniter 4 em: https://codeigniter.com/download
2. Extraia e copie a pasta `vendor` para o diretório `controle/`

## Passo 2: Importar Banco de Dados

### Via phpMyAdmin:
1. Acesse phpMyAdmin no cPanel
2. Selecione o banco `guil5541_sabores`
3. Clique em "Importar"
4. Selecione o arquivo `database.sql`
5. Clique em "Executar"

### Via MySQL Command Line:
```bash
mysql -h br404.hostgator.com.br -u guil5541_sabores -p guil5541_sabores < database.sql
# Senha: Sm2025.#
```

## Passo 3: Configurar Permissões

```bash
chmod 755 app/
chmod 755 public/
chmod 777 public/uploads/
chmod 777 writable/
chmod 777 writable/cache/
chmod 777 writable/logs/
chmod 777 writable/session/
```

## Passo 4: Configurar URL Base

Edite o arquivo `.env`:

```env
app.baseURL = 'http://seu-dominio.com.br/'
```

Se estiver em uma subpasta:
```env
app.baseURL = 'http://seu-dominio.com.br/controle/'
```

## Passo 5: Testar Instalação

Acesse no navegador:
```
http://seu-dominio.com.br/
```

Você deverá ver a tela de login.

## Passo 6: Fazer Login

Use uma das contas de teste:

**Admin:**
- Email: admin@sabores.com.br
- Senha: admin123

**Cliente:**
- Email: cliente@empresa.com.br
- Senha: cliente123

## ✅ Checklist de Verificação

- [ ] CodeIgniter 4 instalado (pasta vendor existe)
- [ ] Banco de dados importado com sucesso
- [ ] Arquivo .env configurado com URL correta
- [ ] Permissões configuradas corretamente
- [ ] Pasta uploads criada: `public/uploads/chamados`
- [ ] Consegue acessar a tela de login
- [ ] Consegue fazer login com usuário de teste
- [ ] Dashboard carrega sem erros

## 🐛 Problemas Comuns

### Erro 500 - Internal Server Error
- Verifique as permissões das pastas
- Verifique se o arquivo .htaccess existe em `public/`
- Ative display_errors no .env para ver o erro específico

### Erro "Class 'CodeIgniter\...' not found"
- Execute `composer install` para instalar dependências
- Ou baixe o CodeIgniter 4 manualmente

### Erro de conexão com banco
- Verifique as credenciais em `.env`
- Teste a conexão ao banco via phpMyAdmin

### Página em branco
- Verifique os logs em `writable/logs/`
- Certifique-se que a pasta writable tem permissão 777

### Upload não funciona
- Crie a pasta: `mkdir -p public/uploads/chamados`
- Dê permissão: `chmod 777 public/uploads/chamados`

## 📞 Precisa de Ajuda?

Se encontrar problemas, verifique:
1. Logs do sistema em `writable/logs/`
2. Logs do PHP no servidor
3. Console do navegador (F12) para erros JavaScript

---

**Após a instalação, leia o README.md para mais informações sobre o sistema!**
