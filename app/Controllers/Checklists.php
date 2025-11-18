<?php

namespace App\Controllers;

use App\Models\ChecklistRegistroModel;
use App\Models\ChecklistItemModel;
use App\Models\ChecklistRespostaModel;
use App\Models\ChecklistProdutoModel;
use App\Models\ChecklistConfiguracaoModel;

class Checklists extends BaseController
{
    protected $registroModel;
    protected $itemModel;
    protected $respostaModel;
    protected $produtoModel;
    protected $configuracaoModel;

    public function __construct()
    {
        $this->registroModel = new ChecklistRegistroModel();
        $this->itemModel = new ChecklistItemModel();
        $this->respostaModel = new ChecklistRespostaModel();
        $this->produtoModel = new ChecklistProdutoModel();
        $this->configuracaoModel = new ChecklistConfiguracaoModel();
    }

    /**
     * Dashboard do operador
     */
    public function index()
    {
        if ($check = $this->verificarLogin()) {
            return $check;
        }

        // Buscar registros pendentes e recentes do operador
        $registrosPendentes = $this->registroModel->getRegistrosPendentes($this->usuarioLogado->id);
        $registrosRecentes = $this->registroModel->getRegistrosPorOperador($this->usuarioLogado->id, 10);

        // Verificar disponibilidade dos checklists para hoje
        $aberturaDisponivel = $this->configuracaoModel->checklistDisponivelHoje(
            $this->usuarioLogado->empresa_id,
            'abertura'
        );
        $encerramentoDisponivel = $this->configuracaoModel->checklistDisponivelHoje(
            $this->usuarioLogado->empresa_id,
            'encerramento'
        );

        return $this->renderView('checklists/dashboard', [
            'titulo' => 'Meus Checklists - Sistema de Chamados',
            'registrosPendentes' => $registrosPendentes,
            'registrosRecentes' => $registrosRecentes,
            'aberturaDisponivel' => $aberturaDisponivel,
            'encerramentoDisponivel' => $encerramentoDisponivel
        ]);
    }

    /**
     * Iniciar novo checklist
     */
    public function novo($tipo)
    {
        if ($check = $this->verificarLogin()) {
            return $check;
        }

        if (!in_array($tipo, ['abertura', 'encerramento'])) {
            return redirect()->back()->with('erro', 'Tipo de checklist inválido.');
        }

        // Verificar se o checklist está disponível para hoje
        $disponivelHoje = $this->configuracaoModel->checklistDisponivelHoje(
            $this->usuarioLogado->empresa_id,
            $tipo
        );

        if (!$disponivelHoje) {
            $diasNomes = \App\Models\ChecklistConfiguracaoModel::getNomesDiasSemana();
            $config = $this->configuracaoModel->where('empresa_id', $this->usuarioLogado->empresa_id)
                                             ->where('tipo', $tipo)
                                             ->where('ativo', 1)
                                             ->first();

            $diasPermitidos = [];
            if ($config) {
                $diasIds = explode(',', $config->dias_semana);
                foreach ($diasIds as $diaId) {
                    $diasPermitidos[] = $diasNomes[$diaId];
                }
            }

            $mensagem = 'Este checklist não está disponível para hoje.';
            if (!empty($diasPermitidos)) {
                $mensagem .= ' Dias permitidos: ' . implode(', ', $diasPermitidos) . '.';
            }

            return redirect()->back()->with('erro', $mensagem);
        }

        $data = date('Y-m-d');

        // Verificar se já existe registro para hoje
        $registroExistente = $this->registroModel->verificarRegistroExistente(
            $this->usuarioLogado->id,
            $data,
            $tipo
        );

        if ($registroExistente) {
            return redirect()->to("/checklists/preencher/{$registroExistente->id}")
                           ->with('info', 'Você já iniciou este checklist hoje. Continue o preenchimento.');
        }

        // Criar novo registro
        $registroId = $this->registroModel->insert([
            'empresa_id' => $this->usuarioLogado->empresa_id,
            'operador_id' => $this->usuarioLogado->id,
            'data' => $data,
            'tipo' => $tipo,
            'status' => 'em_andamento'
        ]);

        if ($registroId) {
            return redirect()->to("/checklists/preencher/{$registroId}");
        } else {
            return redirect()->back()->with('erro', 'Erro ao criar checklist.');
        }
    }

