<?php

namespace App\Controllers;

use App\Models\CardapioModel;
use App\Models\AvaliacaoCardapioModel;
use App\Models\EmpresaModel;

class Avaliacoes extends BaseController
{
    protected $cardapioModel;
    protected $avaliacaoModel;
    protected $empresaModel;

    public function __construct()
    {
        $this->cardapioModel = new CardapioModel();
        $this->avaliacaoModel = new AvaliacaoCardapioModel();
        $this->empresaModel = new EmpresaModel();
    }

    /**
     * Página inicial para cliente avaliar cardápios
     */
    public function index()
    {
        if ($check = $this->verificarLogin()) {
            return $check;
        }

        // Se não for cliente, redireciona para dashboard de avaliações
        if ($this->usuarioLogado->tipo !== 'cliente') {
            return redirect()->to('/avaliacoes/dashboard');
        }

        $empresaId = $this->usuarioLogado->empresa_id;

        // Filtros
        $status = $this->request->getGet('status') ?? 'pendentes';
        $mes = $this->request->getGet('mes') ?? date('m');
        $ano = $this->request->getGet('ano') ?? date('Y');

        // Gerar cardápios automaticamente para dias úteis do mês
        $this->gerarCardapiosDiasUteis($empresaId, $mes, $ano);

        // Buscar cardápios do mês selecionado
        $cardapios = $this->cardapioModel->getCardapiosPorMes($empresaId, $mes, $ano);

        // Verificar quais já foram avaliados e aplicar filtro
        $cardapiosFiltrados = [];
        foreach ($cardapios as $cardapio) {
            $cardapio->ja_avaliado = $this->avaliacaoModel->clienteJaAvaliou(
                $cardapio->id,
                $this->usuarioLogado->id
            );

            if ($cardapio->ja_avaliado) {
                $cardapio->avaliacao = $this->avaliacaoModel->getAvaliacaoCliente(
                    $cardapio->id,
                    $this->usuarioLogado->id
                );
            }

            // Aplicar filtro de status
            if ($status === 'pendentes' && !$cardapio->ja_avaliado) {
                $cardapiosFiltrados[] = $cardapio;
            } elseif ($status === 'avaliados' && $cardapio->ja_avaliado) {
                $cardapiosFiltrados[] = $cardapio;
            } elseif ($status === 'todos') {
                $cardapiosFiltrados[] = $cardapio;
            }
        }

        return $this->renderView('avaliacoes/index', [
            'titulo' => 'Avaliar Cardápios',
            'cardapios' => $cardapiosFiltrados,
            'status' => $status,
            'mes' => $mes,
            'ano' => $ano
        ]);
    }

    /**
     * Página para avaliar um cardápio específico
     */
    public function avaliar($cardapioId)
    {
        if ($check = $this->verificarLogin()) {
            return $check;
        }

        // Apenas clientes podem avaliar
        if ($this->usuarioLogado->tipo !== 'cliente') {
            return redirect()->to('/avaliacoes/dashboard')->with('erro', 'Apenas clientes podem avaliar cardápios.');
        }

        $cardapio = $this->cardapioModel->find($cardapioId);

        if (!$cardapio) {
            return redirect()->to('/avaliacoes')->with('erro', 'Cardápio não encontrado.');
        }

        // Verificar se o cardápio é da empresa do cliente
        if ($cardapio->empresa_id != $this->usuarioLogado->empresa_id) {
            return redirect()->to('/avaliacoes')->with('erro', 'Você não pode avaliar este cardápio.');
        }

        // Buscar avaliação existente
        $avaliacao = $this->avaliacaoModel->getAvaliacaoCliente($cardapioId, $this->usuarioLogado->id);

        return $this->renderView('avaliacoes/avaliar', [
            'titulo' => 'Avaliar Cardápio',
            'cardapio' => $cardapio,
            'avaliacao' => $avaliacao
        ]);
    }

