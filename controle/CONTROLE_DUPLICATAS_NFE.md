# 🔒 Controle de Duplicatas - Importação NFe

## ✨ Nova Funcionalidade Implementada

O sistema agora **impede a importação de notas fiscais duplicadas**, verificando automaticamente se uma NF-e já foi importada anteriormente através da **chave de acesso** (44 dígitos).

---

## 🔍 Como Funciona

### **1️⃣ Verificação Automática**

Durante o upload dos XMLs, o sistema:
1. Extrai a **chave de acesso** de cada NF-e
2. Verifica no banco se essa chave já foi importada
3. Se for duplicada, **ignora** o XML automaticamente
4. Se for nova, **processa** normalmente

### **2️⃣ Informações Detalhadas**

Quando XMLs são ignorados, o sistema mostra:
- ✅ Nome do arquivo
- ✅ Número/Série da NF-e
- ✅ Competência da importação anterior
- ✅ Empresa associada

**Exemplo de mensagem:**
```
⚠️ XMLs Ignorados (já importados anteriormente):
• 35250312345678901234567890123456789012.xml - NF 1234/1 (Competência: 2025-11) (Artely)
```

---

## 📊 Mensagens do Sistema

### **Upload com Sucesso:**
```
Lote #15: ✅ 3 XML(s) importado(s) com sucesso (45 item(ns))
```

### **Com Duplicatas:**
```
Lote #16: ✅ 2 XML(s) importado(s) com sucesso (30 item(ns)).
⚠️ 1 XML(s) ignorado(s) (já importados anteriormente)
```

### **Com Erros:**
```
Lote #17: ✅ 1 XML(s) importado(s) com sucesso (15 item(ns)).
❌ 1 XML(s) com erro
```

### **Combinado:**
```
Lote #18: ✅ 2 XML(s) importado(s) com sucesso (30 item(ns)).
⚠️ 1 XML(s) ignorado(s) (já importados anteriormente).
❌ 1 XML(s) com erro
```

---

## 🎯 Cenários de Uso

### **Cenário 1: Tentativa de Re-importação**
**Situação:** Você já importou a NF-e 1234/1 em novembro/2025

**Ação:** Tenta importar o mesmo XML novamente

**Resultado:**
- ⚠️ XML é **ignorado** automaticamente
- ✅ Sistema mostra aviso com detalhes da importação anterior
- ✅ Não cria dados duplicados no banco

### **Cenário 2: Upload em Lote**
**Situação:** Você envia 10 XMLs, sendo que 3 já foram importados

**Resultado:**
- ✅ 7 XMLs novos são processados
- ⚠️ 3 XMLs duplicados são ignorados
- ✅ Sistema mostra resumo completo

### **Cenário 3: Mix de Novos, Duplicados e Erros**
**Situação:** 10 XMLs enviados:
- 6 novos (OK)
- 3 duplicados
- 1 com erro (XML corrompido)

**Resultado:**
```
Lote #20: ✅ 6 XML(s) importado(s) com sucesso (90 item(ns)).
⚠️ 3 XML(s) ignorado(s) (já importados anteriormente).
❌ 1 XML(s) com erro

Detalhes dos ignorados:
• nota1.xml - NF 100/1 (Competência: 2025-10) (Artely)
• nota2.xml - NF 101/1 (Competência: 2025-10) (DAF)
• nota3.xml - NF 102/1 (Competência: 2025-11) (JEA)

Erros:
• nota_corrompida.xml (XML inválido)
```

---

## 🔧 Detalhes Técnicos

### **Verificação por Chave de Acesso**
A chave de acesso é **única** para cada NF-e e contém 44 dígitos.

**Exemplo:**
```
35250312345678901234567890123456789012
```

### **Onde a Verificação Acontece**

**1. NfeModel.php** (linha 236-259)
```php
public function verificarChaveDuplicada(string $chave): ?array
{
    // Busca se a chave já existe em nfe_docs
    $doc = $this->builder('nfe_docs')
        ->select('...')
        ->join('nfe_imports', ...)
        ->where('nfe_docs.chave', $chave)
        ->get()
        ->getRowArray();

    return $doc ?: null;
}
```

**2. Nfe.php Controller** (linha 67-84)
```php
// Verificação antes de processar
$chave = $parsed['doc']['chave'] ?? null;
if ($chave) {
    $duplicada = $model->verificarChaveDuplicada($chave);
    if ($duplicada) {
        // Ignora e registra no log
        $ignorados[] = "...";
        continue;
    }
}
```

### **Log do Sistema**
Duplicatas também são registradas no log:
```
INFO - 2025-11-14 15:30:45 --> NFe duplicada ignorada: nota1.xml - Chave: 35250312345678901234567890123456789012
```

---

## ✅ Benefícios

1. **Evita Dados Duplicados**
   - Não permite inserir a mesma NF-e duas vezes em `refeicoes`

2. **Informação Clara**
   - Mostra quais XMLs foram ignorados e por quê

3. **Histórico Preservado**
   - Mostra quando/onde a NF-e foi importada anteriormente

4. **Performance**
   - Não processa XMLs desnecessários

5. **Auditoria**
   - Registra em log todas as tentativas de duplicação

---

## 🚫 Limitações

### **Baseado Apenas na Chave de Acesso**
- Se o XML não tiver chave de acesso, não haverá verificação de duplicidade
- Notas muito antigas sem chave podem ser importadas mais de uma vez

### **Não Impede Re-importação Manual**
- Se você deletar os dados de uma importação do banco e tentar importar novamente, o sistema permitirá

---

## 📝 Manutenção

### **Ver NF-es Já Importadas**
```sql
SELECT chave, numero, serie, dhEmi, arquivo,
       import_id, created_at
FROM nfe_docs
ORDER BY id DESC
LIMIT 100;
```

### **Verificar Duplicatas Manualmente**
```sql
SELECT chave, COUNT(*) as vezes
FROM nfe_docs
GROUP BY chave
HAVING COUNT(*) > 1;
```

### **Deletar Importação Duplicada (se necessário)**
```sql
-- Atenção: Só faça isso se tiver certeza!
DELETE FROM nfe_items WHERE import_id = X;
DELETE FROM nfe_docs WHERE import_id = X;
DELETE FROM nfe_imports WHERE id = X;
```

---

## 🎉 Resultado Final

Agora você pode:
- ✅ Fazer upload de XMLs sem se preocupar com duplicatas
- ✅ Re-enviar lotes inteiros sem criar dados duplicados
- ✅ Ver claramente quais XMLs foram processados, ignorados ou falharam
- ✅ Manter a integridade dos dados de refeições

---

**Data de Implementação:** 14/11/2025
**Versão:** 2.0 - Sistema NFe com Controle de Duplicatas
