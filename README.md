# Sistema de Gestão de Chamados - Sabores Refeitório

Sistema completo de gestão de ocorrências e solicitações para empresa de refeitório, desenvolvido com CodeIgniter 4, Bootstrap 5 e MySQL.

## 🎯 Sobre o Sistema

Portal de atendimento que permite aos clientes abrirem ocorrências (reclamações/problemas) e solicitações (pedidos/informações) relacionadas aos serviços de refeitório. A equipe interna pode responder e acompanhar cada chamado até sua finalização pelo cliente.

## ✨ Funcionalidades

### Para Clientes:
- ✅ Abrir ocorrências e solicitações
- ✅ Acompanhar status dos chamados
- ✅ Enviar e receber mensagens
- ✅ Anexar fotos e documentos
- ✅ Finalizar chamados
- ✅ Avaliar atendimento (1-5 estrelas)
- ✅ Dashboard com estatísticas pessoais

### Para Atendentes:
- ✅ Visualizar todos os chamados
- ✅ Responder chamados
- ✅ Criar notas internas
- ✅ Atribuir chamados
- ✅ Dashboard com métricas gerais

### Para Administradores:
- ✅ Todas as funções de atendente
- ✅ Gerenciar empresas clientes
- ✅ Gerenciar usuários
- ✅ Relatórios completos
- ✅ Configurações do sistema

## 🛠 Tecnologias Utilizadas

- **Backend:** PHP 8+ com CodeIgniter 4
- **Frontend:** Bootstrap 5, jQuery, FontAwesome
- **Banco de Dados:** MySQL 5.7+
- **Componentes:** DataTables, Chart.js
- **Arquitetura:** MVC (Model-View-Controller)

## 📋 Requisitos

- PHP 8.0 ou superior
- MySQL 5.7 ou superior
- Servidor web (Apache/Nginx)
- Extensões PHP: mysqli, intl, mbstring, json
- Composer (para instalação do CodeIgniter 4)

## 🚀 Instalação

### 1. Configurar Banco de Dados

Execute o arquivo `database.sql` no seu MySQL:

```bash
mysql -u guil5541_sabores -p guil5541_sabores < database.sql
```

Ou importe via phpMyAdmin:
1. Acesse phpMyAdmin
2. Selecione o banco `guil5541_sabores`
3. Vá em "Importar"
4. Selecione o arquivo `database.sql`
5. Clique em "Executar"

### 2. Configurar Conexão com Banco

O arquivo `.env` já está configurado com as credenciais do banco:

```env
database.default.hostname = br404.hostgator.com.br
database.default.database = guil5541_sabores
database.default.username = guil5541_sabores
database.default.password = Sm2025.#
```

### 3. Configurar Permissões

Certifique-se de que as pastas tenham permissões corretas:

```bash
chmod 755 app/
chmod 755 public/
chmod 777 public/uploads/
chmod 777 writable/
```

### 4. Configurar URL Base

Edite o arquivo `.env` e altere a URL base:

```env
app.baseURL = 'http://seu-dominio.com.br/'
```

### 5. Criar Diretório de Uploads

```bash
mkdir -p public/uploads/chamados
chmod 777 public/uploads/chamados
```

## 👥 Usuários de Teste

O sistema vem com 3 usuários pré-cadastrados para teste:

### Administrador
- **Email:** admin@sabores.com.br
- **Senha:** admin123
- **Permissões:** Acesso total ao sistema

### Atendente
- **Email:** atendente@sabores.com.br
- **Senha:** atendente123
- **Permissões:** Gerenciar chamados e usuários

### Cliente
- **Email:** cliente@empresa.com.br
- **Senha:** cliente123
- **Permissões:** Criar e acompanhar próprios chamados

## 📁 Estrutura do Projeto