    /**
     * Salvar avaliação do cliente
     */
    public function salvarAvaliacao($cardapioId)
    {
        if ($check = $this->verificarLogin()) {
            return $check;
        }

        // Apenas clientes podem avaliar
        if ($this->usuarioLogado->tipo !== 'cliente') {
            return redirect()->to('/avaliacoes/dashboard')->with('erro', 'Apenas clientes podem avaliar cardápios.');
        }

        $cardapio = $this->cardapioModel->find($cardapioId);

        if (!$cardapio || $cardapio->empresa_id != $this->usuarioLogado->empresa_id) {
            return redirect()->to('/avaliacoes')->with('erro', 'Cardápio não encontrado.');
        }

        $avaliacao = $this->request->getPost('avaliacao');
        $motivo = $this->request->getPost('motivo');

        // Validar
        if (!in_array($avaliacao, ['otimo', 'bom', 'regular', 'ruim'])) {
            return redirect()->back()->with('erro', 'Avaliação inválida.');
        }

        // Motivo obrigatório para Regular e Ruim
        if (in_array($avaliacao, ['regular', 'ruim']) && empty(trim($motivo))) {
            return redirect()->back()->withInput()->with('erro', 'O motivo é obrigatório para avaliações Regular ou Ruim.');
        }

        $data = [
            'cardapio_id' => $cardapioId,
            'cliente_id' => $this->usuarioLogado->id,
            'empresa_id' => $this->usuarioLogado->empresa_id,
            'tipo_avaliacao' => 'cliente',
            'data' => $cardapio->data,
            'avaliacao' => $avaliacao,
            'motivo' => $motivo
        ];

        // Verificar se já existe avaliação
        $avaliacaoExistente = $this->avaliacaoModel->getAvaliacaoCliente($cardapioId, $this->usuarioLogado->id);

        if ($avaliacaoExistente) {
            // Atualizar
            $this->avaliacaoModel->update($avaliacaoExistente->id, $data);
            $mensagem = 'Avaliação atualizada com sucesso!';
        } else {
            // Criar nova
            $this->avaliacaoModel->insert($data);
            $mensagem = 'Avaliação registrada com sucesso!';
        }

        return redirect()->to('/avaliacoes')->with('sucesso', $mensagem);
    }

