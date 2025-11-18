<?php

namespace App\Models;

use CodeIgniter\Model;

class ChecklistRegistroModel extends Model
{
    protected $table            = 'checklists_registros';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'empresa_id',
        'operador_id',
        'data',
        'tipo',
        'status',
        'observacoes',
        'finalizado_em'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'empresa_id'  => 'required|integer',
        'operador_id' => 'required|integer',
        'data'        => 'required|valid_date',
        'tipo'        => 'required|in_list[abertura,encerramento]'
    ];

    /**
     * Buscar registro com informações de empresa e operador
     */
    public function getRegistroCompleto($id)
    {
        return $this->select('checklists_registros.*,
                             empresas.nome_fantasia as empresa_nome,
                             usuarios.nome as operador_nome')
                    ->join('empresas', 'empresas.id = checklists_registros.empresa_id')
                    ->join('usuarios', 'usuarios.id = checklists_registros.operador_id')
                    ->find($id);
    }

    /**
     * Verificar se já existe um registro para o operador na data e tipo
     */
    public function verificarRegistroExistente($operadorId, $data, $tipo)
    {
        return $this->where('operador_id', $operadorId)
                    ->where('data', $data)
                    ->where('tipo', $tipo)
                    ->first();
    }

    /**
     * Buscar registros por empresa
     */
    public function getRegistrosPorEmpresa($empresaId, $dataInicio = null, $dataFim = null)
    {
        $this->select('checklists_registros.*,
                      usuarios.nome as operador_nome')
             ->join('usuarios', 'usuarios.id = checklists_registros.operador_id')
             ->where('checklists_registros.empresa_id', $empresaId);

        if ($dataInicio) {
            $this->where('checklists_registros.data >=', $dataInicio);
        }

        if ($dataFim) {
            $this->where('checklists_registros.data <=', $dataFim);
        }

        return $this->orderBy('checklists_registros.data', 'DESC')
                    ->orderBy('checklists_registros.tipo', 'ASC')
                    ->findAll();
    }

    /**
     * Buscar registros por operador
     */
    public function getRegistrosPorOperador($operadorId, $limit = 10)
    {
        return $this->where('operador_id', $operadorId)
                    ->orderBy('data', 'DESC')
                    ->orderBy('tipo', 'ASC')
                    ->findAll($limit);
    }

    /**
     * Buscar registros pendentes do operador
     */
    public function getRegistrosPendentes($operadorId)
    {
        return $this->where('operador_id', $operadorId)
                    ->where('data', date('Y-m-d'))
                    ->whereIn('status', ['pendente', 'em_andamento'])
                    ->findAll();
    }

    /**
     * Finalizar registro
     */
    public function finalizarRegistro($id)
    {
        return $this->update($id, [
            'status' => 'finalizado',
            'finalizado_em' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Estatísticas de checklists
     */
    public function getEstatisticas($empresaId = null, $dataInicio = null, $dataFim = null)
    {
        $db = \Config\Database::connect();
        $builder = $db->table($this->table);

        if ($empresaId) {
            $builder->where('empresa_id', $empresaId);
        }

        if ($dataInicio) {
            $builder->where('data >=', $dataInicio);
        }

        if ($dataFim) {
            $builder->where('data <=', $dataFim);
        }

        $builder->select('COUNT(*) as total,
                         SUM(CASE WHEN status = "finalizado" THEN 1 ELSE 0 END) as finalizados,
                         SUM(CASE WHEN status = "pendente" THEN 1 ELSE 0 END) as pendentes,
                         SUM(CASE WHEN tipo = "abertura" THEN 1 ELSE 0 END) as aberturas,
                         SUM(CASE WHEN tipo = "encerramento" THEN 1 ELSE 0 END) as encerramentos');

        return $builder->get()->getRow();
    }
}
