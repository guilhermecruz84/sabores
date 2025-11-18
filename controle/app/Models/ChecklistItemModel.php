<?php

namespace App\Models;

use CodeIgniter\Model;

class ChecklistItemModel extends Model
{
    protected $table            = 'checklists_itens';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'tipo',
        'ordem',
        'requer_foto',
        'gera_alerta',
        'descricao',
        'tipo_resposta',
        'obrigatorio',
        'ativo'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'tipo'        => 'required|in_list[abertura,encerramento]',
        'descricao'   => 'required|min_length[3]|max_length[255]',
        'tipo_resposta' => 'required|in_list[sim_nao,texto,numero,multipla_escolha]'
    ];

    /**
     * Buscar itens por tipo (abertura ou encerramento)
     */
    public function getItensPorTipo($tipo)
    {
        return $this->where('tipo', $tipo)
                    ->where('ativo', 1)
                    ->orderBy('ordem', 'ASC')
                    ->findAll();
    }

    /**
     * Buscar todos os itens ativos
     */
    public function getItensAtivos()
    {
        return $this->where('ativo', 1)
                    ->orderBy('tipo', 'ASC')
                    ->orderBy('ordem', 'ASC')
                    ->findAll();
    }

    /**
     * Reordenar itens
     */
    public function reordenar($itens)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        foreach ($itens as $ordem => $itemId) {
            $this->update($itemId, ['ordem' => $ordem]);
        }

        $db->transComplete();
        return $db->transStatus();
    }
}
