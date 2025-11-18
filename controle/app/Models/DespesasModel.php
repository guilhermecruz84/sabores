<?php
namespace App\Models;

use CodeIgniter\Model;

class DespesaModel extends Model
{
    protected $table            = 'despesas';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useTimestamps    = false; // vamos preencher manualmente
    protected $allowedFields    = [
        'descricao','categoria','valor','data','status','criado_em','atualizado_em'
    ];

    protected $validationRules  = [
        'descricao' => 'required|min_length[3]|max_length[255]',
        'valor'     => 'required|numeric',
        'data'      => 'required|valid_date[Y-m-d]',
        'status'    => 'permit_empty|in_list[0,1]',
        'categoria' => 'permit_empty|max_length[100]',
    ];
    protected $validationMessages = [
        'descricao' => [
            'required'    => 'A descrição é obrigatória.',
            'min_length'  => 'A descrição deve ter ao menos 3 caracteres.'
        ],
        'valor' => [
            'required' => 'O valor é obrigatório.',
            'numeric'  => 'Informe um valor numérico, ex.: 1234.56'
        ],
        'data' => [
            'required'   => 'A data é obrigatória.',
            'valid_date' => 'Use o formato AAAA-MM-DD.'
        ],
    ];

    /** Busca com filtros simples (q, período, categoria, status) */
    public function listarComFiltros(array $f = [])
    {
        $builder = $this->builder()->orderBy('data','DESC')->orderBy('id','DESC');

        if (!empty($f['q'])) {
            $q = trim($f['q']);
            $builder->groupStart()
                ->like('descricao', $q)
                ->orLike('categoria', $q)
            ->groupEnd();
        }
        if (!empty($f['data_ini'])) $builder->where('data >=', $f['data_ini']);
        if (!empty($f['data_fim'])) $builder->where('data <=', $f['data_fim']);
        if ($f['status'] !== '' && $f['status'] !== null) {
            $builder->where('status', (int)$f['status']);
        }
        if (!empty($f['categoria'])) $builder->where('categoria', $f['categoria']);

        return $builder->get()->getResultArray();
    }

    /** Totalizador com os mesmos filtros */
    public function somarComFiltros(array $f = []): float
    {
        $builder = $this->builder()->select('SUM(valor) as total');

        if (!empty($f['q'])) {
            $q = trim($f['q']);
            $builder->groupStart()
                ->like('descricao', $q)
                ->orLike('categoria', $q)
            ->groupEnd();
        }
        if (!empty($f['data_ini'])) $builder->where('data >=', $f['data_ini']);
        if (!empty($f['data_fim'])) $builder->where('data <=', $f['data_fim']);
        if ($f['status'] !== '' && $f['status'] !== null) {
            $builder->where('status', (int)$f['status']);
        }
        if (!empty($f['categoria'])) $builder->where('categoria', $f['categoria']);

        $row = $builder->get()->getRowArray();
        return (float)($row['total'] ?? 0);
    }
}
