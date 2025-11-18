<?php

namespace App\Controllers;

use App\Models\ChamadoModel;
use App\Models\RespostaModel;
use App\Models\AnexoModel;
use App\Models\CategoriaModel;
use App\Models\UsuarioModel;

class Chamados extends BaseController
{
    protected $chamadoModel;
    protected $respostaModel;
    protected $anexoModel;
    protected $categoriaModel;
    protected $usuarioModel;

    public function __construct()
    {
        $this->chamadoModel = new ChamadoModel();
        $this->respostaModel = new RespostaModel();
        $this->anexoModel = new AnexoModel();
        $this->categoriaModel = new CategoriaModel();
        $this->usuarioModel = new UsuarioModel();
    }

    /**
     * Listar chamados
     */
    public function index()
    {
        if ($check = $this->verificarLogin()) {
            return $check;
        }

        // Bloquear operadores (eles só têm acesso aos checklists)
        if ($check = $this->verificarNaoOperador()) {
            return $check;
        }

        $filtros = [];

        // Aplicar filtros baseados no tipo de usuário
        if ($this->usuarioLogado->tipo === 'cliente') {
            $filtros['usuario_id'] = $this->usuarioLogado->id;
        }

        // Filtros da URL
        if ($this->request->getGet('tipo')) {
            $filtros['tipo'] = $this->request->getGet('tipo');
        }

        if ($this->request->getGet('status')) {
            $filtros['status'] = $this->request->getGet('status');
        }

        if ($this->request->getGet('categoria')) {
            $filtros['categoria'] = $this->request->getGet('categoria');
        }

        if ($this->request->getGet('busca')) {
            $filtros['busca'] = $this->request->getGet('busca');
        }

        if ($this->request->getGet('atendente_id') && in_array($this->usuarioLogado->tipo, ['admin', 'atendente'])) {
            $filtros['atendente_id'] = $this->request->getGet('atendente_id');
        }

        if ($this->request->getGet('empresa_id') && $this->usuarioLogado->tipo === 'admin') {
            $filtros['empresa_id'] = $this->request->getGet('empresa_id');
        }

        $chamados = $this->chamadoModel->getChamadosCompletos($filtros);

        // Buscar categorias e atendentes para os filtros
        $categorias = $this->categoriaModel->getCategoriasAtivas();
        $atendentes = $this->usuarioModel->getAtendentesAtivos();

        return $this->renderView('chamados/index', [
            'titulo' => 'Chamados - Sistema de Chamados',
            'chamados' => $chamados,
            'categorias' => $categorias,
            'atendentes' => $atendentes,
            'filtros' => $filtros
        ]);
    }

    /**
     * Formulário para novo chamado
     */
    public function novo()
    {
        if ($check = $this->verificarLogin()) {
            return $check;
        }

        // Apenas clientes podem criar chamados diretamente
        // Admin/atendente podem criar em nome de clientes
        $categorias = $this->categoriaModel->getCategoriasAtivas();

        return $this->renderView('chamados/novo', [
            'titulo' => 'Novo Chamado - Sistema de Chamados',
            'categorias' => $categorias
        ]);
    }

    /**
     * Criar novo chamado
     */
    public function criar()
    {
        if ($check = $this->verificarLogin()) {
            return $check;
        }

        $regras = [
            'tipo' => 'required|in_list[ocorrencia,solicitacao]',
            'assunto' => 'required|min_length[5]|max_length[255]',
            'descricao' => 'required|min_length[10]',
            'categoria' => 'permit_empty|max_length[100]'
        ];

        if (!$this->validate($regras)) {
            return redirect()->back()->withInput()->with('erros', $this->validator->getErrors());
        }

        // Preparar dados do chamado
        $data = [
            'empresa_id' => $this->usuarioLogado->empresa_id,
            'usuario_id' => $this->usuarioLogado->id,
            'tipo' => $this->request->getPost('tipo'),
            'assunto' => $this->request->getPost('assunto'),
            'descricao' => $this->request->getPost('descricao'),
            'categoria' => $this->request->getPost('categoria'),
            'status' => 'aberto'
        ];

        $chamadoId = $this->chamadoModel->insert($data);

        if ($chamadoId) {
            // Processar uploads se houver
            $this->processarUploads($chamadoId);

            $chamado = $this->chamadoModel->find($chamadoId);

            // Enviar email de notificação
            $this->enviarEmailNovoChamado($chamadoId);

            return redirect()->to('/chamados/ver/' . $chamadoId)
                ->with('sucesso', 'Chamado #' . $chamado->protocolo . ' criado com sucesso!');
        } else {
            return redirect()->back()->withInput()->with('erro', 'Erro ao criar chamado. Tente novamente.');
        }
    }

