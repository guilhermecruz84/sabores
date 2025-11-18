<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends Model
{
    protected $table            = 'usuarios';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'empresa_id',
        'nome',
        'email',
        'senha',
        'tipo',
        'foto',
        'telefone',
        'ativo',
        'ultimo_acesso'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'nome'  => 'required|min_length[3]|max_length[255]',
        'email' => 'required|valid_email|is_unique[usuarios.email,id,{id}]',
        'tipo'  => 'required|in_list[admin,atendente,cliente,operador,avaliador]'
    ];

    protected $validationMessages = [
        'email' => [
            'is_unique' => 'Este email já está cadastrado.'
        ]
    ];

    // Callbacks
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    protected function hashPassword(array $data)
    {
        if (isset($data['data']['senha'])) {
            $data['data']['senha'] = password_hash($data['data']['senha'], PASSWORD_DEFAULT);
        }
        return $data;
    }

    /**
     * Buscar usuário por email
     */
    public function findByEmail($email)
    {
        return $this->where('email', $email)->first();
    }

    /**
     * Verificar credenciais de login
     */
    public function verificarCredenciais($email, $senha)
    {
        $usuario = $this->findByEmail($email);

        if ($usuario && password_verify($senha, $usuario->senha) && $usuario->ativo) {
            // Atualizar último acesso
            $this->update($usuario->id, ['ultimo_acesso' => date('Y-m-d H:i:s')]);
            return $usuario;
        }

        return false;
    }

    /**
     * Buscar usuários com informações da empresa
     */
    public function getUsuariosComEmpresa($tipo = null)
    {
        $this->select('usuarios.*, empresas.nome_fantasia as empresa_nome');
        $this->join('empresas', 'empresas.id = usuarios.empresa_id', 'left');
        $this->where('usuarios.ativo', 1); // Apenas usuários ativos

        if ($tipo) {
            $this->where('usuarios.tipo', $tipo);
        }

        return $this->orderBy('usuarios.nome', 'ASC')->findAll();
    }

    /**
     * Buscar atendentes ativos
     */
    public function getAtendentesAtivos()
    {
        return $this->whereIn('tipo', ['admin', 'atendente'])
                    ->where('ativo', 1)
                    ->orderBy('nome', 'ASC')
                    ->findAll();
    }

    /**
     * Buscar clientes de uma empresa
     */
    public function getClientesPorEmpresa($empresaId)
    {
        return $this->where('empresa_id', $empresaId)
                    ->where('tipo', 'cliente')
                    ->where('ativo', 1)
                    ->orderBy('nome', 'ASC')
                    ->findAll();
    }

    /**
     * Contar usuários por tipo
     */
    public function contarPorTipo($tipo)
    {
        return $this->where('tipo', $tipo)->where('ativo', 1)->countAllResults();
    }
}
