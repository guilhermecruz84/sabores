<?php

namespace App\Controllers;

use App\Models\UsuarioModel;
use App\Models\EmpresaModel;

class Usuarios extends BaseController
{
    protected $usuarioModel;
    protected $empresaModel;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
        $this->empresaModel = new EmpresaModel();
    }

    /**
     * Listar usuários
     */
    public function index()
    {
        if ($check = $this->verificarLogin()) {
            return $check;
        }

        if ($check = $this->verificarStaff()) {
            return $check;
        }

        $usuarios = $this->usuarioModel->getUsuariosComEmpresa();

        return $this->renderView('usuarios/index', [
            'titulo' => 'Usuários - Sistema de Chamados',
            'usuarios' => $usuarios
        ]);
    }

    /**
     * Formulário novo usuário
     */
    public function novo()
    {
        if ($check = $this->verificarLogin()) {
            return $check;
        }

        if ($check = $this->verificarStaff()) {
            return $check;
        }

        $empresas = $this->empresaModel->getEmpresasAtivas();

        return $this->renderView('usuarios/form', [
            'titulo' => 'Novo Usuário - Sistema de Chamados',
            'usuario' => null,
            'empresas' => $empresas
        ]);
    }

    /**
     * Criar usuário
     */
    public function criar()
    {
        if ($check = $this->verificarLogin()) {
            return $check;
        }

        if ($check = $this->verificarStaff()) {
            return $check;
        }

        $regras = [
            'nome' => 'required|min_length[3]|max_length[255]',
            'email' => 'required|valid_email|is_unique[usuarios.email]',
            'senha' => 'required|min_length[6]',
            'tipo' => 'required|in_list[admin,atendente,cliente,operador,avaliador]'
        ];

        if (!$this->validate($regras)) {
            return redirect()->back()->withInput()->with('erros', $this->validator->getErrors());
        }

        $empresaId = $this->request->getPost('empresa_id');

        $data = [
            'nome' => $this->request->getPost('nome'),
            'email' => $this->request->getPost('email'),
            'senha' => $this->request->getPost('senha'),
            'tipo' => $this->request->getPost('tipo'),
            'empresa_id' => !empty($empresaId) ? $empresaId : null,
            'telefone' => $this->request->getPost('telefone'),
            'ativo' => 1
        ];

        // Desabilita validação do model pois já validamos acima
        $this->usuarioModel->skipValidation(true);

        if ($this->usuarioModel->insert($data)) {
            return redirect()->to('/usuarios')->with('sucesso', 'Usuário criado com sucesso!');
        } else {
            return redirect()->back()->withInput()->with('erro', 'Erro ao criar usuário.');
        }
    }

    /**
     * Ver perfil
     */
    public function perfil()
    {
        if ($check = $this->verificarLogin()) {
            return $check;
        }

        return $this->renderView('usuarios/perfil', [
            'titulo' => 'Meu Perfil - Sistema de Chamados'
        ]);
    }

    /**
     * Atualizar perfil
     */
    public function atualizarPerfil()
    {
        if ($check = $this->verificarLogin()) {
            return $check;
        }

        $data = [
            'nome' => $this->request->getPost('nome'),
            'telefone' => $this->request->getPost('telefone')
        ];

        $senhaAtual = $this->request->getPost('senha_atual');
        $novaSenha = $this->request->getPost('nova_senha');

        // Se quiser mudar a senha
        if (!empty($senhaAtual) && !empty($novaSenha)) {
            $usuario = $this->usuarioModel->find($this->usuarioLogado->id);

            if (!password_verify($senhaAtual, $usuario->senha)) {
                return redirect()->back()->with('erro', 'Senha atual incorreta.');
            }

            $data['senha'] = $novaSenha;
        }

        if ($this->usuarioModel->update($this->usuarioLogado->id, $data)) {
            return redirect()->back()->with('sucesso', 'Perfil atualizado com sucesso!');
        } else {
            return redirect()->back()->with('erro', 'Erro ao atualizar perfil.');
        }
    }

    /**
     * Formulário editar usuário
     */
    public function editar($id)
    {
        if ($check = $this->verificarLogin()) {
            return $check;
        }

        if ($check = $this->verificarStaff()) {
            return $check;
        }

        $usuario = $this->usuarioModel->find($id);

        if (!$usuario) {
            return redirect()->to('/usuarios')->with('erro', 'Usuário não encontrado.');
        }

        $empresas = $this->empresaModel->getEmpresasAtivas();

        return $this->renderView('usuarios/form', [
            'titulo' => 'Editar Usuário - Sistema de Chamados',
            'usuario' => $usuario,
            'empresas' => $empresas
        ]);
    }

    /**
     * Atualizar usuário
     */
    public function atualizar($id)
    {
        if ($check = $this->verificarLogin()) {
            return $check;
        }

        if ($check = $this->verificarStaff()) {
            return $check;
        }

        $usuario = $this->usuarioModel->find($id);

        if (!$usuario) {
            return redirect()->to('/usuarios')->with('erro', 'Usuário não encontrado.');
        }

        $regras = [
            'nome' => 'required|min_length[3]|max_length[255]',
            'email' => "required|valid_email|is_unique[usuarios.email,id,{$id}]",
            'tipo' => 'required|in_list[admin,atendente,cliente,operador,avaliador]'
        ];

        // Se senha foi preenchida, valida
        if ($this->request->getPost('senha')) {
            $regras['senha'] = 'min_length[6]';
        }

        if (!$this->validate($regras)) {
            return redirect()->back()->withInput()->with('erros', $this->validator->getErrors());
        }

        $empresaId = $this->request->getPost('empresa_id');

        $data = [
            'nome' => $this->request->getPost('nome'),
            'email' => $this->request->getPost('email'),
            'tipo' => $this->request->getPost('tipo'),
            'empresa_id' => !empty($empresaId) ? $empresaId : null,
            'telefone' => $this->request->getPost('telefone'),
            'ativo' => $this->request->getPost('ativo') ? 1 : 0
        ];

        // Só atualiza senha se foi preenchida
        if ($this->request->getPost('senha')) {
            $data['senha'] = $this->request->getPost('senha');
        }

        // Desabilita validação do model pois já validamos acima
        $this->usuarioModel->skipValidation(true);

        if ($this->usuarioModel->update($id, $data)) {
            return redirect()->to('/usuarios')->with('sucesso', 'Usuário atualizado com sucesso!');
        } else {
            return redirect()->back()->withInput()->with('erro', 'Erro ao atualizar usuário.');
        }
    }
}
