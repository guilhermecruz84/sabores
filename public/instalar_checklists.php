<?php
/**
 * Script para criar tabelas de checklists
 * Execute este arquivo acessando: http://localhost:8080/instalar_checklists.php
 */

// Configurações do banco
$host = 'br404.hostgator.com.br';
$db   = 'guil5541_sabores';
$user = 'guil5541_sabores';
$pass = 'Sm2025.#';
$port = 3306;

// Conectar ao banco
try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h2 style='color: green;'>✓ Conectado ao banco de dados com sucesso!</h2>";

    // Array de comandos SQL
    $commands = [
        // Tabela de Registros de Checklists
        "CREATE TABLE IF NOT EXISTS `checklists_registros` (
          `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
          `empresa_id` int(11) UNSIGNED NOT NULL,
          `operador_id` int(11) UNSIGNED NOT NULL,
          `data` date NOT NULL,
          `tipo` enum('abertura','encerramento') NOT NULL,
          `status` enum('pendente','em_andamento','finalizado') DEFAULT 'pendente',
          `observacoes` text DEFAULT NULL,
          `finalizado_em` datetime DEFAULT NULL,
          `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
          `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`),
          KEY `empresa_id` (`empresa_id`),
          KEY `operador_id` (`operador_id`),
          KEY `data` (`data`),
          KEY `tipo` (`tipo`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        // Tabela de Itens do Checklist
        "CREATE TABLE IF NOT EXISTS `checklists_itens` (
          `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
          `tipo` enum('abertura','encerramento') NOT NULL,
          `ordem` int(11) NOT NULL DEFAULT 0,
          `descricao` varchar(255) NOT NULL,
          `tipo_resposta` enum('sim_nao','texto','numero','multipla_escolha') DEFAULT 'sim_nao',
          `obrigatorio` tinyint(1) DEFAULT 1,
          `ativo` tinyint(1) DEFAULT 1,
          `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
          `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`),
          KEY `tipo` (`tipo`),
          KEY `ativo` (`ativo`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        // Tabela de Respostas aos Itens
        "CREATE TABLE IF NOT EXISTS `checklists_respostas` (
          `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
          `registro_id` int(11) UNSIGNED NOT NULL,
          `item_id` int(11) UNSIGNED NOT NULL,
          `resposta` text DEFAULT NULL,
          `conforme` tinyint(1) DEFAULT NULL COMMENT 'NULL=não aplicável, 0=não conforme, 1=conforme',
          `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
          `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`),
          KEY `registro_id` (`registro_id`),
          KEY `item_id` (`item_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        // Tabela de Produtos (sobras/faltas)
        "CREATE TABLE IF NOT EXISTS `checklists_produtos` (
          `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
          `registro_id` int(11) UNSIGNED NOT NULL,
          `tipo_registro` enum('sobra','falta') NOT NULL,
          `item` varchar(255) NOT NULL,
          `quantidade` varchar(100) DEFAULT NULL,
          `observacao` text DEFAULT NULL,
          `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`),
          KEY `registro_id` (`registro_id`),
          KEY `tipo_registro` (`tipo_registro`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    ];

    // Inserir itens padrão
    $inserts = [
        // Itens de Abertura
        "INSERT IGNORE INTO `checklists_itens` (`id`, `tipo`, `ordem`, `descricao`, `tipo_resposta`, `obrigatorio`) VALUES
        (1, 'abertura', 1, 'Horário de recebimento das refeições', 'texto', 1)",

        "INSERT IGNORE INTO `checklists_itens` (`id`, `tipo`, `ordem`, `descricao`, `tipo_resposta`, `obrigatorio`) VALUES
        (2, 'abertura', 2, 'Temperatura dos alimentos quentes está adequada? (acima de 60°C)', 'sim_nao', 1)",

        "INSERT IGNORE INTO `checklists_itens` (`id`, `tipo`, `ordem`, `descricao`, `tipo_resposta`, `obrigatorio`) VALUES
        (3, 'abertura', 3, 'Temperatura dos alimentos frios está adequada? (abaixo de 10°C)', 'sim_nao', 1)",

        "INSERT IGNORE INTO `checklists_itens` (`id`, `tipo`, `ordem`, `descricao`, `tipo_resposta`, `obrigatorio`) VALUES
        (4, 'abertura', 4, 'As embalagens estão íntegras e bem vedadas?', 'sim_nao', 1)",

        "INSERT IGNORE INTO `checklists_itens` (`id`, `tipo`, `ordem`, `descricao`, `tipo_resposta`, `obrigatorio`) VALUES
        (5, 'abertura', 5, 'A quantidade de refeições está conforme o pedido?', 'sim_nao', 1)",

        "INSERT IGNORE INTO `checklists_itens` (`id`, `tipo`, `ordem`, `descricao`, `tipo_resposta`, `obrigatorio`) VALUES
        (6, 'abertura', 6, 'Os alimentos apresentam aspecto, cor e odor normais?', 'sim_nao', 1)",

        "INSERT IGNORE INTO `checklists_itens` (`id`, `tipo`, `ordem`, `descricao`, `tipo_resposta`, `obrigatorio`) VALUES
        (7, 'abertura', 7, 'O veículo de transporte está limpo e higienizado?', 'sim_nao', 1)",

        "INSERT IGNORE INTO `checklists_itens` (`id`, `tipo`, `ordem`, `descricao`, `tipo_resposta`, `obrigatorio`) VALUES
        (8, 'abertura', 8, 'O entregador está com uniforme limpo e usando EPIs?', 'sim_nao', 1)",

        // Itens de Encerramento
        "INSERT IGNORE INTO `checklists_itens` (`id`, `tipo`, `ordem`, `descricao`, `tipo_resposta`, `obrigatorio`) VALUES
        (9, 'encerramento', 1, 'Horário de encerramento do serviço', 'texto', 1)",

        "INSERT IGNORE INTO `checklists_itens` (`id`, `tipo`, `ordem`, `descricao`, `tipo_resposta`, `obrigatorio`) VALUES
        (10, 'encerramento', 2, 'Todas as refeições foram servidas?', 'sim_nao', 1)",

        "INSERT IGNORE INTO `checklists_itens` (`id`, `tipo`, `ordem`, `descricao`, `tipo_resposta`, `obrigatorio`) VALUES
        (11, 'encerramento', 3, 'Houve reclamações dos usuários?', 'sim_nao', 1)",

        "INSERT IGNORE INTO `checklists_itens` (`id`, `tipo`, `ordem`, `descricao`, `tipo_resposta`, `obrigatorio`) VALUES
        (12, 'encerramento', 4, 'As áreas de serviço foram higienizadas?', 'sim_nao', 1)",

        "INSERT IGNORE INTO `checklists_itens` (`id`, `tipo`, `ordem`, `descricao`, `tipo_resposta`, `obrigatorio`) VALUES
        (13, 'encerramento', 5, 'Os equipamentos foram desligados e limpos?', 'sim_nao', 1)",

        "INSERT IGNORE INTO `checklists_itens` (`id`, `tipo`, `ordem`, `descricao`, `tipo_resposta`, `obrigatorio`) VALUES
        (14, 'encerramento', 6, 'O lixo foi descartado adequadamente?', 'sim_nao', 1)",
    ];

    $sucesso = 0;
    $erros = 0;

    echo "<h3>Criando tabelas...</h3>";
    echo "<ul>";

    // Executar comandos CREATE TABLE
    foreach ($commands as $sql) {
        try {
            $pdo->exec($sql);
            $sucesso++;
            // Extrair nome da tabela
            preg_match('/CREATE TABLE.*?`(.*?)`/', $sql, $matches);
            $tableName = $matches[1] ?? 'tabela';
            echo "<li style='color: green;'>✓ Tabela <strong>{$tableName}</strong> criada</li>";
        } catch (PDOException $e) {
            $erros++;
            echo "<li style='color: red;'>✗ Erro: " . $e->getMessage() . "</li>";
        }
    }

    echo "</ul>";

    echo "<h3>Inserindo itens padrão...</h3>";
    echo "<ul>";

    // Executar INSERTs
    foreach ($inserts as $sql) {
        try {
            $pdo->exec($sql);
            echo "<li style='color: green;'>✓ Item inserido</li>";
        } catch (PDOException $e) {
            echo "<li style='color: orange;'>⚠ " . $e->getMessage() . "</li>";
        }
    }

    echo "</ul>";

    echo "<hr>";
    echo "<h3>Resultado Final:</h3>";
    echo "<p style='color: green;'><strong>✓ Tabelas criadas: $sucesso</strong></p>";

    if ($erros > 0) {
        echo "<p style='color: red;'><strong>✗ Erros: $erros</strong></p>";
    }

    // Verificar tabelas criadas
    echo "<h3>Tabelas existentes:</h3>";
    $tables = $pdo->query("SHOW TABLES LIKE 'checklists%'")->fetchAll(PDO::FETCH_COLUMN);
    echo "<ul>";
    foreach ($tables as $table) {
        echo "<li style='color: green;'>✓ $table</li>";
    }
    echo "</ul>";

    // Contar itens inseridos
    $count = $pdo->query("SELECT COUNT(*) FROM checklists_itens")->fetchColumn();
    echo "<p><strong>Total de itens padrão cadastrados:</strong> $count</p>";

    echo "<hr>";
    echo "<p style='font-size: 18px; color: green;'><strong>✓ Instalação concluída com sucesso!</strong></p>";
    echo "<p><a href='/' style='padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px;'>Voltar ao Sistema</a></p>";
    echo "<p style='color: #999;'><small>Você pode deletar este arquivo após a execução por segurança.</small></p>";

} catch (PDOException $e) {
    echo "<h2 style='color: red;'>✗ Erro ao conectar ao banco de dados!</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
}
