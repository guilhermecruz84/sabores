<?php

namespace App\Controllers;

use App\Models\EmpresaModel;

class Empresas extends BaseController
{
    protected $empresaModel;

    public function __construct()
    {
        $this->empresaModel = new EmpresaModel();
    }

    /**
     * Listar empresas
     */
    public function index()
    {
        if ($check = $this->verificarLogin()) {
            return $check;
        }

        if ($check = $this->verificarAdmin()) {
            return $check;
        }

        $empresas = $this->empresaModel->getEmpresasComEstatisticas();

        return $this->renderView('empresas/index', [
            'titulo' => 'Empresas - Sistema de Chamados',
            'empresas' => $empresas
        ]);
    }

    /**
     * Formulário nova empresa
     */
    public function nova()
    {
        if ($check = $this->verificarLogin()) {
            return $check;
        }

        if ($check = $this->verificarAdmin()) {
            return $check;
        }

        return $this->renderView('empresas/form', [
            'titulo' => 'Nova Empresa - Sistema de Chamados',
            'empresa' => null
        ]);
    }

    /**
     * Criar empresa
     */
    public function criar()
    {
        if ($check = $this->verificarLogin()) {
            return $check;
        }

        if ($check = $this->verificarAdmin()) {
            return $check;
        }

        $regras = [
            'razao_social' => 'required|min_length[3]|max_length[255]',
            'nome_fantasia' => 'required|min_length[3]|max_length[255]',
            'email' => 'permit_empty|valid_email',
            'cnpj' => 'permit_empty|is_unique[empresas.cnpj]'
        ];

        if (!$this->validate($regras)) {
            return redirect()->back()->withInput()->with('erros', $this->validator->getErrors());
        }

        $data = [
            'razao_social' => $this->request->getPost('razao_social'),
            'nome_fantasia' => $this->request->getPost('nome_fantasia'),
            'cnpj' => $this->request->getPost('cnpj'),
            'telefone' => $this->request->getPost('telefone'),
            'email' => $this->request->getPost('email'),
            'endereco' => $this->request->getPost('endereco'),
            'ativo' => 1
        ];

        if ($this->empresaModel->insert($data)) {
            return redirect()->to('/empresas')->with('sucesso', 'Empresa criada com sucesso!');
        } else {
            return redirect()->back()->withInput()->with('erro', 'Erro ao criar empresa.');
        }
    }

    /**
     * Editar empresa
     */
    public function editar($id)
    {
        if ($check = $this->verificarLogin()) {
            return $check;
        }

        if ($check = $this->verificarAdmin()) {
            return $check;
        }

        $empresa = $this->empresaModel->find($id);

        if (!$empresa) {
            return redirect()->to('/empresas')->with('erro', 'Empresa não encontrada.');
        }

        return $this->renderView('empresas/form', [
            'titulo' => 'Editar Empresa - Sistema de Chamados',
            'empresa' => $empresa
        ]);
    }

    /**
     * Atualizar empresa
     */
    public function atualizar($id)
    {
        if ($check = $this->verificarLogin()) {
            return $check;
        }

        if ($check = $this->verificarAdmin()) {
            return $check;
        }

        $data = [
            'razao_social' => $this->request->getPost('razao_social'),
            'nome_fantasia' => $this->request->getPost('nome_fantasia'),
            'cnpj' => $this->request->getPost('cnpj'),
            'telefone' => $this->request->getPost('telefone'),
            'email' => $this->request->getPost('email'),
            'endereco' => $this->request->getPost('endereco'),
            'ativo' => $this->request->getPost('ativo') ? 1 : 0
        ];

        if ($this->empresaModel->update($id, $data)) {
            return redirect()->to('/empresas')->with('sucesso', 'Empresa atualizada com sucesso!');
        } else {
            return redirect()->back()->withInput()->with('erro', 'Erro ao atualizar empresa.');
        }
    }

    /**
     * Deletar empresa
     */
    public function deletar($id)
    {
        if ($check = $this->verificarLogin()) {
            return $check;
        }

        if ($check = $this->verificarAdmin()) {
            return $check;
        }

        if ($this->empresaModel->delete($id)) {
            return redirect()->to('/empresas')->with('sucesso', 'Empresa deletada com sucesso!');
        } else {
            return redirect()->back()->with('erro', 'Erro ao deletar empresa.');
        }
    }
}
