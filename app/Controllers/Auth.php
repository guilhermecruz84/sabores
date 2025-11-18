<?php

namespace App\Controllers;

use App\Models\UsuarioModel;
use App\Models\EmpresaModel;

class Auth extends BaseController
{
    protected $usuarioModel;
    protected $empresaModel;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
        $this->empresaModel = new EmpresaModel();
    }

    /**
     * Página de login
     */
    public function login()
    {
        // Se já estiver logado, redirecionar para dashboard
        if ($this->session->has('usuario_id')) {
            return redirect()->to('/dashboard');
        }

        return $this->renderView('auth/login', [
            'titulo' => 'Login - Sistema de Chamados'
        ]);
    }

    /**
     * Processar autenticação
     */
    public function authenticate()
    {
        $email = $this->request->getPost('email');
        $senha = $this->request->getPost('senha');
        $lembrar = $this->request->getPost('lembrar');

        // Validar campos
        if (empty($email) || empty($senha)) {
            return redirect()->to('/login')->with('erro', 'Por favor, preencha todos os campos.')->withInput();
        }

        // Verificar credenciais
        $usuario = $this->usuarioModel->verificarCredenciais($email, $senha);

        if (!$usuario) {
            return redirect()->to('/login')->with('erro', 'Usuário ou senha incorretos. Por favor, tente novamente.')->withInput();
        }

        // Verificar se o usuário está ativo
        if (isset($usuario->ativo) && $usuario->ativo != 1) {
            return redirect()->to('/login')->with('erro', 'Sua conta está inativa. Entre em contato com o administrador.')->withInput();
        }

        // Criar sessão
        $this->session->set([
            'usuario_id' => $usuario->id,
            'usuario_nome' => $usuario->nome,
            'usuario_email' => $usuario->email,
            'usuario_tipo' => $usuario->tipo,
            'empresa_id' => $usuario->empresa_id,
            'logado' => true
        ]);

        // Se marcou "lembrar-me", definir cookie por 30 dias
        if ($lembrar) {
            set_cookie('remember_token', base64_encode($usuario->email), 2592000); // 30 dias
        }

        // Redirecionar baseado no tipo de usuário
        if ($usuario->tipo === 'avaliador') {
            return redirect()->to('/avaliador')->with('sucesso', 'Bem-vindo(a), ' . $usuario->nome . '!');
        }

        // Demais usuários vão para o dashboard
        return redirect()->to('/dashboard')->with('sucesso', 'Bem-vindo(a), ' . $usuario->nome . '!');
    }

    /**
     * Página de registro (apenas para clientes)
     */
    public function register()
    {
        // Se já estiver logado, redirecionar para dashboard
        if ($this->session->has('usuario_id')) {
            return redirect()->to('/dashboard');
        }

        $empresas = $this->empresaModel->getEmpresasAtivas();

        return $this->renderView('auth/register', [
            'titulo' => 'Registro - Sistema de Chamados',
            'empresas' => $empresas
        ]);
    }

    /**
     * Processar registro
     */
    public function processRegister()
    {
        $regras = [
            'nome' => 'required|min_length[3]|max_length[255]',
            'email' => 'required|valid_email|is_unique[usuarios.email]',
            'senha' => 'required|min_length[6]',
            'confirmar_senha' => 'required|matches[senha]',
            'empresa_id' => 'required|integer',
            'telefone' => 'permit_empty|min_length[10]|max_length[20]'
        ];

        if (!$this->validate($regras)) {
            return redirect()->back()->withInput()->with('erros', $this->validator->getErrors());
        }

        // Criar usuário
        $data = [
            'nome' => $this->request->getPost('nome'),
            'email' => $this->request->getPost('email'),
            'senha' => $this->request->getPost('senha'),
            'telefone' => $this->request->getPost('telefone'),
            'empresa_id' => $this->request->getPost('empresa_id'),
            'tipo' => 'cliente',
            'ativo' => 1
        ];

        if ($this->usuarioModel->insert($data)) {
            return redirect()->to('/login')->with('sucesso', 'Cadastro realizado com sucesso! Faça login para continuar.');
        } else {
            return redirect()->back()->withInput()->with('erro', 'Erro ao criar usuário. Tente novamente.');
        }
    }

    /**
     * Logout
     */
    public function logout()
    {
        // Destruir sessão
        $this->session->destroy();

        // Remover cookie
        helper('cookie');
        delete_cookie('remember_token');

        return redirect()->to('/login')->with('sucesso', 'Logout realizado com sucesso!');
    }
}
