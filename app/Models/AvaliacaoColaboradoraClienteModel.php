<?php

namespace App\Models;

use CodeIgniter\Model;

class AvaliacaoColaboradoraClienteModel extends Model
{
    protected $table = 'avaliacao_colaboradora_cliente';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'empresa_id',
        'cliente_id',
        'data',
        'assiduidade_pontualidade',
        'apresentacao_pessoal',
        'atendimento_relacionamento',
        'agilidade_produtividade',
        'qualidade_execucao',
        'cumprimento_regras',
        'proatividade',
        'organizacao_limpeza',
        'percepcao_geral',
        'observacoes'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'empresa_id' => 'required|integer',
        'cliente_id' => 'required|integer',
        'data' => 'required|valid_date',
        'assiduidade_pontualidade' => 'required|integer|greater_than[0]|less_than[6]',
        'apresentacao_pessoal' => 'required|integer|greater_than[0]|less_than[6]',
        'atendimento_relacionamento' => 'required|integer|greater_than[0]|less_than[6]',
        'agilidade_produtividade' => 'required|integer|greater_than[0]|less_than[6]',
        'qualidade_execucao' => 'required|integer|greater_than[0]|less_than[6]',
        'cumprimento_regras' => 'required|integer|greater_than[0]|less_than[6]',
        'proatividade' => 'required|integer|greater_than[0]|less_than[6]',
        'organizacao_limpeza' => 'required|integer|greater_than[0]|less_than[6]',
        'percepcao_geral' => 'required|integer|greater_than[0]|less_than[6]',
    ];

    protected $validationMessages = [
        'assiduidade_pontualidade' => [
            'greater_than' => 'A nota de Assiduidade deve ser de 1 a 5',
            'less_than' => 'A nota de Assiduidade deve ser de 1 a 5'
        ],
        'apresentacao_pessoal' => [
            'greater_than' => 'A nota de Apresentação Pessoal deve ser de 1 a 5',
            'less_than' => 'A nota de Apresentação Pessoal deve ser de 1 a 5'
        ]
    ];

    /**
     * Buscar avaliações por empresa
     */
    public function getAvaliacoesPorEmpresa($empresaId, $dataInicio = null, $dataFim = null)
    {
        $builder = $this->select('avaliacao_colaboradora_cliente.*, usuarios.nome as cliente_nome')
                        ->join('usuarios', 'usuarios.id = avaliacao_colaboradora_cliente.cliente_id')
                        ->where('avaliacao_colaboradora_cliente.empresa_id', $empresaId)
                        ->orderBy('avaliacao_colaboradora_cliente.data', 'DESC');

        if ($dataInicio) {
            $builder->where('avaliacao_colaboradora_cliente.data >=', $dataInicio);
        }

        if ($dataFim) {
            $builder->where('avaliacao_colaboradora_cliente.data <=', $dataFim);
        }

        return $builder->findAll();
    }

    /**
     * Buscar avaliações por cliente
     */
    public function getAvaliacoesPorCliente($clienteId, $dataInicio = null, $dataFim = null)
    {
        $builder = $this->select('avaliacao_colaboradora_cliente.*')
                        ->where('cliente_id', $clienteId)
                        ->orderBy('data', 'DESC');

        if ($dataInicio) {
            $builder->where('data >=', $dataInicio);
        }

        if ($dataFim) {
            $builder->where('data <=', $dataFim);
        }

        return $builder->findAll();
    }

    /**
     * Verificar se já existe avaliação do cliente no mês
     */
    public function jaAvaliouNoMes($clienteId, $ano = null, $mes = null)
    {
        if (!$ano) {
            $ano = date('Y');
        }
        if (!$mes) {
            $mes = date('m');
        }

        // Primeiro e último dia do mês
        $primeiroDia = "$ano-$mes-01";
        $ultimoDia = date('Y-m-t', strtotime($primeiroDia));

        return $this->where('cliente_id', $clienteId)
                    ->where('data >=', $primeiroDia)
                    ->where('data <=', $ultimoDia)
                    ->countAllResults() > 0;
    }

    /**
     * Obter avaliação do mês do cliente
     */
    public function getAvaliacaoDoMes($clienteId, $ano = null, $mes = null)
    {
        if (!$ano) {
            $ano = date('Y');
        }
        if (!$mes) {
            $mes = date('m');
        }

        // Primeiro e último dia do mês
        $primeiroDia = "$ano-$mes-01";
        $ultimoDia = date('Y-m-t', strtotime($primeiroDia));

        return $this->where('cliente_id', $clienteId)
                    ->where('data >=', $primeiroDia)
                    ->where('data <=', $ultimoDia)
                    ->first();
    }

    /**
     * Obter média geral das avaliações por período
     */
    public function getMediaGeralPorPeriodo($empresaId, $dataInicio, $dataFim)
    {
        $result = $this->selectAvg('media_geral', 'media')
                       ->where('empresa_id', $empresaId)
                       ->where('data >=', $dataInicio)
                       ->where('data <=', $dataFim)
                       ->first();

        return $result ? round($result['media'], 2) : 0;
    }

    /**
     * Obter estatísticas por critério
     */
    public function getEstatisticasPorCriterio($empresaId, $dataInicio = null, $dataFim = null)
    {
        $builder = $this->select('
            AVG(assiduidade_pontualidade) as media_assiduidade,
            AVG(apresentacao_pessoal) as media_apresentacao,
            AVG(atendimento_relacionamento) as media_atendimento,
            AVG(agilidade_produtividade) as media_agilidade,
            AVG(qualidade_execucao) as media_qualidade,
            AVG(cumprimento_regras) as media_cumprimento,
            AVG(proatividade) as media_proatividade,
            AVG(organizacao_limpeza) as media_organizacao,
            AVG(percepcao_geral) as media_percepcao,
            COUNT(*) as total_avaliacoes
        ')->where('empresa_id', $empresaId);

        if ($dataInicio) {
            $builder->where('data >=', $dataInicio);
        }

        if ($dataFim) {
            $builder->where('data <=', $dataFim);
        }

        return $builder->first();
    }
}