    /**
     * Preencher checklist
     */
    public function preencher($id)
    {
        if ($check = $this->verificarLogin()) {
            return $check;
        }

        $registro = $this->registroModel->find($id);

        if (!$registro) {
            return redirect()->to('/checklists')->with('erro', 'Checklist não encontrado.');
        }

        // Verificar se o operador é o dono do registro
        if ($registro->operador_id != $this->usuarioLogado->id && !in_array($this->usuarioLogado->tipo, ['admin', 'atendente'])) {
            return redirect()->to('/checklists')->with('erro', 'Você não tem permissão para acessar este checklist.');
        }

        // Buscar itens do checklist
        $itens = $this->itemModel->getItensPorTipo($registro->tipo);

        // Buscar respostas já preenchidas
        $respostas = $this->respostaModel->getRespostasPorRegistro($id);
        $respostasMap = [];
        foreach ($respostas as $resposta) {
            $respostasMap[$resposta->item_id] = $resposta;
        }

        // Se for checklist de encerramento, buscar produtos (sobras/faltas)
        $produtos = [];
        if ($registro->tipo === 'encerramento') {
            $produtos = $this->produtoModel->getProdutosPorRegistro($id);
        }

        return $this->renderView('checklists/preencher', [
            'titulo' => ucfirst($registro->tipo) . ' - Checklist',
            'registro' => $registro,
            'itens' => $itens,
            'respostasMap' => $respostasMap,
            'produtos' => $produtos
        ]);
    }

    /**
     * Salvar checklist
     */
    public function salvar($id)
    {
        if ($check = $this->verificarLogin()) {
            return $check;
        }

        $registro = $this->registroModel->find($id);

        if (!$registro || $registro->operador_id != $this->usuarioLogado->id) {
            return redirect()->to('/checklists')->with('erro', 'Checklist não encontrado.');
        }

        // Processar respostas
        $respostas = $this->request->getPost('respostas');
        if ($respostas) {
            // Processar uploads de fotos
            $files = $this->request->getFileMultiple('fotos');
            if ($files) {
                foreach ($files as $itemId => $file) {
                    if ($file && $file->isValid() && !$file->hasMoved()) {
                        // Validar tamanho (5MB)
                        if ($file->getSize() > 5242880) {
                            return redirect()->back()->with('erro', 'Arquivo muito grande. Máximo: 5MB');
                        }

                        // Validar tipo
                        $mimeType = $file->getMimeType();
                        if (!in_array($mimeType, ['image/jpeg', 'image/jpg', 'image/png'])) {
                            return redirect()->back()->with('erro', 'Formato inválido. Aceitos: JPG, PNG');
                        }

                        // Gerar nome único
                        $newName = 'checklist_' . $id . '_item_' . $itemId . '_' . time() . '.' . $file->getExtension();

                        // Mover arquivo
                        $uploadPath = WRITEPATH . 'uploads/checklists/';
                        $file->move($uploadPath, $newName);

                        // Salvar caminho no array de respostas
                        if (!isset($respostas[$itemId])) {
                            $respostas[$itemId] = [];
                        }
                        $respostas[$itemId]['foto_path'] = 'writable/uploads/checklists/' . $newName;
                    }
                }
            }

            $this->respostaModel->salvarRespostas($id, $respostas);

            // Gerar alertas para itens marcados com "gera_alerta" e resposta "Não Conforme"
            $this->gerarAlertasNaoConformidades($id, $respostas, $registro);
        }

        // Processar produtos (sobras/faltas) se for encerramento
        if ($registro->tipo === 'encerramento') {
            $sobras = $this->request->getPost('sobras');
            $faltas = $this->request->getPost('faltas');

            if ($sobras) {
                // Processar upload de fotos das sobras
                foreach ($sobras as $idx => $sobra) {
                    $fileKey = 'sobras_foto_' . $idx;
                    $file = $this->request->getFile($fileKey);

                    if ($file && $file->isValid() && !$file->hasMoved()) {
                        $newName = $file->getRandomName();
                        $uploadPath = WRITEPATH . 'uploads/sobras/';

                        // Criar diretório se não existir
                        if (!is_dir($uploadPath)) {
                            mkdir($uploadPath, 0755, true);
                        }

                        $file->move($uploadPath, $newName);
                        $sobras[$idx]['foto'] = 'writable/uploads/sobras/' . $newName;
                    } elseif (!empty($sobra['foto_atual'])) {
                        // Manter foto existente se não houver nova
                        $sobras[$idx]['foto'] = $sobra['foto_atual'];
                    }
                }

                $this->produtoModel->salvarProdutos($id, $sobras, 'sobra');
            }

            if ($faltas) {
                $this->produtoModel->salvarProdutos($id, $faltas, 'falta');
            }
        }

        // Atualizar observações
        $observacoes = $this->request->getPost('observacoes');
        if ($observacoes) {
            $this->registroModel->update($id, ['observacoes' => $observacoes]);
        }

        // Verificar se deve finalizar
        if ($this->request->getPost('finalizar')) {
            // Verificar se todas as respostas obrigatórias foram preenchidas
            if ($this->respostaModel->verificarRespostasCompletas($id)) {
                $this->registroModel->finalizarRegistro($id);
                return redirect()->to('/checklists')->with('sucesso', 'Checklist finalizado com sucesso!');
            } else {
                return redirect()->back()->with('erro', 'Preencha todas as perguntas obrigatórias antes de finalizar.');
            }
        }

        return redirect()->back()->with('sucesso', 'Checklist salvo com sucesso!');
    }

