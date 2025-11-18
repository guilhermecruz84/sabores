<?php

namespace App\Models;

use CodeIgniter\Model;

class ChecklistProdutoModel extends Model
{
    protected $table            = 'checklists_produtos';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'registro_id',
        'tipo_registro',
        'item',
        'quantidade',
        'observacao',
        'foto'
    ];

    // Dates
    protected $useTimestamps = false;
    protected $createdField  = 'created_at';

    // Validation
    protected $validationRules = [
        'registro_id'   => 'required|integer',
        'tipo_registro' => 'required|in_list[sobra,falta]',
        'item'          => 'required|min_length[2]|max_length[255]'
    ];

    /**
     * Buscar produtos por registro
     */
    public function getProdutosPorRegistro($registroId)
    {
        return $this->where('registro_id', $registroId)
                    ->orderBy('tipo_registro', 'ASC')
                    ->orderBy('item', 'ASC')
                    ->findAll();
    }

    /**
     * Buscar sobras de um registro
     */
    public function getSobras($registroId)
    {
        return $this->where('registro_id', $registroId)
                    ->where('tipo_registro', 'sobra')
                    ->findAll();
    }

    /**
     * Buscar faltas de um registro
     */
    public function getFaltas($registroId)
    {
        return $this->where('registro_id', $registroId)
                    ->where('tipo_registro', 'falta')
                    ->findAll();
    }

    /**
     * Salvar múltiplos produtos
     */
    public function salvarProdutos($registroId, $produtos, $tipoRegistro)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        // Deletar produtos anteriores do tipo
        $this->where('registro_id', $registroId)
             ->where('tipo_registro', $tipoRegistro)
             ->delete();

        // Inserir novos produtos
        foreach ($produtos as $produto) {
            if (empty($produto['item'])) {
                continue;
            }

            $this->insert([
                'registro_id'   => $registroId,
                'tipo_registro' => $tipoRegistro,
                'item'          => $produto['item'],
                'quantidade'    => $produto['quantidade'] ?? null,
                'observacao'    => $produto['observacao'] ?? null
            ]);
        }

        $db->transComplete();
        return $db->transStatus();
    }

    /**
     * Relatório de itens mais frequentes
     */
    public function getItensMaisFrequentes($tipoRegistro, $empresaId = null, $limit = 10)
    {
        $db = \Config\Database::connect();
        $builder = $db->table($this->table);

        $builder->select('checklists_produtos.item, COUNT(*) as total')
                ->join('checklists_registros', 'checklists_registros.id = checklists_produtos.registro_id')
                ->where('checklists_produtos.tipo_registro', $tipoRegistro);

        if ($empresaId) {
            $builder->where('checklists_registros.empresa_id', $empresaId);
        }

        return $builder->groupBy('checklists_produtos.item')
                       ->orderBy('total', 'DESC')
                       ->limit($limit)
                       ->get()
                       ->getResult();
    }

    /**
     * Buscar lista de itens únicos para autocompletar
     */
    public function getItensUnicos($tipoRegistro, $empresaId = null)
    {
        $db = \Config\Database::connect();
        $builder = $db->table($this->table);

        $builder->select('DISTINCT checklists_produtos.item')
                ->join('checklists_registros', 'checklists_registros.id = checklists_produtos.registro_id')
                ->where('checklists_produtos.tipo_registro', $tipoRegistro)
                ->where('checklists_produtos.item !=', '');

        if ($empresaId) {
            $builder->where('checklists_registros.empresa_id', $empresaId);
        }

        return $builder->orderBy('checklists_produtos.item', 'ASC')
                       ->get()
                       ->getResultArray();
    }
}
