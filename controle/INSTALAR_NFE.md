# 📦 Instalação Completa - Sistema NFe

## ⚠️ IMPORTANTE - Execute os SQLs nesta ordem!

O sistema de importação de NF-e precisa de 2 ajustes no banco de dados:

---

## 🔧 **PASSO 1: Criar Tabela `servicos`**

### **Via phpMyAdmin:**

1. Acesse **phpMyAdmin** no cPanel
2. Selecione o banco: **guil5541_sabores**
3. Vá na aba **SQL**
4. Cole este código:

```sql
CREATE TABLE IF NOT EXISTS `servicos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(100) NOT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_ativo` (`ativo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `servicos` (`id`, `titulo`, `ativo`) VALUES
(1, 'Almoço', 1),
(2, 'Jantar', 1),
(3, 'Café da manhã', 1),
(4, 'Lanche', 1);
```

5. Clique em **Executar**
6. ✅ Deve aparecer: **4 linhas inseridas**

---

## 🔧 **PASSO 2: Adicionar Colunas na Tabela `refeicoes`**

1. **Ainda no phpMyAdmin**, aba **SQL**
2. Cole este código:

```sql
ALTER TABLE `refeicoes`
ADD COLUMN `servico_id` int(11) NULL AFTER `servico`,
ADD INDEX `idx_servico_id` (`servico_id`);

ALTER TABLE `refeicoes`
ADD COLUMN `servico_nome` varchar(100) NULL AFTER `servico_id`;
```

3. Clique em **Executar**
4. ✅ Deve aparecer: **2 colunas adicionadas**

---

## 🧪 **PASSO 3: Testar Importação de NFe**

1. Acesse: `https://www.saboresemmovimento.com.br/controle/operacional/nfe`

2. Faça **upload de um arquivo XML** (NF-e)

3. Na tela de **revisão**, você verá:
   - Empresas disponíveis
   - Serviços disponíveis:
     - ✅ Almoço
     - ✅ Jantar
     - ✅ Café da manhã
     - ✅ Lanche
   - Lista de itens da NF-e

4. Preencha:
   - **Competência** (mês/ano): Ex: `11/2025` ou `2025-11`
   - **Empresa**: Selecione a empresa
   - **Serviço para cada item**: Associe cada item da NF-e a um serviço

5. Clique em **Finalizar**

6. ✅ Deve redirecionar para a lista de importações com mensagem de sucesso!

---

## ✅ **Verificação de Sucesso:**

Após executar os SQLs, verifique:

### **Verificar Tabela `servicos`:**
```sql
SELECT * FROM servicos;
```
Deve retornar 4 registros (Almoço, Jantar, Café da manhã, Lanche)

### **Verificar Estrutura `refeicoes`:**
```sql
DESCRIBE refeicoes;
```
Deve mostrar as colunas:
- ✅ `servico_id` int(11)
- ✅ `servico_nome` varchar(100)

---

## 🚨 **Se Ainda Der Erro:**

### **Erro "Table servicos doesn't exist":**
- ❌ Você não executou o PASSO 1
- ✅ Volte e execute o CREATE TABLE servicos

### **Erro "Unknown column servico_id":**
- ❌ Você não executou o PASSO 2
- ✅ Volte e execute o ALTER TABLE refeicoes

### **Erro "Whoops! We seem to have hit a snag":**
- Verifique se o `.env` está em `production` (não `development`)
- Se estiver em `development`, volte para `production`

---

## 📊 **O que Cada Campo Faz:**

### **Tabela `servicos`:**
- `id`: ID único do serviço
- `titulo`: Nome do serviço (Almoço, Jantar, etc.)
- `ativo`: Se o serviço está ativo (1) ou não (0)

### **Colunas Novas em `refeicoes`:**
- `servico_id`: Referência ao ID da tabela servicos
- `servico_nome`: Nome completo do serviço (redundante, mas usado para relatórios)

---

## 🧹 **Limpeza (Opcional):**

Após confirmar que está funcionando, você pode deletar os arquivos SQL:
```
controle/public/install_servicos.sql
controle/public/alter_refeicoes_add_servico_fields.sql
```

E os arquivos de teste/debug:
```
controle/public/check-nfe.php
controle/public/debug-nfe-error.php
controle/public/test-nfe-model.php
```

---

## ✅ **Pronto!**

Após executar os 2 SQLs, o sistema de importação de NF-e deve funcionar perfeitamente! 🚀

**Data:** 14/11/2025