    /**
     * Visualizar checklist finalizado
     */
    public function ver($id)
    {
        if ($check = $this->verificarLogin()) {
            return $check;
        }

        $registro = $this->registroModel->getRegistroCompleto($id);

        if (!$registro) {
            return redirect()->to('/checklists')->with('erro', 'Checklist não encontrado.');
        }

        // Verificar permissão
        if ($registro->operador_id != $this->usuarioLogado->id && !in_array($this->usuarioLogado->tipo, ['admin', 'atendente'])) {
            return redirect()->to('/checklists')->with('erro', 'Você não tem permissão para visualizar este checklist.');
        }

        $respostas = $this->respostaModel->getRespostasPorRegistro($id);
        $produtos = $this->produtoModel->getProdutosPorRegistro($id);

        return $this->renderView('checklists/visualizar', [
            'titulo' => 'Visualizar Checklist',
            'registro' => $registro,
            'respostas' => $respostas,
            'produtos' => $produtos
        ]);
    }

    /**
     * Gerenciar itens do checklist (admin)
     */
    public function itens()
    {
        if ($check = $this->verificarLogin()) {
            return $check;
        }

        if ($check = $this->verificarAdmin()) {
            return $check;
        }

        $itensAbertura = $this->itemModel->getItensPorTipo('abertura');
        $itensEncerramento = $this->itemModel->getItensPorTipo('encerramento');

        return $this->renderView('checklists/itens', [
            'titulo' => 'Gerenciar Itens do Checklist',
            'itensAbertura' => $itensAbertura,
            'itensEncerramento' => $itensEncerramento
        ]);
    }

    /**
     * Criar novo item do checklist (admin)
     */
    public function criarItem()
    {
        if ($check = $this->verificarLogin()) {
            return $check;
        }

        if ($check = $this->verificarAdmin()) {
            return $check;
        }

        $data = [
            'tipo' => $this->request->getPost('tipo'),
            'ordem' => $this->request->getPost('ordem'),
            'descricao' => $this->request->getPost('descricao'),
            'tipo_resposta' => $this->request->getPost('tipo_resposta'),
            'obrigatorio' => $this->request->getPost('obrigatorio') ? 1 : 0,
            'requer_foto' => $this->request->getPost('requer_foto') ? 1 : 0,
            'gera_alerta' => $this->request->getPost('gera_alerta') ? 1 : 0,
            'ativo' => 1
        ];

        if ($this->itemModel->insert($data)) {
            return redirect()->to('/checklists/itens')->with('sucesso', 'Item criado com sucesso!');
        } else {
            return redirect()->back()->with('erro', 'Erro ao criar item.');
        }
    }

    /**
     * Editar item do checklist (admin)
     */
    public function editarItem($id)
    {
        if ($check = $this->verificarLogin()) {
            return $check;
        }

        if ($check = $this->verificarAdmin()) {
            return $check;
        }

        $data = [
            'descricao' => $this->request->getPost('descricao'),
            'tipo_resposta' => $this->request->getPost('tipo_resposta'),
            'obrigatorio' => $this->request->getPost('obrigatorio') ? 1 : 0,
            'requer_foto' => $this->request->getPost('requer_foto') ? 1 : 0,
            'gera_alerta' => $this->request->getPost('gera_alerta') ? 1 : 0,
            'ordem' => $this->request->getPost('ordem')
        ];

        if ($this->itemModel->update($id, $data)) {
            return redirect()->to('/checklists/itens')->with('sucesso', 'Item atualizado com sucesso!');
        } else {
            return redirect()->back()->with('erro', 'Erro ao atualizar item.');
        }
    }