    /**
     * Dashboard de avaliações (Admin/Administrativo)
     */
    public function dashboard()
    {
        if ($check = $this->verificarLogin()) {
            return $check;
        }

        // Apenas admin e administrativo
        if (!in_array($this->usuarioLogado->tipo, ['admin', 'atendente'])) {
            return redirect()->to('/avaliacoes')->with('erro', 'Você não tem permissão para acessar esta página.');
        }

        // Mês e ano (padrão: mês atual)
        $mes = $this->request->getGet('mes') ?? date('m');
        $ano = $this->request->getGet('ano') ?? date('Y');
        $empresaId = $this->request->getGet('empresa_id') ?? '';

        $dataInicio = "$ano-$mes-01";
        $dataFim = date('Y-m-t', strtotime($dataInicio));

        // Buscar lista de empresas para o filtro
        $empresas = $this->empresaModel->orderBy('nome_fantasia')->findAll();

        // Buscar todas as avaliações de CLIENTES no período
        $builder = $this->avaliacaoModel->builder();
        $builder->select('avaliacoes_cardapio.*, empresas.nome_fantasia as empresa_nome')
            ->join('empresas', 'empresas.id = avaliacoes_cardapio.empresa_id')
            ->where('avaliacoes_cardapio.data >=', $dataInicio)
            ->where('avaliacoes_cardapio.data <=', $dataFim)
            ->where('avaliacoes_cardapio.tipo_avaliacao', 'cliente');

        // Filtrar por empresa se selecionado
        if (!empty($empresaId)) {
            $builder->where('avaliacoes_cardapio.empresa_id', $empresaId);
        }

        $avaliacoesClientes = $builder
            ->orderBy('avaliacoes_cardapio.data', 'DESC')
            ->orderBy('empresas.nome_fantasia')
            ->get()
            ->getResult();

        // Buscar todas as avaliações de FUNCIONÁRIOS no período
        $builder = $this->avaliacaoModel->builder();
        $builder->select('avaliacoes_cardapio.*, empresas.nome_fantasia as empresa_nome')
            ->join('empresas', 'empresas.id = avaliacoes_cardapio.empresa_id')
            ->where('avaliacoes_cardapio.data >=', $dataInicio)
            ->where('avaliacoes_cardapio.data <=', $dataFim)
            ->where('avaliacoes_cardapio.tipo_avaliacao', 'funcionarios');

        // Filtrar por empresa se selecionado
        if (!empty($empresaId)) {
            $builder->where('avaliacoes_cardapio.empresa_id', $empresaId);
        }

        $avaliacoesFuncionarios = $builder
            ->orderBy('avaliacoes_cardapio.data', 'DESC')
            ->orderBy('empresas.nome_fantasia')
            ->get()
            ->getResult();

        // Buscar todas as avaliações de COLABORADORAS no período
        $avaliacaoColaboradoraModel = new \App\Models\AvaliacaoColaboradoraModel();
        $builder = $avaliacaoColaboradoraModel->builder();
        $builder->select('avaliacoes_colaboradores.*, empresas.nome_fantasia as empresa_nome')
            ->join('empresas', 'empresas.id = avaliacoes_colaboradores.empresa_id')
            ->where('avaliacoes_colaboradores.data >=', $dataInicio)
            ->where('avaliacoes_colaboradores.data <=', $dataFim);

        // Filtrar por empresa se selecionado
        if (!empty($empresaId)) {
            $builder->where('avaliacoes_colaboradores.empresa_id', $empresaId);
        }

        $avaliacoesColaboradoras = $builder
            ->orderBy('avaliacoes_colaboradores.data', 'DESC')
            ->orderBy('empresas.nome_fantasia')
            ->get()
            ->getResult();

        // Organizar por data + empresa e calcular médias
        $avaliacoesPorDataEmpresa = [];

        // Agrupar clientes (sempre 1 por data/empresa)
        foreach ($avaliacoesClientes as $aval) {
            $key = $aval->data . '_' . $aval->empresa_id;
            if (!isset($avaliacoesPorDataEmpresa[$key])) {
                $avaliacoesPorDataEmpresa[$key] = [
                    'data' => $aval->data,
                    'empresa_id' => $aval->empresa_id,
                    'empresa_nome' => $aval->empresa_nome,
                    'cliente' => null,
                    'funcionarios' => [],
                    'colaboradoras' => []
                ];
            }
            $avaliacoesPorDataEmpresa[$key]['cliente'] = $aval;
        }

        // Agrupar funcionários (múltiplas avaliações por data/empresa)
        foreach ($avaliacoesFuncionarios as $aval) {
            $key = $aval->data . '_' . $aval->empresa_id;
            if (!isset($avaliacoesPorDataEmpresa[$key])) {
                $avaliacoesPorDataEmpresa[$key] = [
                    'data' => $aval->data,
                    'empresa_id' => $aval->empresa_id,
                    'empresa_nome' => $aval->empresa_nome,
                    'cliente' => null,
                    'funcionarios' => [],
                    'colaboradoras' => []
                ];
            }
            $avaliacoesPorDataEmpresa[$key]['funcionarios'][] = $aval;
        }

        // Agrupar colaboradoras (múltiplas avaliações por data/empresa)
        foreach ($avaliacoesColaboradoras as $aval) {
            $key = $aval->data . '_' . $aval->empresa_id;
            if (!isset($avaliacoesPorDataEmpresa[$key])) {
                $avaliacoesPorDataEmpresa[$key] = [
                    'data' => $aval->data,
                    'empresa_id' => $aval->empresa_id,
                    'empresa_nome' => $aval->empresa_nome,
                    'cliente' => null,
                    'funcionarios' => [],
                    'colaboradoras' => []
                ];
            }
            $avaliacoesPorDataEmpresa[$key]['colaboradoras'][] = $aval;
        }

        // Calcular médias para funcionários e colaboradoras
        $avaliacoesMap = [
            'otimo' => 4,
            'bom' => 3,
            'regular' => 2,
            'ruim' => 1
        ];

        foreach ($avaliacoesPorDataEmpresa as $key => &$item) {
            // Calcular média de funcionários
            if (!empty($item['funcionarios'])) {
                $total = 0;
                $count = count($item['funcionarios']);
                $motivos = [];

                foreach ($item['funcionarios'] as $aval) {
                    $total += $avaliacoesMap[$aval->avaliacao];
                    if (!empty($aval->motivo)) {
                        $motivos[] = $aval->motivo;
                    }
                }

                $media = $total / $count;

                // Converter média de volta para texto
                if ($media >= 3.5) $mediaTexto = 'otimo';
                elseif ($media >= 2.5) $mediaTexto = 'bom';
                elseif ($media >= 1.5) $mediaTexto = 'regular';
                else $mediaTexto = 'ruim';

                // Criar objeto com média
                $item['funcionarios_resumo'] = (object)[
                    'avaliacao' => $mediaTexto,
                    'quantidade' => $count,
                    'motivos' => $motivos
                ];
            } else {
                $item['funcionarios_resumo'] = null;
            }

            // Calcular média de colaboradoras
            if (!empty($item['colaboradoras'])) {
                $total = 0;
                $count = count($item['colaboradoras']);
                $motivos = [];

                foreach ($item['colaboradoras'] as $aval) {
                    $total += $avaliacoesMap[$aval->avaliacao];
                    if (!empty($aval->motivo)) {
                        $motivos[] = $aval->motivo;
                    }
                }

                $media = $total / $count;

                // Converter média de volta para texto
                if ($media >= 3.5) $mediaTexto = 'otimo';
                elseif ($media >= 2.5) $mediaTexto = 'bom';
                elseif ($media >= 1.5) $mediaTexto = 'regular';
                else $mediaTexto = 'ruim';

                // Criar objeto com média
                $item['colaboradoras_resumo'] = (object)[
                    'avaliacao' => $mediaTexto,
                    'quantidade' => $count,
                    'motivos' => $motivos
                ];
            } else {
                $item['colaboradoras_resumo'] = null;
            }
        }

        // Converter para array ordenado por data DESC
        $avaliacoesPorDataEmpresa = array_values($avaliacoesPorDataEmpresa);

        return $this->renderView('avaliacoes/dashboard', [
            'titulo' => 'Dashboard de Avaliações',
            'avaliacoes' => $avaliacoesPorDataEmpresa,
            'mes' => $mes,
            'ano' => $ano,
            'empresas' => $empresas,
            'empresaId' => $empresaId
        ]);
    }

