<?php

namespace App\Models;

use CodeIgniter\Model;

class ChamadoModel extends Model
{
    protected $table            = 'chamados';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'protocolo',
        'empresa_id',
        'usuario_id',
        'tipo',
        'categoria',
        'assunto',
        'descricao',
        'prioridade',
        'status',
        'atendente_id',
        'data_finalizacao',
        'avaliacao',
        'comentario_avaliacao'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'tipo'      => 'required|in_list[ocorrencia,solicitacao]',
        'assunto'   => 'required|min_length[5]|max_length[255]',
        'descricao' => 'required|min_length[10]'
    ];

    // Callbacks
    protected $beforeInsert = ['gerarProtocolo'];

    /**
     * Gerar protocolo único para o chamado
     */
    protected function gerarProtocolo(array $data)
    {
        if (!isset($data['data']['protocolo'])) {
            $ano = date('Y');
            $numero = str_pad($this->countAll() + 1, 6, '0', STR_PAD_LEFT);
            $data['data']['protocolo'] = $ano . $numero;
        }
        return $data;
    }

    /**
     * Buscar chamados com informações relacionadas
     */
    public function getChamadosCompletos($filtros = [])
    {
        $this->select('chamados.*,
                      empresas.nome_fantasia as empresa_nome,
                      usuarios.nome as usuario_nome,
                      atendente.nome as atendente_nome');
        $this->join('empresas', 'empresas.id = chamados.empresa_id', 'left');
        $this->join('usuarios', 'usuarios.id = chamados.usuario_id', 'left');
        $this->join('usuarios as atendente', 'atendente.id = chamados.atendente_id', 'left');

        // Aplicar filtros
        if (isset($filtros['empresa_id'])) {
            $this->where('chamados.empresa_id', $filtros['empresa_id']);
        }

        if (isset($filtros['usuario_id'])) {
            $this->where('chamados.usuario_id', $filtros['usuario_id']);
        }

        if (isset($filtros['tipo'])) {
            $this->where('chamados.tipo', $filtros['tipo']);
        }

        if (isset($filtros['status'])) {
            $this->where('chamados.status', $filtros['status']);
        }

        if (isset($filtros['atendente_id'])) {
            $this->where('chamados.atendente_id', $filtros['atendente_id']);
        }

        if (isset($filtros['categoria'])) {
            $this->where('chamados.categoria', $filtros['categoria']);
        }

        if (isset($filtros['prioridade'])) {
            $this->where('chamados.prioridade', $filtros['prioridade']);
        }

        if (isset($filtros['busca']) && !empty($filtros['busca'])) {
            $this->groupStart();
            $this->like('chamados.protocolo', $filtros['busca']);
            $this->orLike('chamados.assunto', $filtros['busca']);
            $this->orLike('chamados.descricao', $filtros['busca']);
            $this->groupEnd();
        }

        $this->orderBy('chamados.created_at', 'DESC');

        return $this->findAll();
    }

    /**
     * Buscar chamado completo por ID
     */
    public function getChamadoCompleto($id)
    {
        $this->select('chamados.*,
                      empresas.nome_fantasia as empresa_nome,
                      empresas.telefone as empresa_telefone,
                      empresas.email as empresa_email,
                      usuarios.nome as usuario_nome,
                      usuarios.email as usuario_email,
                      usuarios.telefone as usuario_telefone,
                      atendente.nome as atendente_nome,
                      atendente.email as atendente_email');
        $this->join('empresas', 'empresas.id = chamados.empresa_id', 'left');
        $this->join('usuarios', 'usuarios.id = chamados.usuario_id', 'left');
        $this->join('usuarios as atendente', 'atendente.id = chamados.atendente_id', 'left');
        $this->where('chamados.id', $id);

        return $this->first();
    }

    /**
     * Contar chamados por status
     */
    public function contarPorStatus($empresaId = null, $usuarioId = null)
    {
        $db = \Config\Database::connect();
        $builder = $db->table($this->table);

        $builder->select('status, COUNT(*) as total');

        if ($empresaId) {
            $builder->where('empresa_id', $empresaId);
        }

        if ($usuarioId) {
            $builder->where('usuario_id', $usuarioId);
        }

        $builder->groupBy('status');

        $resultado = $builder->get()->getResult();

        $contagem = [
            'aberto' => 0,
            'em_atendimento' => 0,
            'aguardando_cliente' => 0,
            'finalizado' => 0
        ];

        foreach ($resultado as $row) {
            $contagem[$row->status] = $row->total;
        }

        return $contagem;
    }

    /**
     * Contar chamados por tipo
     */
    public function contarPorTipo($empresaId = null, $usuarioId = null)
    {
        $db = \Config\Database::connect();
        $builder = $db->table($this->table);

        $builder->select('tipo, COUNT(*) as total');

        if ($empresaId) {
            $builder->where('empresa_id', $empresaId);
        }

        if ($usuarioId) {
            $builder->where('usuario_id', $usuarioId);
        }

        $builder->groupBy('tipo');

        $resultado = $builder->get()->getResult();

        $contagem = [
            'ocorrencia' => 0,
            'solicitacao' => 0
        ];

        foreach ($resultado as $row) {
            $contagem[$row->tipo] = $row->total;
        }

        return $contagem;
    }

    /**
     * Buscar estatísticas gerais
     */
    public function getEstatisticas($empresaId = null, $usuarioId = null, $periodo = 30)
    {
        $db = \Config\Database::connect();
        $builder = $db->table($this->table);

        $builder->select('
            COUNT(*) as total,
            COUNT(CASE WHEN status = "aberto" THEN 1 END) as abertos,
            COUNT(CASE WHEN status = "em_atendimento" THEN 1 END) as em_atendimento,
            COUNT(CASE WHEN status = "aguardando_cliente" THEN 1 END) as aguardando_cliente,
            COUNT(CASE WHEN status = "finalizado" THEN 1 END) as finalizados,
            COUNT(CASE WHEN tipo = "ocorrencia" THEN 1 END) as ocorrencias,
            COUNT(CASE WHEN tipo = "solicitacao" THEN 1 END) as solicitacoes,
            AVG(avaliacao) as media_avaliacao
        ');

        if ($empresaId) {
            $builder->where('empresa_id', $empresaId);
        }

        if ($usuarioId) {
            $builder->where('usuario_id', $usuarioId);
        }

        if ($periodo) {
            $builder->where('created_at >=', date('Y-m-d', strtotime("-{$periodo} days")));
        }

        return $builder->get()->getRow();
    }

    /**
     * Atualizar status do chamado
     */
    public function atualizarStatus($id, $status, $atendenteId = null)
    {
        $data = ['status' => $status];

        if ($status === 'finalizado') {
            $data['data_finalizacao'] = date('Y-m-d H:i:s');
        }

        if ($atendenteId && $status === 'em_atendimento') {
            $data['atendente_id'] = $atendenteId;
        }

        return $this->update($id, $data);
    }

    /**
     * Adicionar avaliação ao chamado
     */
    public function avaliar($id, $avaliacao, $comentario = null)
    {
        return $this->update($id, [
            'avaliacao' => $avaliacao,
            'comentario_avaliacao' => $comentario
        ]);
    }
}