    /**
     * Ativar/Desativar item (admin)
     */
    public function toggleItem($id)
    {
        if ($check = $this->verificarLogin()) {
            return $check;
        }

        if ($check = $this->verificarAdmin()) {
            return $check;
        }

        $item = $this->itemModel->find($id);

        if ($item) {
            $novoStatus = $item->ativo ? 0 : 1;
            $this->itemModel->update($id, ['ativo' => $novoStatus]);
            $mensagem = $novoStatus ? 'Item ativado' : 'Item desativado';
            return redirect()->to('/checklists/itens')->with('sucesso', $mensagem);
        }

        return redirect()->back()->with('erro', 'Item não encontrado.');
    }

    /**
     * Relatório de checklists (admin/atendente)
     */
    public function relatorio()
    {
        if ($check = $this->verificarLogin()) {
            return $check;
        }

        if ($check = $this->verificarStaff()) {
            return $check;
        }

        // Filtros
        $empresaId = $this->request->getGet('empresa_id');
        $dataInicio = $this->request->getGet('data_inicio');
        $dataFim = $this->request->getGet('data_fim');
        $tipo = $this->request->getGet('tipo');

        // Buscar registros
        $registros = $this->registroModel
                          ->select('checklists_registros.*, empresas.nome_fantasia as empresa_nome, usuarios.nome as operador_nome')
                          ->join('empresas', 'empresas.id = checklists_registros.empresa_id')
                          ->join('usuarios', 'usuarios.id = checklists_registros.operador_id');

        if ($empresaId) {
            $registros->where('checklists_registros.empresa_id', $empresaId);
        }

        if ($dataInicio) {
            $registros->where('checklists_registros.data >=', $dataInicio);
        }

        if ($dataFim) {
            $registros->where('checklists_registros.data <=', $dataFim);
        }

        if ($tipo) {
            $registros->where('checklists_registros.tipo', $tipo);
        }

        $registros = $registros->orderBy('checklists_registros.data', 'DESC')
                              ->orderBy('checklists_registros.created_at', 'DESC')
                              ->findAll();

        // Estatísticas
        $estatisticas = $this->registroModel->getEstatisticas($empresaId, $dataInicio, $dataFim);

        // Itens mais frequentes em sobras e faltas
        $sobrasFrequentes = $this->produtoModel->getItensMaisFrequentes('sobra', $empresaId, 5);
        $faltasFrequentes = $this->produtoModel->getItensMaisFrequentes('falta', $empresaId, 5);

        // Buscar empresas para filtro
        $empresaModel = new \App\Models\EmpresaModel();
        $empresas = $empresaModel->getEmpresasAtivas();

        return $this->renderView('checklists/relatorio', [
            'titulo' => 'Relatório de Checklists',
            'registros' => $registros,
            'estatisticas' => $estatisticas,
            'sobrasFrequentes' => $sobrasFrequentes,
            'faltasFrequentes' => $faltasFrequentes,
            'empresas' => $empresas,
            'filtros' => [
                'empresa_id' => $empresaId,
                'data_inicio' => $dataInicio,
                'data_fim' => $dataFim,
                'tipo' => $tipo
            ]
        ]);
    }

    /**
     * Configurar dias da semana (admin)
     */
    public function configurarDias()
    {
        if ($check = $this->verificarLogin()) {
            return $check;
        }

        if ($check = $this->verificarAdmin()) {
            return $check;
        }

        // Buscar empresas
        $empresaModel = new \App\Models\EmpresaModel();
        $empresas = $empresaModel->getEmpresasAtivas();

        // Buscar configurações existentes
        $configuracoes = $this->configuracaoModel->findAll();
        $configMap = [];
        foreach ($configuracoes as $config) {
            $key = $config->empresa_id . '_' . $config->tipo;
            $configMap[$key] = $config;
        }

        return $this->renderView('checklists/configurar_dias', [
            'titulo' => 'Configurar Dias da Semana',
            'empresas' => $empresas,
            'configMap' => $configMap,
            'diasSemana' => \App\Models\ChecklistConfiguracaoModel::getNomesDiasSemana()
        ]);
    }

