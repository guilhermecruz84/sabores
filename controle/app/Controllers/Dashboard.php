<?php

namespace App\Controllers;

use App\Models\ChamadoModel;
use App\Models\EmpresaModel;
use App\Models\UsuarioModel;

class Dashboard extends BaseController
{
    protected $chamadoModel;
    protected $empresaModel;
    protected $usuarioModel;

    public function __construct()
    {
        $this->chamadoModel = new ChamadoModel();
        $this->empresaModel = new EmpresaModel();
        $this->usuarioModel = new UsuarioModel();
    }

    /**
     * Dashboard principal
     */
    public function index()
    {
        // Verificar se está logado
        if ($check = $this->verificarLogin()) {
            return $check;
        }

        // Bloquear operadores (eles só têm acesso aos checklists)
        if ($check = $this->verificarNaoOperador()) {
            return $check;
        }

        $data = [
            'titulo' => 'Dashboard - Sistema de Chamados'
        ];

        // Estatísticas baseadas no tipo de usuário
        if ($this->usuarioLogado->tipo === 'cliente') {
            // Estatísticas do cliente
            $data['estatisticas'] = $this->chamadoModel->getEstatisticas(
                $this->usuarioLogado->empresa_id,
                $this->usuarioLogado->id
            );

            $data['chamados_recentes'] = $this->chamadoModel->getChamadosCompletos([
                'usuario_id' => $this->usuarioLogado->id
            ]);

            $data['contagem_status'] = $this->chamadoModel->contarPorStatus(
                $this->usuarioLogado->empresa_id,
                $this->usuarioLogado->id
            );

            $data['contagem_tipo'] = $this->chamadoModel->contarPorTipo(
                $this->usuarioLogado->empresa_id,
                $this->usuarioLogado->id
            );
        } else {
            // Estatísticas para admin/atendente
            $data['estatisticas'] = $this->chamadoModel->getEstatisticas();

            $data['chamados_recentes'] = $this->chamadoModel->getChamadosCompletos([
                'status' => 'aberto'
            ]);

            $data['contagem_status'] = $this->chamadoModel->contarPorStatus();
            $data['contagem_tipo'] = $this->chamadoModel->contarPorTipo();

            // Estatísticas adicionais para admin
            if ($this->usuarioLogado->tipo === 'admin') {
                $data['total_empresas'] = $this->empresaModel->where('ativo', 1)->countAllResults();
                $data['total_clientes'] = $this->usuarioModel->contarPorTipo('cliente');
                $data['total_atendentes'] = $this->usuarioModel->contarPorTipo('atendente');
            }

            // Para atendentes, mostrar apenas seus chamados
            if ($this->usuarioLogado->tipo === 'atendente') {
                $data['meus_chamados'] = $this->chamadoModel->getChamadosCompletos([
                    'atendente_id' => $this->usuarioLogado->id
                ]);
            }
        }

        // Limitar chamados recentes a 10
        if (isset($data['chamados_recentes']) && count($data['chamados_recentes']) > 10) {
            $data['chamados_recentes'] = array_slice($data['chamados_recentes'], 0, 10);
        }

        return $this->renderView('dashboard/index', $data);
    }

    /**
     * Obter dados para gráficos (AJAX)
     */
    public function graficos()
    {
        if (!$this->request->isAJAX()) {
            return $this->respondWithError('Requisição inválida');
        }

        $periodo = $this->request->getGet('periodo') ?? 30;

        $filtros = [];
        if ($this->usuarioLogado->tipo === 'cliente') {
            $filtros['empresa_id'] = $this->usuarioLogado->empresa_id;
            $filtros['usuario_id'] = $this->usuarioLogado->id;
        }

        $estatisticas = $this->chamadoModel->getEstatisticas(
            $filtros['empresa_id'] ?? null,
            $filtros['usuario_id'] ?? null,
            $periodo
        );

        $contagem_status = $this->chamadoModel->contarPorStatus(
            $filtros['empresa_id'] ?? null,
            $filtros['usuario_id'] ?? null
        );

        $contagem_tipo = $this->chamadoModel->contarPorTipo(
            $filtros['empresa_id'] ?? null,
            $filtros['usuario_id'] ?? null
        );

        return $this->respondWithSuccess('Dados carregados', [
            'estatisticas' => $estatisticas,
            'status' => $contagem_status,
            'tipos' => $contagem_tipo
        ]);
    }
}
