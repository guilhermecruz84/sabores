<?php

namespace App\Models;

use CodeIgniter\Model;

class RespostaModel extends Model
{
    protected $table            = 'respostas';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'chamado_id',
        'usuario_id',
        'mensagem',
        'interno'
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';

    // Validation
    protected $validationRules = [
        'chamado_id' => 'required|integer',
        'usuario_id' => 'required|integer',
        'mensagem'   => 'required|min_length[3]'
    ];

    /**
     * Buscar respostas de um chamado com informações do usuário
     */
    public function getRespostasPorChamado($chamadoId, $mostrarInternas = true)
    {
        $this->select('respostas.*,
                      usuarios.nome as usuario_nome,
                      usuarios.tipo as usuario_tipo,
                      usuarios.foto as usuario_foto');
        $this->join('usuarios', 'usuarios.id = respostas.usuario_id', 'left');
        $this->where('respostas.chamado_id', $chamadoId);

        if (!$mostrarInternas) {
            $this->where('respostas.interno', 0);
        }

        $this->orderBy('respostas.created_at', 'ASC');

        return $this->findAll();
    }

    /**
     * Contar respostas de um chamado
     */
    public function contarRespostasPorChamado($chamadoId)
    {
        return $this->where('chamado_id', $chamadoId)->countAllResults();
    }

    /**
     * Buscar última resposta de um chamado
     */
    public function getUltimaResposta($chamadoId)
    {
        $this->select('respostas.*,
                      usuarios.nome as usuario_nome,
                      usuarios.tipo as usuario_tipo');
        $this->join('usuarios', 'usuarios.id = respostas.usuario_id', 'left');
        $this->where('respostas.chamado_id', $chamadoId);
        $this->orderBy('respostas.created_at', 'DESC');
        $this->limit(1);

        return $this->first();
    }
}