    /**
     * Salvar configuração de dias (admin)
     */
    public function salvarConfiguracao()
    {
        if ($check = $this->verificarLogin()) {
            return $check;
        }

        if ($check = $this->verificarAdmin()) {
            return $check;
        }

        $empresaId = $this->request->getPost('empresa_id');
        $tipo = $this->request->getPost('tipo');
        $dias = $this->request->getPost('dias');

        if (!$empresaId || !$tipo) {
            return redirect()->back()->with('erro', 'Empresa e tipo são obrigatórios.');
        }

        if (empty($dias)) {
            return redirect()->back()->with('erro', 'Selecione pelo menos um dia da semana.');
        }

        // Verificar se já existe configuração
        $configExistente = $this->configuracaoModel
                               ->where('empresa_id', $empresaId)
                               ->where('tipo', $tipo)
                               ->first();

        $diasString = implode(',', $dias);

        if ($configExistente) {
            // Atualizar
            $this->configuracaoModel->update($configExistente->id, [
                'dias_semana' => $diasString,
                'ativo' => 1
            ]);
        } else {
            // Criar
            $this->configuracaoModel->insert([
                'empresa_id' => $empresaId,
                'tipo' => $tipo,
                'dias_semana' => $diasString,
                'ativo' => 1
            ]);
        }

        return redirect()->to('/checklists/configurar-dias')->with('sucesso', 'Configuração salva com sucesso!');
    }

    /**
     * Desativar configuração de dias (admin)
     */
    public function desativarConfiguracao($id)
    {
        if ($check = $this->verificarLogin()) {
            return $check;
        }

        if ($check = $this->verificarAdmin()) {
            return $check;
        }

        $config = $this->configuracaoModel->find($id);

        if ($config) {
            $novoStatus = $config->ativo ? 0 : 1;
            $this->configuracaoModel->update($id, ['ativo' => $novoStatus]);
            $mensagem = $novoStatus ? 'Configuração ativada' : 'Configuração desativada';
            return redirect()->to('/checklists/configurar-dias')->with('sucesso', $mensagem);
        }

        return redirect()->back()->with('erro', 'Configuração não encontrada.');
    }

    /**
     * API: Retornar itens únicos para autocompletar
     */
    public function getItensAutocompletar()
    {
        if ($check = $this->verificarLogin()) {
            return $this->response->setJSON(['error' => 'Não autorizado']);
        }

        $tipo = $this->request->getGet('tipo'); // 'sobra' ou 'falta'

        if (!in_array($tipo, ['sobra', 'falta'])) {
            return $this->response->setJSON(['error' => 'Tipo inválido']);
        }

        // Buscar itens de TODAS as empresas para padronização global
        $itens = $this->produtoModel->getItensUnicos($tipo, null);

        // Extrair apenas os nomes dos itens
        $lista = array_column($itens, 'item');

        return $this->response->setJSON($lista);
    }

    /**
     * Gerar alertas para itens não conformes que possuem gera_alerta marcado
     */
    private function gerarAlertasNaoConformidades($registroId, $respostas, $registro)
    {
        $alertaModel = new \App\Models\ChecklistAlertaModel();

        foreach ($respostas as $itemId => $resposta) {
            // Verificar se a resposta é "Não Conforme" (conforme = 0)
            if (isset($resposta['conforme']) && $resposta['conforme'] == 0) {
                // Buscar o item para verificar se gera alerta
                $item = $this->itemModel->find($itemId);

                if ($item && $item->gera_alerta == 1) {
                    // Criar alerta
                    $alertaData = [
                        'checklist_registro_id' => $registroId,
                        'checklist_item_id' => $itemId,
                        'checklist_resposta_id' => null, // Será preenchido quando tivermos o ID da resposta
                        'empresa_id' => $registro->empresa_id,
                        'operador_id' => $registro->operador_id,
                        'descricao_item' => $item->descricao,
                        'observacao' => $resposta['observacao'] ?? null,
                        'data_ocorrencia' => $registro->data,
                        'status' => 'pendente'
                    ];

                    $alertaModel->insert($alertaData);
                }
            }
        }
    }
}
