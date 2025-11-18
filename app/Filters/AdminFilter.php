<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AdminFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        if (!$session->has('usuario_id')) {
            return redirect()->to('/login')->with('erro', 'Você precisa estar logado.');
        }

        if ($session->get('usuario_tipo') !== 'admin') {
            return redirect()->to('/dashboard')->with('erro', 'Você não tem permissão para acessar esta página.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }
}