    /**
     * Histórico de avaliações (Admin/Administrativo)
     */
    public function historico()
    {
        if ($check = $this->verificarLogin()) {
            return $check;
        }

        // Apenas admin e administrativo
        if (!in_array($this->usuarioLogado->tipo, ['admin', 'atendente'])) {
            return redirect()->to('/avaliacoes')->with('erro', 'Você não tem permissão para acessar esta página.');
        }

        // Filtros
        $empresaId = $this->request->getGet('empresa_id');
        $dataInicio = $this->request->getGet('data_inicio');
        $dataFim = $this->request->getGet('data_fim');

        // Buscar avaliações
        $avaliacoes = $this->avaliacaoModel->getAvaliacoesDetalhadas($empresaId, $dataInicio, $dataFim);

        // Buscar empresas para filtro
        $empresas = $this->empresaModel->getEmpresasAtivas();

        return $this->renderView('avaliacoes/historico', [
            'titulo' => 'Histórico de Avaliações',
            'avaliacoes' => $avaliacoes,
            'empresas' => $empresas,
            'filtros' => [
                'empresa_id' => $empresaId,
                'data_inicio' => $dataInicio,
                'data_fim' => $dataFim
            ]
        ]);
    }

    /**
     * Gerenciar cardápios (Admin)
     */
    public function gerenciarCardapios()
    {
        if ($check = $this->verificarLogin()) {
            return $check;
        }

        if ($check = $this->verificarAdmin()) {
            return $check;
        }

        $empresaId = $this->request->getGet('empresa_id');

        // Buscar cardápios
        if ($empresaId) {
            $cardapios = $this->cardapioModel->getCardapiosComAvaliacoes($empresaId);
        } else {
            $cardapios = [];
        }

        // Buscar empresas
        $empresas = $this->empresaModel->getEmpresasAtivas();

        return $this->renderView('avaliacoes/gerenciar_cardapios', [
            'titulo' => 'Gerenciar Cardápios',
            'cardapios' => $cardapios,
            'empresas' => $empresas,
            'empresa_id_selecionada' => $empresaId
        ]);
    }

