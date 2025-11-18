<?php

namespace App\Controllers;

use App\Models\CardapioModel;
use App\Models\AvaliacaoCardapioModel;
use App\Models\AvaliacaoColaboradoraModel;

class Avaliador extends BaseController
{
    protected $cardapioModel;
    protected $avaliacaoCardapioModel;
    protected $avaliacaoColaboradoraModel;

    public function __construct()
    {
        $this->cardapioModel = new CardapioModel();
        $this->avaliacaoCardapioModel = new AvaliacaoCardapioModel();
        $this->avaliacaoColaboradoraModel = new AvaliacaoColaboradoraModel();
    }

    /**
     * Página inicial do avaliador (Tablet)
     * Redireciona automaticamente para o fluxo de avaliação
     */
    public function index()
    {
        if ($check = $this->verificarLogin()) {
            return $check;
        }

        // Apenas avaliadores podem acessar
        if ($this->usuarioLogado->tipo !== 'avaliador') {
            return redirect()->to('/dashboard')->with('erro', 'Acesso negado.');
        }

        // Sempre redireciona para avaliar cardápio
        // Isso permite múltiplas avaliações no mesmo dia
        return redirect()->to('/avaliador/avaliar-cardapio');
    }

    /**
     * Página para avaliar o cardápio do dia (funcionários)
     */
    public function avaliarCardapio()
    {
        if ($check = $this->verificarLogin()) {
            return $check;
        }

        if ($this->usuarioLogado->tipo !== 'avaliador') {
            return redirect()->to('/dashboard')->with('erro', 'Acesso negado.');
        }

        // Sempre mostra formulário limpo para nova avaliação
        return $this->renderView('avaliador/avaliar_cardapio', [
            'titulo' => 'Avaliar Cardápio',
            'avaliacao' => null
        ]);
    }

    /**
     * Salvar avaliação do cardápio (funcionários)
     */
    public function salvarAvaliacaoCardapio()
    {
        if ($check = $this->verificarLogin()) {
            return $check;
        }

        if ($this->usuarioLogado->tipo !== 'avaliador') {
            return redirect()->to('/dashboard')->with('erro', 'Acesso negado.');
        }

        $empresaId = $this->usuarioLogado->empresa_id;
        $hoje = date('Y-m-d');

        $avaliacao = $this->request->getPost('avaliacao');
        $motivo = $this->request->getPost('motivo');

        // Validar
        if (!in_array($avaliacao, ['otimo', 'bom', 'regular', 'ruim'])) {
            return redirect()->back()->with('erro', 'Avaliação inválida.');
        }

        $data = [
            'cardapio_id' => null,
            'cliente_id' => $this->usuarioLogado->id,
            'empresa_id' => $empresaId,
            'tipo_avaliacao' => 'funcionarios',
            'data' => $hoje,
            'avaliacao' => $avaliacao,
            'motivo' => $motivo
        ];

        // Sempre criar nova avaliação (permite múltiplas avaliações por dia)
        $this->avaliacaoCardapioModel->insert($data);

        // Redirecionar para avaliação da colaboradora (próximo passo)
        return redirect()->to('/avaliador/avaliar-colaboradora');
    }

    /**
     * Página para avaliar colaboradora
     */
    public function avaliarColaboradora()
    {
        if ($check = $this->verificarLogin()) {
            return $check;
        }

        if ($this->usuarioLogado->tipo !== 'avaliador') {
            return redirect()->to('/dashboard')->with('erro', 'Acesso negado.');
        }

        // Sempre mostra formulário limpo para nova avaliação
        return $this->renderView('avaliador/avaliar_colaboradora', [
            'titulo' => 'Avaliar Colaboradora',
            'avaliacao' => null
        ]);
    }

    /**
     * Salvar avaliação da colaboradora
     */
    public function salvarAvaliacaoColaboradora()
    {
        if ($check = $this->verificarLogin()) {
            return $check;
        }

        if ($this->usuarioLogado->tipo !== 'avaliador') {
            return redirect()->to('/dashboard')->with('erro', 'Acesso negado.');
        }

        $empresaId = $this->usuarioLogado->empresa_id;
        $hoje = date('Y-m-d');

        $avaliacao = $this->request->getPost('avaliacao');
        $motivo = $this->request->getPost('motivo');

        // Validar
        if (!in_array($avaliacao, ['otimo', 'bom', 'regular', 'ruim'])) {
            return redirect()->back()->with('erro', 'Avaliação inválida.');
        }

        $data = [
            'empresa_id' => $empresaId,
            'data' => $hoje,
            'avaliacao' => $avaliacao,
            'motivo' => $motivo
        ];

        // Sempre criar nova avaliação (permite múltiplas avaliações por dia)
        $this->avaliacaoColaboradoraModel->insert($data);

        // Redirecionar para tela de obrigado
        return redirect()->to('/avaliador/obrigado');
    }

    /**
     * Tela de obrigado após conclusão das avaliações
     */
    public function obrigado()
    {
        if ($check = $this->verificarLogin()) {
            return $check;
        }

        if ($this->usuarioLogado->tipo !== 'avaliador') {
            return redirect()->to('/dashboard')->with('erro', 'Acesso negado.');
        }

        return $this->renderView('avaliador/obrigado', [
            'titulo' => 'Avaliações Concluídas'
        ]);
    }
}
