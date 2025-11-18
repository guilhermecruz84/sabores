<?php
namespace App\Models;

use CodeIgniter\Model;

class ServicoModel extends Model
{
    protected $table            = 'servicos';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $allowedFields    = ['nome', 'ativo', 'created_at', 'updated_at'];
    protected $useTimestamps    = false;

    /**
     * Retorna apenas os nomes dos serviços ATIVOS, ordenados alfabeticamente.
     * Formato: ["Almoço", "Café", "Jantar", ...]
     */
    public function nomesAtivos(): array
    {
        $nomes = $this->select('nome')
            ->where('ativo', 1)
            ->orderBy('nome', 'ASC')
            ->findColumn('nome');

        return is_array($nomes) ? $nomes : [];
    }
}