```
controle/
├── app/
│   ├── Config/
│   │   ├── Database.php       # Configuração do banco
│   │   ├── Routes.php         # Rotas do sistema
│   │   └── Filters.php        # Filtros de autenticação
│   ├── Controllers/
│   │   ├── Auth.php           # Autenticação
│   │   ├── Dashboard.php      # Dashboard
│   │   ├── Chamados.php       # Gestão de chamados
│   │   ├── Empresas.php       # Gestão de empresas
│   │   └── Usuarios.php       # Gestão de usuários
│   ├── Models/
│   │   ├── UsuarioModel.php
│   │   ├── EmpresaModel.php
│   │   ├── ChamadoModel.php
│   │   ├── RespostaModel.php
│   │   ├── AnexoModel.php
│   │   └── CategoriaModel.php
│   ├── Views/
│   │   ├── layouts/
│   │   │   └── main.php       # Layout principal
│   │   ├── auth/              # Telas de login/registro
│   │   ├── dashboard/         # Dashboard
│   │   └── chamados/          # Telas de chamados
│   └── Filters/
│       ├── AuthFilter.php     # Verificação de login
│       ├── AdminFilter.php    # Verificação de admin
│       └── StaffFilter.php    # Verificação de equipe
├── public/
│   ├── css/
│   │   └── style.css          # CSS customizado
│   ├── js/
│   │   └── app.js             # JavaScript customizado
│   └── uploads/
│       └── chamados/          # Arquivos anexados
├── writable/                  # Logs e cache
├── database.sql               # Estrutura do banco
├── .env                       # Configurações
└── README.md                  # Este arquivo
```

## 🎨 Interface e Design

- **Layout Responsivo:** Funciona perfeitamente em desktop, tablet e mobile
- **Cores Personalizadas:**
  - Primária: `#FF6B35` (Laranja)
  - Secundária: `#004E89` (Azul)
- **Cards Modernos:** Design clean com cards e sombras suaves
- **Ícones Intuitivos:** FontAwesome 6 para todos os ícones
- **Gráficos:** Chart.js para visualizações de dados
- **Tabelas:** DataTables com busca, ordenação e paginação

## 📊 Status dos Chamados

1. **🔵 Aberto** - Chamado recém-criado, aguardando atendimento
2. **🟡 Em Atendimento** - Equipe está trabalhando no chamado
3. **🟠 Aguardando Cliente** - Aguardando resposta do cliente
4. **🟢 Finalizado** - Chamado resolvido e finalizado pelo cliente

## 🔐 Níveis de Acesso

### Cliente
- Criar chamados
- Ver apenas seus próprios chamados
- Responder seus chamados
- Finalizar seus chamados
- Avaliar atendimento

### Atendente
- Ver todos os chamados
- Responder qualquer chamado
- Criar notas internas
- Atribuir chamados
- Gerenciar usuários

### Admin
- Todas as permissões de atendente
- Gerenciar empresas
- Gerenciar todos os usuários
- Acesso a relatórios completos
- Configurações do sistema

## 🔄 Fluxo de Trabalho

1. **Cliente abre um chamado** com tipo (ocorrência/solicitação), categoria, descrição e anexos
2. **Sistema gera protocolo** único automaticamente
3. **Atendente recebe notificação** e pode se atribuir ao chamado
4. **Conversação via mensagens** entre cliente e atendente
5. **Cliente finaliza** quando estiver satisfeito
6. **Cliente avalia** o atendimento (opcional)

## 📱 Recursos Adicionais

- **Anexos:** Upload de fotos e documentos (max 5MB por arquivo)
- **Categorias:** 11 categorias pré-definidas (editáveis)
- **Prioridades:** Baixa, Média, Alta, Urgente
- **Notas Internas:** Mensagens visíveis apenas para equipe
- **Busca Avançada:** Filtros por tipo, status, categoria, etc.
- **Gráficos:** Estatísticas visuais no dashboard
- **Responsivo:** Funciona em qualquer dispositivo

## 🛡️ Segurança

- Senhas criptografadas com bcrypt
- Proteção contra SQL Injection
- Validação de inputs
- Sessões seguras
- Filtros de autenticação em todas as rotas protegidas
- Permissões por tipo de usuário

## 🐛 Solução de Problemas

### Erro de conexão com banco de dados
- Verifique as credenciais em `.env` e `app/Config/Database.php`
- Teste a conexão manualmente via MySQL

### Erro 404 nas rotas
- Verifique se o arquivo `.htaccess` existe na pasta `public/`
- Certifique-se de que o `mod_rewrite` está habilitado no Apache

### Erro de permissão em uploads
- Execute: `chmod 777 public/uploads/chamados`
- Verifique se o usuário do servidor web tem permissão de escrita

### Sessão expira muito rápido
- Ajuste `session.expiration` no arquivo `.env`
- Aumente o valor de `session.gc_maxlifetime` no PHP.ini

## 📞 Suporte

Para suporte ou dúvidas:
- **Email:** contato@sabores.com.br
- **Telefone:** (11) 9999-9999

## 📝 Licença

Sistema proprietário - Sabores Refeitório © 2025

---

**Desenvolvido com ❤️ usando CodeIgniter 4 + Bootstrap 5**