    /**
     * Ver detalhes do chamado
     */
    public function ver($id)
    {
        if ($check = $this->verificarLogin()) {
            return $check;
        }

        $chamado = $this->chamadoModel->getChamadoCompleto($id);

        if (!$chamado) {
            return redirect()->to('/chamados')->with('erro', 'Chamado não encontrado.');
        }

        // Verificar permissão
        if ($this->usuarioLogado->tipo === 'cliente' && $chamado->usuario_id != $this->usuarioLogado->id) {
            return redirect()->to('/chamados')->with('erro', 'Você não tem permissão para ver este chamado.');
        }

        // Buscar respostas
        $mostrarInternas = in_array($this->usuarioLogado->tipo, ['admin', 'atendente']);
        $respostas = $this->respostaModel->getRespostasPorChamado($id, $mostrarInternas);

        // Buscar anexos
        $anexos = $this->anexoModel->getAnexosPorChamado($id);

        // Buscar atendentes disponíveis
        $atendentes = $this->usuarioModel->getAtendentesAtivos();

        return $this->renderView('chamados/ver', [
            'titulo' => 'Chamado #' . $chamado->protocolo . ' - Sistema de Chamados',
            'chamado' => $chamado,
            'respostas' => $respostas,
            'anexos' => $anexos,
            'atendentes' => $atendentes
        ]);
    }

    /**
     * Responder chamado
     */
    public function responder($id)
    {
        if ($check = $this->verificarLogin()) {
            return $check;
        }

        $chamado = $this->chamadoModel->find($id);

        if (!$chamado) {
            return redirect()->to('/chamados')->with('erro', 'Chamado não encontrado.');
        }

        // Verificar permissão
        if ($this->usuarioLogado->tipo === 'cliente' && $chamado->usuario_id != $this->usuarioLogado->id) {
            return redirect()->to('/chamados')->with('erro', 'Você não tem permissão para responder este chamado.');
        }

        $mensagem = $this->request->getPost('mensagem');
        $interno = $this->request->getPost('interno') ? 1 : 0;

        if (empty($mensagem)) {
            return redirect()->back()->with('erro', 'A mensagem não pode estar vazia.');
        }

        // Clientes não podem criar notas internas
        if ($this->usuarioLogado->tipo === 'cliente') {
            $interno = 0;
        }

        // Criar resposta
        $respostaData = [
            'chamado_id' => $id,
            'usuario_id' => $this->usuarioLogado->id,
            'mensagem' => $mensagem,
            'interno' => $interno
        ];

        $respostaId = $this->respostaModel->insert($respostaData);

        if ($respostaId) {
            // Atualizar status do chamado
            $novoStatus = $chamado->status;

            if ($this->usuarioLogado->tipo === 'cliente') {
                // Cliente respondeu
                if ($chamado->status === 'aguardando_cliente') {
                    $novoStatus = 'em_atendimento';
                }
            } else {
                // Atendente respondeu
                if ($chamado->status === 'aberto') {
                    $novoStatus = 'em_atendimento';
                    // Atribuir ao atendente se não tiver
                    if (!$chamado->atendente_id) {
                        $this->chamadoModel->update($id, ['atendente_id' => $this->usuarioLogado->id]);
                    }
                } elseif ($chamado->status === 'em_atendimento') {
                    $novoStatus = 'aguardando_cliente';
                }
            }

            if ($novoStatus !== $chamado->status) {
                $this->chamadoModel->atualizarStatus($id, $novoStatus);
            }

            // Processar uploads se houver
            $this->processarUploads($id, $respostaId);

            return redirect()->back()->with('sucesso', 'Resposta adicionada com sucesso!');
        } else {
            return redirect()->back()->with('erro', 'Erro ao adicionar resposta. Tente novamente.');
        }
    }

