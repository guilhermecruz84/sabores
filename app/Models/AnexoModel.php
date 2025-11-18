<?php

namespace App\Models;

use CodeIgniter\Model;

class AnexoModel extends Model
{
    protected $table            = 'anexos';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'chamado_id',
        'resposta_id',
        'usuario_id',
        'nome_original',
        'nome_arquivo',
        'tipo',
        'tamanho'
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';

    // Validation
    protected $validationRules = [
        'usuario_id'    => 'required|integer',
        'nome_original' => 'required',
        'nome_arquivo'  => 'required'
    ];

    /**
     * Buscar anexos de um chamado
     */
    public function getAnexosPorChamado($chamadoId)
    {
        $this->select('anexos.*,
                      usuarios.nome as usuario_nome');
        $this->join('usuarios', 'usuarios.id = anexos.usuario_id', 'left');
        $this->where('anexos.chamado_id', $chamadoId);
        $this->orderBy('anexos.created_at', 'ASC');

        return $this->findAll();
    }

    /**
     * Buscar anexos de uma resposta
     */
    public function getAnexosPorResposta($respostaId)
    {
        $this->select('anexos.*,
                      usuarios.nome as usuario_nome');
        $this->join('usuarios', 'usuarios.id = anexos.usuario_id', 'left');
        $this->where('anexos.resposta_id', $respostaId);
        $this->orderBy('anexos.created_at', 'ASC');

        return $this->findAll();
    }

    /**
     * Formatar tamanho de arquivo
     */
    public static function formatarTamanho($bytes)
    {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            $bytes = $bytes . ' bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes . ' byte';
        } else {
            $bytes = '0 bytes';
        }

        return $bytes;
    }

    /**
     * Verificar se é imagem
     */
    public static function isImagem($tipo)
    {
        $imagemTipos = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        return in_array($tipo, $imagemTipos);
    }
}
