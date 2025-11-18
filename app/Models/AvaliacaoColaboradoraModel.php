<?php

namespace App\Models;

use CodeIgniter\Model;

class AvaliacaoColaboradoraModel extends Model
{
    protected $table            = 'avaliacoes_colaboradores';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'empresa_id',
        'data',
        'avaliacao',
        'motivo'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Verificar se já foi avaliado para uma data/empresa
     */
    public function jaAvaliado($empresaId, $data)
    {
        return $this->where('empresa_id', $empresaId)
                    ->where('data', $data)
                    ->countAllResults() > 0;
    }

    /**
     * Buscar avaliação por empresa e data
     */
    public function getAvaliacaoPorData($empresaId, $data)
    {
        return $this->where('empresa_id', $empresaId)
                    ->where('data', $data)
                    ->first();
    }

    /**
     * Buscar avaliações por empresa
     */
    public function getAvaliacoesPorEmpresa($empresaId, $dataInicio = null, $dataFim = null)
    {
        $builder = $this->db->table($this->table);
        $builder->select('avaliacoes_colaboradores.*,
                         empresas.nome_fantasia as empresa_nome');
        $builder->join('empresas', 'empresas.id = avaliacoes_colaboradores.empresa_id');
        $builder->where('avaliacoes_colaboradores.empresa_id', $empresaId);

        if ($dataInicio) {
            $builder->where('avaliacoes_colaboradores.data >=', $dataInicio);
        }

        if ($dataFim) {
            $builder->where('avaliacoes_colaboradores.data <=', $dataFim);
        }

        $builder->orderBy('avaliacoes_colaboradores.data', 'DESC');

        return $builder->get()->getResult();
    }

    /**
     * Buscar estatísticas de avaliação por empresa (mês/ano)
     */
    public function getEstatisticasPorEmpresa($empresaId = null, $mes = null, $ano = null)
    {
        if (!$mes) {
            $mes = date('m');
        }
        if (!$ano) {
            $ano = date('Y');
        }

        $builder = $this->db->table($this->table);
        $builder->select('empresa_id,
                         empresas.nome_fantasia as empresa_nome,
                         COUNT(*) as total_avaliacoes,
                         SUM(CASE WHEN avaliacao = "otimo" THEN 1 ELSE 0 END) as total_otimo,
                         SUM(CASE WHEN avaliacao = "bom" THEN 1 ELSE 0 END) as total_bom,
                         SUM(CASE WHEN avaliacao = "regular" THEN 1 ELSE 0 END) as total_regular,
                         SUM(CASE WHEN avaliacao = "ruim" THEN 1 ELSE 0 END) as total_ruim,
                         AVG(CASE
                             WHEN avaliacao = "otimo" THEN 4
                             WHEN avaliacao = "bom" THEN 3
                             WHEN avaliacao = "regular" THEN 2
                             WHEN avaliacao = "ruim" THEN 1
                         END) as media_numerica');
        $builder->join('empresas', 'empresas.id = avaliacoes_colaboradores.empresa_id');
        $builder->where('MONTH(avaliacoes_colaboradores.data)', $mes);
        $builder->where('YEAR(avaliacoes_colaboradores.data)', $ano);

        if ($empresaId) {
            $builder->where('avaliacoes_colaboradores.empresa_id', $empresaId);
        }

        $builder->groupBy('avaliacoes_colaboradores.empresa_id');
        $builder->orderBy('empresas.nome_fantasia');

        return $builder->get()->getResult();
    }

    /**
     * Buscar todas as avaliações com detalhes
     */
    public function getAvaliacoesDetalhadas($empresaId = null, $dataInicio = null, $dataFim = null)
    {
        $builder = $this->db->table($this->table);
        $builder->select('avaliacoes_colaboradores.*,
                         empresas.nome_fantasia as empresa_nome');
        $builder->join('empresas', 'empresas.id = avaliacoes_colaboradores.empresa_id');

        if ($empresaId) {
            $builder->where('avaliacoes_colaboradores.empresa_id', $empresaId);
        }

        if ($dataInicio) {
            $builder->where('avaliacoes_colaboradores.data >=', $dataInicio);
        }

        if ($dataFim) {
            $builder->where('avaliacoes_colaboradores.data <=', $dataFim);
        }

        $builder->orderBy('avaliacoes_colaboradores.data', 'DESC');
        $builder->orderBy('empresas.nome_fantasia');

        return $builder->get()->getResult();
    }

    /**
     * Converter média numérica para texto
     */
    public static function mediaParaTexto($media)
    {
        if ($media >= 3.5) {
            return 'Ótimo';
        } elseif ($media >= 2.5) {
            return 'Bom';
        } elseif ($media >= 1.5) {
            return 'Regular';
        } else {
            return 'Ruim';
        }
    }

    /**
     * Converter média numérica para cor
     */
    public static function mediaParaCor($media)
    {
        if ($media >= 3.5) {
            return 'success';
        } elseif ($media >= 2.5) {
            return 'primary';
        } elseif ($media >= 1.5) {
            return 'warning';
        } else {
            return 'danger';
        }
    }
}