    /**
     * Finalizar chamado (apenas cliente)
     */
    public function finalizar($id)
    {
        if ($check = $this->verificarLogin()) {
            return $check;
        }

        $chamado = $this->chamadoModel->find($id);

        if (!$chamado) {
            return redirect()->to('/chamados')->with('erro', 'Chamado não encontrado.');
        }

        // Apenas o cliente que abriu pode finalizar
        if ($chamado->usuario_id != $this->usuarioLogado->id) {
            return redirect()->to('/chamados')->with('erro', 'Você não tem permissão para finalizar este chamado.');
        }

        // Avaliação (opcional)
        $avaliacao = $this->request->getPost('avaliacao');
        $comentario = $this->request->getPost('comentario_avaliacao');

        $this->chamadoModel->atualizarStatus($id, 'finalizado');

        if ($avaliacao) {
            $this->chamadoModel->avaliar($id, $avaliacao, $comentario);
        }

        return redirect()->to('/chamados/ver/' . $id)->with('sucesso', 'Chamado finalizado com sucesso!');
    }

    /**
     * Atribuir chamado a um atendente (apenas admin/atendente)
     */
    public function atribuir($id)
    {
        if ($check = $this->verificarLogin()) {
            return $check;
        }

        if ($check = $this->verificarStaff()) {
            return $check;
        }

        $atendenteId = $this->request->getPost('atendente_id');

        if (empty($atendenteId)) {
            return redirect()->back()->with('erro', 'Selecione um atendente.');
        }

        $this->chamadoModel->update($id, ['atendente_id' => $atendenteId]);

        return redirect()->back()->with('sucesso', 'Chamado atribuído com sucesso!');
    }

    /**
     * Processar uploads de arquivos
     */
    protected function processarUploads($chamadoId, $respostaId = null)
    {
        $arquivos = $this->request->getFiles();

        if (empty($arquivos['anexos'])) {
            return;
        }

        foreach ($arquivos['anexos'] as $arquivo) {
            if ($arquivo->isValid() && !$arquivo->hasMoved()) {
                // Gerar nome único
                $nomeOriginal = $arquivo->getName();
                $extensao = $arquivo->getExtension();
                $nomeArquivo = uniqid() . '_' . time() . '.' . $extensao;

                // Mover arquivo
                $caminhoUpload = FCPATH . 'uploads/chamados/';
                if (!is_dir($caminhoUpload)) {
                    mkdir($caminhoUpload, 0777, true);
                }

                $arquivo->move($caminhoUpload, $nomeArquivo);

                // Salvar no banco
                $anexoData = [
                    'chamado_id' => $chamadoId,
                    'resposta_id' => $respostaId,
                    'usuario_id' => $this->usuarioLogado->id,
                    'nome_original' => $nomeOriginal,
                    'nome_arquivo' => $nomeArquivo,
                    'tipo' => $arquivo->getMimeType(),
                    'tamanho' => $arquivo->getSize()
                ];

                $this->anexoModel->insert($anexoData);
            }
        }
    }

