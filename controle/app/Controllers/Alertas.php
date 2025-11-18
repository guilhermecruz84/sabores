<?php

namespace App\Controllers;

use App\Models\ChecklistAlertaModel;

class Alertas extends BaseController
{
    protected $alertaModel;

    public function __construct()
    {
        $this->alertaModel = new ChecklistAlertaModel();
    }

    /**
     * Verificar login e permissão (somente Admin e Administrativo)
     */
    protected function verificarLogin()
    {
        $session = session();

        if (!$session->has('usuario_id')) {
            return redirect()->to('/login');
        }

        $tipo = $session->get('usuario_tipo');

        // Permitir apenas Admin e Administrativo (admin e atendente)
        if (!in_array($tipo, ['admin', 'atendente'])) {
            return redirect()->to('/dashboard')->with('erro', 'Acesso negado. Apenas Admin e Administrativo podem acessar os alertas.');
        }

        return null;
    }

    /**
     * Listar alertas pendentes do dia
     */
    public function index()
    {
        if ($check = $this->verificarLogin()) {
            return $check;
        }

        $data = [
            'titulo' => 'Alertas de Não Conformidades',
            'alertas' => $this->alertaModel->getAlertasPendentesHoje(),
            'totalPendentes' => $this->alertaModel->countAlertasPendentesHoje()
        ];

        return view('alertas/index', $data);
    }

    /**
     * Marcar alerta como concluído
     */
    public function concluir($id)
    {
        if ($check = $this->verificarLogin()) {
            return $check;
        }

        $session = session();
        $usuarioId = $session->get('usuario_id');

        $alerta = $this->alertaModel->find($id);

        if (!$alerta) {
            return redirect()->back()->with('erro', 'Alerta não encontrado.');
        }

        if ($this->alertaModel->marcarComoConcluido($id, $usuarioId)) {
            return redirect()->to('/alertas')->with('sucesso', 'Alerta marcado como concluído.');
        }

        return redirect()->back()->with('erro', 'Erro ao concluir alerta.');
    }

    /**
     * Visualizar todos os alertas (histórico)
     */
    public function historico()
    {
        if ($check = $this->verificarLogin()) {
            return $check;
        }

        $data = [
            'titulo' => 'Histórico de Alertas',
            'alertas' => $this->alertaModel->getAllWithRelations()
        ];

        return view('alertas/historico', $data);
    }

    /**
     * API: Contar alertas pendentes (para badge no menu)
     */
    public function contarPendentes()
    {
        if ($check = $this->verificarLogin()) {
            return $this->response->setJSON(['error' => 'Não autorizado']);
        }

        $count = $this->alertaModel->countAlertasPendentesHoje();

        return $this->response->setJSON(['count' => $count]);
    }
}