    /**
     * Criar/Editar cardápio (Admin)
     */
    public function salvarCardapio($id = null)
    {
        if ($check = $this->verificarLogin()) {
            return $check;
        }

        if ($check = $this->verificarAdmin()) {
            return $check;
        }

        $empresaId = $this->request->getPost('empresa_id');
        $data = $this->request->getPost('data');
        $descricao = $this->request->getPost('descricao');

        // Validar
        if (!$empresaId || !$data || !$descricao) {
            return redirect()->back()->withInput()->with('erro', 'Todos os campos são obrigatórios.');
        }

        $dadosCardapio = [
            'empresa_id' => $empresaId,
            'data' => $data,
            'descricao' => $descricao
        ];

        if ($id) {
            // Atualizar
            $this->cardapioModel->update($id, $dadosCardapio);
            $mensagem = 'Cardápio atualizado com sucesso!';
        } else {
            // Verificar se já existe cardápio para esta data/empresa
            if ($this->cardapioModel->cardapioExiste($empresaId, $data)) {
                return redirect()->back()->withInput()->with('erro', 'Já existe um cardápio cadastrado para esta empresa nesta data.');
            }

            // Criar novo
            $this->cardapioModel->insert($dadosCardapio);
            $mensagem = 'Cardápio criado com sucesso!';
        }

        return redirect()->to("/avaliacoes/gerenciar-cardapios?empresa_id={$empresaId}")->with('sucesso', $mensagem);
    }

    /**
     * Deletar cardápio (Admin)
     */
    public function deletarCardapio($id)
    {
        if ($check = $this->verificarLogin()) {
            return $check;
        }

        if ($check = $this->verificarAdmin()) {
            return $check;
        }

        $cardapio = $this->cardapioModel->find($id);

        if (!$cardapio) {
            return redirect()->back()->with('erro', 'Cardápio não encontrado.');
        }

        // Deletar avaliações associadas
        $this->avaliacaoModel->where('cardapio_id', $id)->delete();

        // Deletar cardápio
        $this->cardapioModel->delete($id);

        return redirect()->back()->with('sucesso', 'Cardápio deletado com sucesso!');
    }

    /**
     * Gerar cardápios automaticamente para dias úteis do mês
     * (Segunda a Sexta-feira)
     */
    protected function gerarCardapiosDiasUteis($empresaId, $mes, $ano)
    {
        // Calcular primeiro e último dia do mês
        $primeiroDia = strtotime("$ano-$mes-01");
        $ultimoDia = strtotime(date('Y-m-t', $primeiroDia));

        // Percorrer todos os dias do mês
        for ($timestamp = $primeiroDia; $timestamp <= $ultimoDia; $timestamp = strtotime('+1 day', $timestamp)) {
            $diaSemana = date('w', $timestamp); // 0=domingo, 6=sábado

            // Apenas dias úteis (1=segunda até 5=sexta)
            if ($diaSemana >= 1 && $diaSemana <= 5) {
                $data = date('Y-m-d', $timestamp);

                // Verificar se já existe cardápio para esta data/empresa
                if (!$this->cardapioModel->cardapioExiste($empresaId, $data)) {
                    // Criar cardápio automaticamente
                    $this->cardapioModel->insert([
                        'empresa_id' => $empresaId,
                        'data' => $data,
                        'descricao' => 'Cardápio do dia ' . date('d/m/Y', $timestamp)
                    ]);
                }
            }
        }
    }
}