    /**
     * Download de anexo
     */
    public function download($anexoId)
    {
        if ($check = $this->verificarLogin()) {
            return $check;
        }

        $anexo = $this->anexoModel->find($anexoId);

        if (!$anexo) {
            return redirect()->back()->with('erro', 'Anexo não encontrado.');
        }

        // Verificar permissão
        $chamado = $this->chamadoModel->find($anexo->chamado_id);
        if ($this->usuarioLogado->tipo === 'cliente' && $chamado->usuario_id != $this->usuarioLogado->id) {
            return redirect()->back()->with('erro', 'Você não tem permissão para baixar este anexo.');
        }

        $caminhoArquivo = FCPATH . 'uploads/chamados/' . $anexo->nome_arquivo;

        if (!file_exists($caminhoArquivo)) {
            return redirect()->back()->with('erro', 'Arquivo não encontrado.');
        }

        return $this->response->download($caminhoArquivo, null)->setFileName($anexo->nome_original);
    }

    /**
     * Enviar email de notificação de novo chamado
     */
    protected function enviarEmailNovoChamado($chamadoId)
    {
        try {
            // Buscar dados completos do chamado
            $chamadoCompleto = $this->chamadoModel->getChamadoCompleto($chamadoId);

            if (!$chamadoCompleto) {
                log_message('error', 'Chamado não encontrado para envio de email: ' . $chamadoId);
                return false;
            }

            // Buscar dados do cliente
            $cliente = $this->usuarioModel->find($chamadoCompleto->usuario_id);

            // Buscar dados da empresa
            $empresaModel = new \App\Models\EmpresaModel();
            $empresa = $empresaModel->find($chamadoCompleto->empresa_id);

            // Preparar dados para o email
            $chamadoArray = [
                'id' => $chamadoCompleto->id,
                'titulo' => $chamadoCompleto->assunto,
                'descricao' => $chamadoCompleto->descricao,
                'prioridade' => $chamadoCompleto->prioridade ?? 'media',
                'created_at' => $chamadoCompleto->created_at
            ];

            $clienteArray = [
                'nome' => $cliente->nome,
                'email' => $cliente->email
            ];

            $empresaArray = [
                'nome' => $empresa->nome ?? 'Não especificada'
            ];

            // Link para visualizar o chamado
            $linkChamado = base_url('chamados/ver/' . $chamadoId);

            // Renderizar template de email
            $mensagem = view('emails/novo_chamado', [
                'chamado' => $chamadoArray,
                'cliente' => $clienteArray,
                'empresa' => $empresaArray,
                'linkChamado' => $linkChamado
            ]);

            // Configurar email
            $email = \Config\Services::email();

            $config = [
                'protocol' => getenv('email.protocol') ?: 'smtp',
                'SMTPHost' => getenv('email.SMTPHost') ?: 'br404.hostgator.com.br',
                'SMTPUser' => getenv('email.SMTPUser') ?: 'noreply@saboresemmovimento.com.br',
                'SMTPPass' => getenv('email.SMTPPass') ?: '',
                'SMTPPort' => getenv('email.SMTPPort') ?: 587,
                'SMTPCrypto' => getenv('email.SMTPCrypto') ?: 'tls',
                'mailType' => 'html',
                'charset' => 'utf-8',
                'newline' => "\r\n"
            ];

            $email->initialize($config);

            $fromEmail = getenv('email.fromEmail') ?: 'noreply@saboresemmovimento.com.br';
            $fromName = getenv('email.fromName') ?: 'Sistema Sabores';
            $recipients = getenv('email.recipients') ?: 'contato@saboresemmovimento.com.br';

            $email->setFrom($fromEmail, $fromName);
            $email->setTo($recipients);
            $email->setSubject('🎫 Novo Chamado #' . $chamadoId . ' - ' . $chamadoCompleto->assunto);
            $email->setMessage($mensagem);

            // Enviar email
            if ($email->send()) {
                log_message('info', 'Email de novo chamado enviado com sucesso. ID: ' . $chamadoId);
                return true;
            } else {
                log_message('error', 'Erro ao enviar email de novo chamado. ID: ' . $chamadoId . ' - ' . $email->printDebugger(['headers']));
                return false;
            }

        } catch (\Exception $e) {
            log_message('error', 'Exceção ao enviar email de novo chamado: ' . $e->getMessage());
            return false;
        }
    }
}
