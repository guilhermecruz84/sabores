-- Tabela para armazenar avaliações de colaboradoras feitas pelos clientes
CREATE TABLE IF NOT EXISTS avaliacao_colaboradora_cliente (
    id INT AUTO_INCREMENT PRIMARY KEY,
    empresa_id INT NOT NULL,
    cliente_id INT NOT NULL,
    data DATE NOT NULL,

    -- Critérios de avaliação (notas de 1 a 5)
    assiduidade_pontualidade TINYINT NOT NULL CHECK (assiduidade_pontualidade BETWEEN 1 AND 5),
    apresentacao_pessoal TINYINT NOT NULL CHECK (apresentacao_pessoal BETWEEN 1 AND 5),
    atendimento_relacionamento TINYINT NOT NULL CHECK (atendimento_relacionamento BETWEEN 1 AND 5),
    agilidade_produtividade TINYINT NOT NULL CHECK (agilidade_produtividade BETWEEN 1 AND 5),
    qualidade_execucao TINYINT NOT NULL CHECK (qualidade_execucao BETWEEN 1 AND 5),
    cumprimento_regras TINYINT NOT NULL CHECK (cumprimento_regras BETWEEN 1 AND 5),
    proatividade TINYINT NOT NULL CHECK (proatividade BETWEEN 1 AND 5),
    organizacao_limpeza TINYINT NOT NULL CHECK (organizacao_limpeza BETWEEN 1 AND 5),
    percepcao_geral TINYINT NOT NULL CHECK (percepcao_geral BETWEEN 1 AND 5),

    -- Campo de observações
    observacoes TEXT,

    -- Média calculada
    media_geral DECIMAL(3,2) GENERATED ALWAYS AS (
        (assiduidade_pontualidade + apresentacao_pessoal + atendimento_relacionamento +
         agilidade_produtividade + qualidade_execucao + cumprimento_regras +
         proatividade + organizacao_limpeza + percepcao_geral) / 9.0
    ) STORED,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (empresa_id) REFERENCES empresas(id) ON DELETE CASCADE,
    FOREIGN KEY (cliente_id) REFERENCES usuarios(id) ON DELETE CASCADE,

    INDEX idx_empresa (empresa_id),
    INDEX idx_cliente (cliente_id),
    INDEX idx_data (data)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
