<?php

namespace App\Models;

use CodeIgniter\Model;

class ChecklistAlertaModel extends Model
{
    protected $table            = 'checklist_alertas';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'checklist_registro_id',
        'checklist_item_id',
        'checklist_resposta_id',
        'empresa_id',
        'operador_id',
        'descricao_item',
        'observacao',
        'data_ocorrencia',
        'status',
        'concluido_por',
        'concluido_em'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Buscar alertas pendentes do dia
     */
    public function getAlertasPendentesHoje()
    {
        return $this->select('checklist_alertas.*,
                             empresas.nome_fantasia as empresa_nome,
                             usuarios.nome as operador_nome')
                    ->join('empresas', 'empresas.id = checklist_alertas.empresa_id', 'left')
                    ->join('usuarios', 'usuarios.id = checklist_alertas.operador_id', 'left')
                    ->where('checklist_alertas.status', 'pendente')
                    ->where('checklist_alertas.data_ocorrencia', date('Y-m-d'))
                    ->orderBy('checklist_alertas.created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Buscar todos os alertas pendentes
     */
    public function getAlertasPendentes()
    {
        return $this->select('checklist_alertas.*,
                             empresas.nome_fantasia as empresa_nome,
                             usuarios.nome as operador_nome')
                    ->join('empresas', 'empresas.id = checklist_alertas.empresa_id', 'left')
                    ->join('usuarios', 'usuarios.id = checklist_alertas.operador_id', 'left')
                    ->where('checklist_alertas.status', 'pendente')
                    ->orderBy('checklist_alertas.data_ocorrencia', 'DESC')
                    ->orderBy('checklist_alertas.created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Marcar alerta como concluído
     */
    public function marcarComoConcluido($alertaId, $usuarioId)
    {
        return $this->update($alertaId, [
            'status' => 'concluido',
            'concluido_por' => $usuarioId,
            'concluido_em' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Contar alertas pendentes do dia
     */
    public function countAlertasPendentesHoje()
    {
        return $this->where('status', 'pendente')
                    ->where('data_ocorrencia', date('Y-m-d'))
                    ->countAllResults();
    }

    /**
     * Buscar todos os alertas com informações relacionadas
     */
    public function getAllWithRelations()
    {
        return $this->select('checklist_alertas.*,
                             empresas.nome_fantasia as empresa_nome,
                             usuarios.nome as operador_nome,
                             concluido.nome as concluido_por_nome')
                    ->join('empresas', 'empresas.id = checklist_alertas.empresa_id', 'left')
                    ->join('usuarios', 'usuarios.id = checklist_alertas.operador_id', 'left')
                    ->join('usuarios as concluido', 'concluido.id = checklist_alertas.concluido_por', 'left')
                    ->orderBy('checklist_alertas.data_ocorrencia', 'DESC')
                    ->orderBy('checklist_alertas.created_at', 'DESC')
                    ->findAll();
    }
}
