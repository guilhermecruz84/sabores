-- ===============================================
-- SISTEMA DE ALERTAS PARA CHECKLIST
-- ===============================================

-- 1. Adicionar coluna 'gera_alerta' na tabela checklists_itens
ALTER TABLE `checklists_itens`
ADD COLUMN `gera_alerta` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Se marcado, gera alerta quando resposta for Não Conforme' AFTER `requer_foto`;

-- 2. Criar tabela de alertas
CREATE TABLE IF NOT EXISTS `checklist_alertas` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `checklist_registro_id` INT(11) UNSIGNED NOT NULL COMMENT 'ID do registro do checklist',
  `checklist_item_id` INT(11) UNSIGNED NOT NULL COMMENT 'ID do item que gerou o alerta',
  `checklist_resposta_id` INT(11) UNSIGNED NULL COMMENT 'ID da resposta',
  `empresa_id` INT(11) UNSIGNED NOT NULL COMMENT 'ID da empresa',
  `operador_id` INT(11) UNSIGNED NOT NULL COMMENT 'ID do operador que preencheu',
  `descricao_item` VARCHAR(500) NOT NULL COMMENT 'Descrição do item',
  `observacao` TEXT NULL COMMENT 'Observação do operador sobre a não conformidade',
  `data_ocorrencia` DATE NOT NULL COMMENT 'Data da ocorrência',
  `status` ENUM('pendente', 'concluido') NOT NULL DEFAULT 'pendente',
  `concluido_por` INT(11) UNSIGNED NULL COMMENT 'ID do usuário que marcou como concluído',
  `concluido_em` DATETIME NULL COMMENT 'Data/hora que foi marcado como concluído',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_status` (`status`),
  INDEX `idx_data_ocorrencia` (`data_ocorrencia`),
  INDEX `idx_empresa` (`empresa_id`),
  INDEX `idx_checklist_registro` (`checklist_registro_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Alertas gerados por não conformidades nos checklists';

-- 3. Verificar estrutura
SELECT 'Tabela checklist_alertas criada com sucesso!' as Resultado;
SELECT COUNT(*) as Total_Alertas FROM checklist_alertas;
