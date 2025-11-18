<?php

namespace App\Models;

use CodeIgniter\Model;

class ChecklistConfiguracaoModel extends Model
{
    protected $table            = 'checklists_configuracao';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'empresa_id',
        'tipo',
        'dias_semana',
        'ativo'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Verificar se checklist está disponível para a empresa no dia
     */
    public function checklistDisponivelHoje($empresaId, $tipo)
    {
        $config = $this->where('empresa_id', $empresaId)
                      ->where('tipo', $tipo)
                      ->where('ativo', 1)
                      ->first();

        if (!$config) {
            // Se não há configuração, permite qualquer dia
            return true;
        }

        // Pegar dia da semana atual (1 = Segunda, 7 = Domingo)
        $diaAtual = date('N');

        // Verificar se o dia atual está na lista de dias permitidos
        $diasPermitidos = explode(',', $config->dias_semana);

        return in_array($diaAtual, $diasPermitidos);
    }

    /**
     * Buscar configuração por empresa
     */
    public function getConfiguracaoPorEmpresa($empresaId)
    {
        return $this->where('empresa_id', $empresaId)->findAll();
    }

    /**
     * Buscar ou criar configuração
     */
    public function getOuCriarConfiguracao($empresaId, $tipo)
    {
        $config = $this->where('empresa_id', $empresaId)
                      ->where('tipo', $tipo)
                      ->first();

        if (!$config) {
            // Criar configuração padrão (Segunda a Sexta)
            $id = $this->insert([
                'empresa_id' => $empresaId,
                'tipo' => $tipo,
                'dias_semana' => '1,2,3,4,5', // Segunda a Sexta
                'ativo' => 1
            ]);

            return $this->find($id);
        }

        return $config;
    }

    /**
     * Atualizar dias da semana
     */
    public function atualizarDias($empresaId, $tipo, $dias)
    {
        $config = $this->where('empresa_id', $empresaId)
                      ->where('tipo', $tipo)
                      ->first();

        $diasString = is_array($dias) ? implode(',', $dias) : $dias;

        if ($config) {
            return $this->update($config->id, ['dias_semana' => $diasString]);
        } else {
            return $this->insert([
                'empresa_id' => $empresaId,
                'tipo' => $tipo,
                'dias_semana' => $diasString,
                'ativo' => 1
            ]);
        }
    }

    /**
     * Pegar nomes dos dias da semana
     */
    public static function getNomesDiasSemana()
    {
        return [
            '1' => 'Segunda-feira',
            '2' => 'Terça-feira',
            '3' => 'Quarta-feira',
            '4' => 'Quinta-feira',
            '5' => 'Sexta-feira',
            '6' => 'Sábado',
            '7' => 'Domingo'
        ];
    }
}
