<?php

namespace App\Models;

use CodeIgniter\Model;

class AvaliacaoCardapioModel extends Model
{
    protected $table            = 'avaliacoes_cardapio';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'cardapio_id',
        'cliente_id',
        'empresa_id',
        'tipo_avaliacao',
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
     * Verificar se cliente já avaliou o cardápio
     */
    public function clienteJaAvaliou($cardapioId, $clienteId)
    {
        return $this->where('cardapio_id', $cardapioId)
                    ->where('cliente_id', $clienteId)
                    ->countAllResults() > 0;
    }

    /**
     * Buscar avaliação do cliente para um cardápio
     */
    public function getAvaliacaoCliente($cardapioId, $clienteId)
    {
        return $this->where('cardapio_id', $cardapioId)
                    ->where('cliente_id', $clienteId)
                    ->first();
    }

    /**
     * Buscar avaliações por empresa com informações do cliente
     */
    public function getAvaliacoesPorEmpresa($empresaId, $dataInicio = null, $dataFim = null)
    {
        $builder = $this->db->table($this->table);
        $builder->select('avaliacoes_cardapio.*,
                         usuarios.nome as cliente_nome,
                         cardapios.descricao as cardapio_descricao');
        $builder->join('usuarios', 'usuarios.id = avaliacoes_cardapio.cliente_id');
        $builder->join('cardapios', 'cardapios.id = avaliacoes_cardapio.cardapio_id');
        $builder->where('avaliacoes_cardapio.empresa_id', $empresaId);

        if ($dataInicio) {
            $builder->where('avaliacoes_cardapio.data >=', $dataInicio);
        }

        if ($dataFim) {
            $builder->where('avaliacoes_cardapio.data <=', $dataFim);
        }

        $builder->orderBy('avaliacoes_cardapio.data', 'DESC');

        return $builder->get()->getResult();
    }

    /**
     * Buscar estatísticas de avaliação por empresa (mês atual)
     */
    public function getEstatisticasPorEmpresa($empresaId = null, $mes = null, $ano = null, $tipoAvaliacao = null)
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
        $builder->join('empresas', 'empresas.id = avaliacoes_cardapio.empresa_id');
        $builder->where('MONTH(avaliacoes_cardapio.data)', $mes);
        $builder->where('YEAR(avaliacoes_cardapio.data)', $ano);

        if ($empresaId) {
            $builder->where('avaliacoes_cardapio.empresa_id', $empresaId);
        }

        if ($tipoAvaliacao) {
            $builder->where('avaliacoes_cardapio.tipo_avaliacao', $tipoAvaliacao);
        }

        $builder->groupBy('avaliacoes_cardapio.empresa_id');
        $builder->orderBy('empresas.nome_fantasia');

        return $builder->get()->getResult();
    }

    /**
     * Buscar histórico de avaliações de um cliente
     */
    public function getHistoricoCliente($clienteId, $limite = 50)
    {
        $builder = $this->db->table($this->table);
        $builder->select('avaliacoes_cardapio.*,
                         cardapios.descricao as cardapio_descricao,
                         empresas.nome_fantasia as empresa_nome');
        $builder->join('cardapios', 'cardapios.id = avaliacoes_cardapio.cardapio_id');
        $builder->join('empresas', 'empresas.id = avaliacoes_cardapio.empresa_id');
        $builder->where('avaliacoes_cardapio.cliente_id', $clienteId);
        $builder->orderBy('avaliacoes_cardapio.data', 'DESC');
        $builder->limit($limite);

        return $builder->get()->getResult();
    }

    /**
     * Buscar todas as avaliações com detalhes
     */
    public function getAvaliacoesDetalhadas($empresaId = null, $dataInicio = null, $dataFim = null)
    {
        $builder = $this->db->table($this->table);
        $builder->select('avaliacoes_cardapio.*,
                         usuarios.nome as cliente_nome,
                         empresas.nome_fantasia as empresa_nome,
                         cardapios.descricao as cardapio_descricao');
        $builder->join('usuarios', 'usuarios.id = avaliacoes_cardapio.cliente_id');
        $builder->join('empresas', 'empresas.id = avaliacoes_cardapio.empresa_id');
        $builder->join('cardapios', 'cardapios.id = avaliacoes_cardapio.cardapio_id');

        if ($empresaId) {
            $builder->where('avaliacoes_cardapio.empresa_id', $empresaId);
        }

        if ($dataInicio) {
            $builder->where('avaliacoes_cardapio.data >=', $dataInicio);
        }

        if ($dataFim) {
            $builder->where('avaliacoes_cardapio.data <=', $dataFim);
        }

        $builder->orderBy('avaliacoes_cardapio.data', 'DESC');
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
