<?php

namespace App\Models;

use CodeIgniter\Model;

class CardapioModel extends Model
{
    protected $table            = 'cardapios';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'empresa_id',
        'data',
        'descricao'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Buscar cardápio por empresa e data
     */
    public function getCardapioPorData($empresaId, $data)
    {
        return $this->where('empresa_id', $empresaId)
                    ->where('data', $data)
                    ->first();
    }

    /**
     * Buscar cardápios recentes de uma empresa
     */
    public function getCardapiosRecentes($empresaId, $limite = 30)
    {
        return $this->where('empresa_id', $empresaId)
                    ->orderBy('data', 'DESC')
                    ->limit($limite)
                    ->findAll();
    }

    /**
     * Buscar cardápios com avaliações
     */
    public function getCardapiosComAvaliacoes($empresaId, $dataInicio = null, $dataFim = null)
    {
        $builder = $this->db->table($this->table);
        $builder->select('cardapios.*,
                         COUNT(avaliacoes_cardapio.id) as total_avaliacoes,
                         AVG(CASE
                             WHEN avaliacoes_cardapio.avaliacao = "otimo" THEN 4
                             WHEN avaliacoes_cardapio.avaliacao = "bom" THEN 3
                             WHEN avaliacoes_cardapio.avaliacao = "regular" THEN 2
                             WHEN avaliacoes_cardapio.avaliacao = "ruim" THEN 1
                         END) as media_numerica');
        $builder->join('avaliacoes_cardapio', 'avaliacoes_cardapio.cardapio_id = cardapios.id', 'left');
        $builder->where('cardapios.empresa_id', $empresaId);

        if ($dataInicio) {
            $builder->where('cardapios.data >=', $dataInicio);
        }

        if ($dataFim) {
            $builder->where('cardapios.data <=', $dataFim);
        }

        $builder->groupBy('cardapios.id');
        $builder->orderBy('cardapios.data', 'DESC');

        return $builder->get()->getResult();
    }

    /**
     * Verificar se cardápio já existe
     */
    public function cardapioExiste($empresaId, $data)
    {
        return $this->where('empresa_id', $empresaId)
                    ->where('data', $data)
                    ->countAllResults() > 0;
    }

    /**
     * Buscar cardápios por mês/ano
     */
    public function getCardapiosPorMes($empresaId, $mes, $ano)
    {
        return $this->where('empresa_id', $empresaId)
                    ->where('MONTH(data)', $mes)
                    ->where('YEAR(data)', $ano)
                    ->orderBy('data', 'DESC')
                    ->findAll();
    }
}
