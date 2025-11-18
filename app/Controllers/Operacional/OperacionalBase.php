<?php
namespace App\Controllers\Operacional;

use App\Controllers\BaseController;

class OperacionalBase extends BaseController
{
    public function __construct()
    {
        helper(['form','number','url']);
    }

    protected function verificarAdmin()
    {
        $session = session();
        $usuarioTipo = $session->get('usuario_tipo');

        if (!$session->get('logado') || $usuarioTipo !== 'admin') {
            return redirect()->to(base_url('dashboard'))->with('erro', 'Acesso negado. Apenas administradores podem acessar o módulo operacional.');
        }
        return null;
    }

    protected function renderView($view, $data = [])
    {
        // Criar objeto usuário a partir da sessão
        $session = session();
        $usuarioLogado = (object)[
            'id' => $session->get('usuario_id'),
            'nome' => $session->get('usuario_nome'),
            'email' => $session->get('usuario_email'),
            'tipo' => $session->get('usuario_tipo'),
            'empresa_id' => $session->get('empresa_id')
        ];

        $data['usuarioLogado'] = $usuarioLogado;
        return view($view, $data);
    }
}
