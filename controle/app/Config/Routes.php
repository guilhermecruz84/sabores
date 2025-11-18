<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Rotas de Autenticação
$routes->get('/', 'Auth::login');
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::authenticate');
$routes->get('logout', 'Auth::logout');
$routes->get('registro', 'Auth::register');
$routes->post('registro', 'Auth::processRegister');

// Rotas protegidas (requerem autenticação)
$routes->group('', ['filter' => 'auth'], function($routes) {

    // Dashboard
    $routes->get('dashboard', 'Dashboard::index');

    // Chamados
    $routes->get('chamados', 'Chamados::index');
    $routes->get('chamados/novo', 'Chamados::novo');
    $routes->post('chamados/criar', 'Chamados::criar');
    $routes->get('chamados/ver/(:num)', 'Chamados::ver/$1');
    $routes->post('chamados/responder/(:num)', 'Chamados::responder/$1');
    $routes->post('chamados/finalizar/(:num)', 'Chamados::finalizar/$1');
    $routes->post('chamados/atribuir/(:num)', 'Chamados::atribuir/$1');
    $routes->post('chamados/upload', 'Chamados::upload');
    $routes->get('chamados/download/(:num)', 'Chamados::download/$1');

    // Empresas (apenas admin)
    $routes->group('empresas', ['filter' => 'admin'], function($routes) {
        $routes->get('/', 'Empresas::index');
        $routes->get('nova', 'Empresas::nova');
        $routes->post('criar', 'Empresas::criar');
        $routes->get('editar/(:num)', 'Empresas::editar/$1');
        $routes->post('atualizar/(:num)', 'Empresas::atualizar/$1');
        $routes->post('deletar/(:num)', 'Empresas::deletar/$1');
    });

    // Usuários (admin e atendente)
    $routes->group('usuarios', ['filter' => 'staff'], function($routes) {
        $routes->get('/', 'Usuarios::index');
        $routes->get('novo', 'Usuarios::novo');
        $routes->post('criar', 'Usuarios::criar');
        $routes->get('editar/(:num)', 'Usuarios::editar/$1');
        $routes->post('atualizar/(:num)', 'Usuarios::atualizar/$1');
        $routes->post('deletar/(:num)', 'Usuarios::deletar/$1');
    });

    // Perfil
    $routes->get('perfil', 'Usuarios::perfil');
    $routes->post('perfil/atualizar', 'Usuarios::atualizarPerfil');

    // Relatórios (admin e atendente)
    $routes->group('relatorios', ['filter' => 'staff'], function($routes) {
        $routes->get('/', 'Relatorios::index');
        $routes->get('exportar', 'Relatorios::exportar');
    });

    // Checklists
    $routes->group('checklists', function($routes) {
        // Dashboard (para operadores e staff)
        $routes->get('/', 'Checklists::index');

        // Criar novo checklist
        $routes->get('novo/(:segment)', 'Checklists::novo/$1');

        // Preencher checklist
        $routes->get('preencher/(:num)', 'Checklists::preencher/$1');
        $routes->post('salvar/(:num)', 'Checklists::salvar/$1');

        // Visualizar checklist
        $routes->get('ver/(:num)', 'Checklists::ver/$1');

        // Relatório (apenas admin e atendente)
        $routes->get('relatorio', 'Checklists::relatorio', ['filter' => 'staff']);

        // Gerenciar itens (apenas admin)
        $routes->group('itens', ['filter' => 'admin'], function($routes) {
            $routes->get('/', 'Checklists::itens');
            $routes->post('criar', 'Checklists::criarItem');
            $routes->post('editar/(:num)', 'Checklists::editarItem/$1');
            $routes->get('toggle/(:num)', 'Checklists::toggleItem/$1');
        });

        // Configurar dias da semana (apenas admin)
        $routes->get('configurar-dias', 'Checklists::configurarDias', ['filter' => 'admin']);
        $routes->post('salvar-configuracao', 'Checklists::salvarConfiguracao', ['filter' => 'admin']);
        $routes->get('desativar-configuracao/(:num)', 'Checklists::desativarConfiguracao/$1', ['filter' => 'admin']);
    });

    // Alertas de Checklist (apenas admin e administrativo)
    $routes->group('alertas', ['filter' => 'staff'], function($routes) {
        $routes->get('/', 'Alertas::index');
        $routes->get('historico', 'Alertas::historico');
        $routes->get('concluir/(:num)', 'Alertas::concluir/$1');
        $routes->get('contar-pendentes', 'Alertas::contarPendentes');
    });

    // Avaliações de Cardápio
    $routes->group('avaliacoes', function($routes) {
        // Cliente
        $routes->get('/', 'Avaliacoes::index');
        $routes->get('avaliar/(:num)', 'Avaliacoes::avaliar/$1');
        $routes->post('salvar-avaliacao/(:num)', 'Avaliacoes::salvarAvaliacao/$1');

        // Admin/Administrativo
        $routes->get('dashboard', 'Avaliacoes::dashboard');
        $routes->get('historico', 'Avaliacoes::historico');

        // Admin - Gerenciar Cardápios
        $routes->get('gerenciar-cardapios', 'Avaliacoes::gerenciarCardapios', ['filter' => 'admin']);
        $routes->post('salvar-cardapio', 'Avaliacoes::salvarCardapio', ['filter' => 'admin']);
        $routes->post('salvar-cardapio/(:num)', 'Avaliacoes::salvarCardapio/$1', ['filter' => 'admin']);
        $routes->get('deletar-cardapio/(:num)', 'Avaliacoes::deletarCardapio/$1', ['filter' => 'admin']);
    });

    // Avaliador (Tablet)
    $routes->group('avaliador', function($routes) {
        $routes->get('/', 'Avaliador::index');
        $routes->get('avaliar-cardapio', 'Avaliador::avaliarCardapio');
        $routes->post('salvar-avaliacao-cardapio', 'Avaliador::salvarAvaliacaoCardapio');
        $routes->get('avaliar-colaboradora', 'Avaliador::avaliarColaboradora');
        $routes->post('salvar-avaliacao-colaboradora', 'Avaliador::salvarAvaliacaoColaboradora');
        $routes->get('obrigado', 'Avaliador::obrigado');
    });

    // Módulo Operacional (Apenas Admin)
    $routes->group('operacional', ['filter' => 'admin', 'namespace' => 'App\Controllers\Operacional'], function($routes) {
        // Dashboard Operacional
        $routes->get('/', 'OperacionalDashboard::index');
        $routes->get('dashboard', 'OperacionalDashboard::index');

        // Refeições/Serviços
        $routes->get('refeicoes', 'Refeicoes::index');
        $routes->post('refeicoes/salvar', 'Refeicoes::salvar');
        $routes->get('refeicoes/listar', 'Refeicoes::listar');
        $routes->get('refeicoes/editar/(:num)', 'Refeicoes::editar/$1');
        $routes->post('refeicoes/atualizar/(:num)', 'Refeicoes::atualizar/$1');
        $routes->get('refeicoes/excluir/(:num)', 'Refeicoes::excluir/$1');

        // Despesas
        $routes->get('despesas', 'Despesas::index');
        $routes->post('despesas/salvar', 'Despesas::salvar');
        $routes->get('despesas/listar', 'Despesas::listar');
        $routes->get('despesas/editar/(:num)', 'Despesas::editar/$1');
        $routes->post('despesas/atualizar/(:num)', 'Despesas::atualizar/$1');
        $routes->get('despesas/excluir/(:num)', 'Despesas::excluir/$1');

        // CMO
        $routes->get('cmo', 'Cmo::index');

        // CMV
        $routes->get('cmv', 'Cmv::index');

        // NFe
        $routes->get('nfe', 'Nfe::index');
        $routes->post('nfe/upload', 'Nfe::upload');
        $routes->get('nfe/review/(:num)', 'Nfe::review/$1');
        $routes->post('nfe/finalize/(:num)', 'Nfe::finalize/$1');
        $routes->get('nfe/listar', 'Nfe::listar');
        $routes->get('nfe/ver/(:num)', 'Nfe::ver/$1');
        $routes->get('nfe/excluir/(:num)', 'Nfe::excluir/$1');
    });
});
