<?php

namespace App\Models;

use CodeIgniter\Model;

class ChecklistRespostaModel extends Model
{
    protected $table            = 'checklists_respostas';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'registro_id',
        'item_id',
        'resposta',
        'conforme',
        'foto_path'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'registro_id' => 'required|integer',
        'item_id'     => 'required|integer'
    ];

    /**
     * Buscar respostas de um registro com dados dos itens
     */
    public function getRespostasPorRegistro($registroId)
    {
        return $this->select('checklists_respostas.*,
                             checklists_itens.descricao,
                             checklists_itens.tipo_resposta,
                             checklists_itens.obrigatorio,
                             checklists_itens.requer_foto')
                    ->join('checklists_itens', 'checklists_itens.id = checklists_respostas.item_id')
                    ->where('checklists_respostas.registro_id', $registroId)
                    ->orderBy('checklists_itens.ordem', 'ASC')
                    ->findAll();
    }

    /**
     * Salvar múltiplas respostas
     */
    public function salvarRespostas($registroId, $respostas)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        foreach ($respostas as $itemId => $dados) {
            // Verificar se já existe resposta
            $respostaExistente = $this->where('registro_id', $registroId)
                                      ->where('item_id', $itemId)
                                      ->first();

            $data = [
                'registro_id' => $registroId,
                'item_id'     => $itemId,
                'resposta'    => $dados['resposta'] ?? null,
                'conforme'    => isset($dados['conforme']) ? (int)$dados['conforme'] : null,
                'foto_path'   => $dados['foto_path'] ?? null
            ];

            if ($respostaExistente) {
                $this->update($respostaExistente->id, $data);
            } else {
                $this->insert($data);
            }
        }

        $db->transComplete();
        return $db->transStatus();
    }

    /**
     * Verificar se todas as respostas obrigatórias foram preenchidas
     */
    public function verificarRespostasCompletas($registroId)
    {
        $db = \Config\Database::connect();

        // Buscar itens obrigatórios do registro
        $registro = $db->table('checklists_registros')->where('id', $registroId)->get()->getRow();

        if (!$registro) {
            return false;
        }

        $itensObrigatorios = $db->table('checklists_itens')
                               ->where('tipo', $registro->tipo)
                               ->where('obrigatorio', 1)
                               ->where('ativo', 1)
                               ->countAllResults();

        $respostasPreenchidas = $this->where('registro_id', $registroId)
                                    ->where('resposta IS NOT NULL')
                                    ->countAllResults();

        return $respostasPreenchidas >= $itensObrigatorios;
    }

    /**
     * Contar não conformidades
     */
    public function contarNaoConformidades($registroId)
    {
        return $this->where('registro_id', $registroId)
                    ->where('conforme', 0)
                    ->countAllResults();
    }
}
