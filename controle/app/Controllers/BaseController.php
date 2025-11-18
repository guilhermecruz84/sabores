<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var array
     */
    protected $helpers = ['form', 'url', 'session'];

    /**
     * Session instance
     *
     * @var \CodeIgniter\Session\Session
     */
    protected $session;

    /**
     * Current user data
     *
     * @var object|null
     */
    protected $usuarioLogado = null;

    /**
     * Constructor.
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.
        $this->session = \Config\Services::session();

        // Carregar dados do usuário logado
        if ($this->session->has('usuario_id')) {
            $usuarioModel = new \App\Models\UsuarioModel();
            $this->usuarioLogado = $usuarioModel->find($this->session->get('usuario_id'));
        }

        // Compartilhar dados com todas as views
        $this->viewData = [
            'usuarioLogado' => $this->usuarioLogado,
            'titulo' => 'Sistema de Chamados'
        ];
    }

    /**
     * Verificar se o usuário está logado
     */
    protected function verificarLogin()
    {
        if (!$this->session->has('usuario_id')) {
            return redirect()->to('/login')->with('erro', 'Você precisa estar logado para acessar esta página.');
        }
    }

    /**
     * Verificar se o usuário é admin
     */
    protected function verificarAdmin()
    {
        if (!$this->usuarioLogado || $this->usuarioLogado->tipo !== 'admin') {
            return redirect()->to('/dashboard')->with('erro', 'Você não tem permissão para acessar esta página.');
        }
    }

    /**
     * Verificar se o usuário é staff (admin ou atendente)
     */
    protected function verificarStaff()
    {
        if (!$this->usuarioLogado || !in_array($this->usuarioLogado->tipo, ['admin', 'atendente'])) {
            return redirect()->to('/dashboard')->with('erro', 'Você não tem permissão para acessar esta página.');
        }
    }

    /**
     * Verificar se o usuário é operador
     */
    protected function verificarOperador()
    {
        if (!$this->usuarioLogado || $this->usuarioLogado->tipo !== 'operador') {
            return redirect()->to('/dashboard')->with('erro', 'Você não tem permissão para acessar esta página.');
        }
    }

    /**
     * Verificar se o usuário NÃO é operador
     * (usado para bloquear operadores de acessar Dashboard e Chamados)
     */
    protected function verificarNaoOperador()
    {
        if ($this->usuarioLogado && $this->usuarioLogado->tipo === 'operador') {
            return redirect()->to('/checklists')->with('erro', 'Operadores não têm acesso a esta área.');
        }
    }

    /**
     * Renderizar view com layout
     */
    protected function renderView($view, $data = [])
    {
        $data = array_merge($this->viewData, $data);
        return view($view, $data);
    }

    /**
     * Retornar JSON
     */
    protected function respondWithJSON($data, $statusCode = 200)
    {
        return $this->response
            ->setStatusCode($statusCode)
            ->setJSON($data);
    }

    /**
     * Retornar erro JSON
     */
    protected function respondWithError($message, $statusCode = 400)
    {
        return $this->respondWithJSON([
            'success' => false,
            'message' => $message
        ], $statusCode);
    }

    /**
     * Retornar sucesso JSON
     */
    protected function respondWithSuccess($message, $data = [])
    {
        return $this->respondWithJSON(array_merge([
            'success' => true,
            'message' => $message
        ], $data));
    }
}
