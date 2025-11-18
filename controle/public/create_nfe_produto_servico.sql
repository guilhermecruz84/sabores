-- Tabela para guardar associações entre produtos e serviços
-- Evita ter que selecionar o serviço toda vez que importar o mesmo produto

CREATE TABLE IF NOT EXISTS `nfe_produto_servico` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo_produto` varchar(100) NOT NULL COMMENT 'cProd da NFe',
  `nome_produto` varchar(255) DEFAULT NULL COMMENT 'xProd da NFe (para referência)',
  `servico_id` int(11) NOT NULL COMMENT 'ID do serviço associado',
  `servico_nome` varchar(100) DEFAULT NULL COMMENT 'Nome do serviço (para referência)',
  `ultima_utilizacao` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_codigo_produto` (`codigo_produto`),
  KEY `idx_servico_id` (`servico_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Associações produto-serviço para auto-preenchimento';
