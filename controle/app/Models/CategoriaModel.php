<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoriaModel extends Model
{
    protected $table            = 'categorias';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nome',
        'tipo',
        'icone',
        'cor',
        'ativo',
        'ordem'
    ];

    /**
     * Buscar categorias ativas
     */
    public function getCategoriasAtivas($tipo = null)
    {
        $this->where('ativo', 1);

        if ($tipo) {
            $this->groupStart();
            $this->where('tipo', $tipo);
            $this->orWhere('tipo', 'ambos');
            $this->groupEnd();
        }

        $this->orderBy('ordem', 'ASC');
        $this->orderBy('nome', 'ASC');

        return $this->findAll();
    }

    /**
     * Buscar categoria por nome
     */
    public function getCategoriaPorNome($nome)
    {
        return $this->where('nome', $nome)->where('ativo', 1)->first();
    }
}
