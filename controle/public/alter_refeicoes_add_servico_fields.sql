-- ================================================
-- ALTER TABLE refeicoes
-- Adiciona campos servico_id e servico_nome
-- Para integração com sistema NFe
-- ================================================

-- Adicionar coluna servico_id (referência à tabela servicos)
ALTER TABLE `refeicoes`
ADD COLUMN `servico_id` int(11) NULL AFTER `servico`,
ADD INDEX `idx_servico_id` (`servico_id`);

-- Adicionar coluna servico_nome (nome completo do serviço)
ALTER TABLE `refeicoes`
ADD COLUMN `servico_nome` varchar(100) NULL AFTER `servico_id`;

-- ================================================
-- Opcional: Preencher servico_id baseado no campo servico existente
-- ================================================

-- Se quiser popular os dados antigos, descomente as linhas abaixo:

-- UPDATE refeicoes SET servico_id = 1, servico_nome = 'Almoço' WHERE servico LIKE '%almo%' OR servico LIKE '%Almo%';
-- UPDATE refeicoes SET servico_id = 2, servico_nome = 'Jantar' WHERE servico LIKE '%jantar%' OR servico LIKE '%Jantar%';
-- UPDATE refeicoes SET servico_id = 3, servico_nome = 'Café da manhã' WHERE servico LIKE '%caf%' OR servico LIKE '%Caf%' OR servico LIKE '%manhã%';
-- UPDATE refeicoes SET servico_id = 4, servico_nome = 'Lanche' WHERE servico LIKE '%lanche%' OR servico LIKE '%Lanche%';

-- ================================================
-- FIM
-- ================================================
