<?php

namespace App\Models;

use CodeIgniter\Model;

class EmpresaModel extends Model
{
    protected $table            = 'empresas';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'razao_social',
        'nome_fantasia',
        'cnpj',
        'telefone',
        'email',
        'endereco',
        'ativo'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'razao_social'  => 'required|min_length[3]|max_length[255]',
        'nome_fantasia' => 'required|min_length[3]|max_length[255]',
        'cnpj'          => 'permit_empty|exact_length[18]|is_unique[empresas.cnpj,id,{id}]',
        'email'         => 'permit_empty|valid_email'
    ];

    protected $validationMessages = [
        'cnpj' => [
            'is_unique' => 'Este CNPJ já está cadastrado.'
        ]
    ];

    /**
     * Buscar apenas empresas ativas
     */
    public function getEmpresasAtivas()
    {
        return $this->where('ativo', 1)
                    ->orderBy('nome_fantasia', 'ASC')
                    ->findAll();
    }

    /**
     * Buscar empresa por CNPJ
     */
    public function findByCnpj($cnpj)
    {
        return $this->where('cnpj', $cnpj)->first();
    }

    /**
     * Buscar empresas com estatísticas (usuários e chamados)
     */
    public function getEmpresasComEstatisticas()
    {
        $builder = $this->db->table('empresas e');

        $builder->select('e.*,
                         COUNT(DISTINCT u.id) as total_usuarios,
                         COUNT(DISTINCT c.id) as total_chamados')
                ->join('usuarios u', 'u.empresa_id = e.id', 'left')
                ->join('chamados c', 'c.empresa_id = e.id', 'left')
                ->groupBy('e.id')
                ->orderBy('e.nome_fantasia', 'ASC');

        $query = $builder->get();
        return $query->getResult();
    }
}
