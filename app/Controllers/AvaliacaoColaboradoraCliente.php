<?php

namespace App\Controllers;

use App\Models\AvaliacaoColaboradoraClienteModel;

class AvaliacaoColaboradoraCliente extends BaseController
{
    protected $avaliacaoModel;

    public function __construct()
    {
        $this->avaliacaoModel = new AvaliacaoColaboradoraClienteModel();
    }

    /**
     * Página principal - Formulário de avaliação
     */
    public function index()
    {
        if ($check = $this->verificarLogin()) {
            return $check;
        }

        // Apenas clientes podem avaliar
        if ($this->usuarioLogado->tipo !== 'cliente') {
            return redirect()->to('/dashboard')->with('erro', 'Acesso negado. Apenas clientes podem avaliar colaboradoras.');
        }

        $data = date('Y-m-d');
        $mesAtual = date('m');
        $anoAtual = date('Y');

        // Nome do mês em português
        $meses = [
            '01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março',
            '04' => 'Abril', '05' => 'Maio', '06' => 'Junho',
            '07' => 'Julho', '08' => 'Agosto', '09' => 'Setembro',
            '10' => 'Outubro', '11' => 'Novembro', '12' => 'Dezembro'
        ];
        $nomeMes = $meses[$mesAtual] . ' de ' . $anoAtual;

        // Verificar se já avaliou no mês
        $jaAvaliou = $this->avaliacaoModel->jaAvaliouNoMes($this->usuarioLogado->id, $anoAtual, $mesAtual);

        // Buscar avaliação do mês se já avaliou
        $avaliacaoDoMes = null;
        if ($jaAvaliou) {
            $avaliacaoDoMes = $this->avaliacaoModel->getAvaliacaoDoMes($this->usuarioLogado->id, $anoAtual, $mesAtual);
        }

        return $this->renderView('avaliacao_colaboradora_cliente/avaliar', [
            'titulo' => 'Avaliar Colaboradora',
            'jaAvaliou' => $jaAvaliou,
            'avaliacaoDoMes' => $avaliacaoDoMes,
            'dataAvaliacao' => $data,
            'nomeMes' => $nomeMes
        ]);
    }

    /**
     * Salvar avaliação
     */
    public function salvar()
    {
        if ($check = $this->verificarLogin()) {
            return $check;
        }

        if ($this->usuarioLogado->tipo !== 'cliente') {
            return redirect()->to('/dashboard')->with('erro', 'Acesso negado.');
        }

        $empresaId = $this->usuarioLogado->empresa_id;
        $clienteId = $this->usuarioLogado->id;
        $data = date('Y-m-d');
        $mesAtual = date('m');
        $anoAtual = date('Y');

        // Verificar se já avaliou no mês
        if ($this->avaliacaoModel->jaAvaliouNoMes($clienteId, $anoAtual, $mesAtual)) {
            return redirect()->back()->with('erro', 'Você já fez uma avaliação este mês. Volte no próximo mês para avaliar novamente.');
        }

        // Coletar dados do formulário
        $dadosAvaliacao = [
            'empresa_id' => $empresaId,
            'cliente_id' => $clienteId,
            'data' => $data,
            'assiduidade_pontualidade' => $this->request->getPost('assiduidade_pontualidade'),
            'apresentacao_pessoal' => $this->request->getPost('apresentacao_pessoal'),
            'atendimento_relacionamento' => $this->request->getPost('atendimento_relacionamento'),
            'agilidade_produtividade' => $this->request->getPost('agilidade_produtividade'),
            'qualidade_execucao' => $this->request->getPost('qualidade_execucao'),
            'cumprimento_regras' => $this->request->getPost('cumprimento_regras'),
            'proatividade' => $this->request->getPost('proatividade'),
            'organizacao_limpeza' => $this->request->getPost('organizacao_limpeza'),
            'percepcao_geral' => $this->request->getPost('percepcao_geral'),
            'observacoes' => $this->request->getPost('observacoes')
        ];

        // Validar e salvar
        if (!$this->avaliacaoModel->insert($dadosAvaliacao)) {
            $erros = $this->avaliacaoModel->errors();
            return redirect()->back()->with('erro', 'Erro ao salvar avaliação: ' . implode(', ', $erros));
        }

        return redirect()->to('/avaliacao-colaboradora-cliente/obrigado')
                        ->with('sucesso', 'Avaliação registrada com sucesso!');
    }

    /**
     * Tela de agradecimento
     */
    public function obrigado()
    {
        if ($check = $this->verificarLogin()) {
            return $check;
        }

        if ($this->usuarioLogado->tipo !== 'cliente') {
            return redirect()->to('/dashboard')->with('erro', 'Acesso negado.');
        }

        return $this->renderView('avaliacao_colaboradora_cliente/obrigado', [
            'titulo' => 'Avaliação Concluída'
        ]);
    }

    /**
     * Histórico de avaliações do cliente
     */
    public function historico()
    {
        if ($check = $this->verificarLogin()) {
            return $check;
        }

        if ($this->usuarioLogado->tipo !== 'cliente') {
            return redirect()->to('/dashboard')->with('erro', 'Acesso negado.');
        }

        $clienteId = $this->usuarioLogado->id;

        // Filtros opcionais
        $dataInicio = $this->request->getGet('data_inicio');
        $dataFim = $this->request->getGet('data_fim');

        $avaliacoes = $this->avaliacaoModel->getAvaliacoesPorCliente($clienteId, $dataInicio, $dataFim);

        return $this->renderView('avaliacao_colaboradora_cliente/historico', [
            'titulo' => 'Histórico de Avaliações',
            'avaliacoes' => $avaliacoes,
            'dataInicio' => $dataInicio,
            'dataFim' => $dataFim
        ]);
    }

    /**
     * Dashboard de estatísticas (apenas para admin/administrativo)
     */
    public function dashboard()
    {
        if ($check = $this->verificarLogin()) {
            return $check;
        }

        // Apenas admin e administrativo podem ver dashboard
        if (!in_array($this->usuarioLogado->tipo, ['admin', 'atendente'])) {
            return redirect()->to('/dashboard')->with('erro', 'Acesso negado.');
        }

        $empresaId = $this->usuarioLogado->empresa_id;

        // Filtros
        $dataInicio = $this->request->getGet('data_inicio') ?: date('Y-m-01'); // Primeiro dia do mês
        $dataFim = $this->request->getGet('data_fim') ?: date('Y-m-d'); // Hoje

        // Buscar avaliações do período
        $avaliacoes = $this->avaliacaoModel->getAvaliacoesPorEmpresa($empresaId, $dataInicio, $dataFim);

        // Estatísticas por critério
        $estatisticas = $this->avaliacaoModel->getEstatisticasPorCriterio($empresaId, $dataInicio, $dataFim);

        // Média geral
        $mediaGeral = $this->avaliacaoModel->getMediaGeralPorPeriodo($empresaId, $dataInicio, $dataFim);

        return $this->renderView('avaliacao_colaboradora_cliente/dashboard', [
            'titulo' => 'Dashboard - Avaliações de Colaboradora',
            'avaliacoes' => $avaliacoes,
            'estatisticas' => $estatisticas,
            'mediaGeral' => $mediaGeral,
            'dataInicio' => $dataInicio,
            'dataFim' => $dataFim
        ]);
    }
}
