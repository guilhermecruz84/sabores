-- ================================================
-- TABELA SERVICOS - Sistema NFe
-- ================================================
-- Esta tabela armazena os tipos de serviços/refeições
-- usados na importação de NFe
-- ================================================

CREATE TABLE IF NOT EXISTS `servicos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(100) NOT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_ativo` (`ativo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================
-- INSERIR SERVIÇOS PADRÃO
-- ================================================

INSERT INTO `servicos` (`id`, `titulo`, `ativo`) VALUES
(1, 'Almoço', 1),
(2, 'Jantar', 1),
(3, 'Café da manhã', 1),
(4, 'Lanche', 1);

-- ================================================
-- FIM
-- ================================================
