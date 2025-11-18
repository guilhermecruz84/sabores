<?php
namespace App\Controllers\Operacional;

use App\Controllers\Operacional\OperacionalBase;

class Despesas extends OperacionalBase
{
    // Tipos fixos (poderia vir de Config ou tabela)
    private array $tipos = ['Folha','Verduras','Carne','Equipamentos'];

    /** Página principal: formulário + últimas despesas */
    public function index()
    {
        $db = \Config\Database::connect();

        $ultimas = $db->table('despesas')
            ->orderBy('ano','DESC')
            ->orderBy('mes','DESC')
            ->orderBy('id','DESC')
            ->get(12)
            ->getResult();

        $redirect = $this->verificarAdmin();
        if ($redirect) return $redirect;

        return $this->renderView('operacional/despesas/form', [
            'title'   => 'Lançar Despesas',
            'tipos'   => $this->tipos,
            'ultimas' => $ultimas,
            'item'    => null, // usado quando abre em modo "novo"
            'action'  => site_url('despesas/salvar'),
        ]);
    }

    /** Persistir nova despesa */
    public function salvar()
    {
        $db = \Config\Database::connect();
        $data = $this->request->getPost(['tipo','descricao','mes','ano','valor']);

        // Validação simples
        $erros = $this->validar($data);
        if ($erros) {
            return redirect()->back()->withInput()->with('msg_error', implode('<br>', $erros));
        }

        // Normaliza valor pt-BR -> float string (Ex.: 1.234,56 -> 1234.56)
        $data['valor'] = $this->normalizaValor($data['valor']);

        if (!$db->table('despesas')->insert($data)) {
            return redirect()->back()->withInput()->with('msg_error','Erro ao salvar.');
        }

        return redirect()->to('/operacional/despesas')->with('msg','Despesa lançada com sucesso.');
    }

    /** Carregar formulário de edição */
    public function editar($id = null)
    {
        $id = (int) $id;
        if ($id <= 0) {
            return redirect()->to('/operacional/despesas')->with('msg_error', 'ID inválido.');
        }

        $db = \Config\Database::connect();
        $item = $db->table('despesas')->where('id', $id)->get()->getRow();

        if (!$item) {
            return redirect()->to('/operacional/despesas')->with('msg_error', 'Despesa não encontrada.');
        }

        // Reaproveita a mesma view 'despesas/form' mostrando dados do item
        // A action aponta para atualizar.
        $ultimas = $db->table('despesas')
            ->orderBy('ano','DESC')
            ->orderBy('mes','DESC')
            ->orderBy('id','DESC')
            ->get(12)
            ->getResult();

        $redirect = $this->verificarAdmin();
        if ($redirect) return $redirect;

        return $this->renderView('operacional/despesas/form', [
            'title'   => 'Editar Despesa',
            'tipos'   => $this->tipos,
            'ultimas' => $ultimas,
            'item'    => $item,
            'action'  => site_url('despesas/atualizar/'.$id),
        ]);
    }

    /** Persistir edição */
    public function atualizar($id = null)
    {
        $id = (int) $id;
        if ($id <= 0) {
            return redirect()->to('/operacional/despesas')->with('msg_error', 'ID inválido.');
        }

        $db = \Config\Database::connect();
        $existe = $db->table('despesas')->where('id', $id)->countAllResults();
        if (!$existe) {
            return redirect()->to('/operacional/despesas')->with('msg_error', 'Despesa não encontrada.');
        }

        $data = $this->request->getPost(['tipo','descricao','mes','ano','valor']);

        $erros = $this->validar($data);
        if ($erros) {
            return redirect()->back()->withInput()->with('msg_error', implode('<br>', $erros));
        }

        $data['valor'] = $this->normalizaValor($data['valor']);

        $ok = $db->table('despesas')->where('id', $id)->update($data);

        if (!$ok) {
            return redirect()->back()->withInput()->with('msg_error','Falha ao atualizar.');
        }

        return redirect()->to('/operacional/despesas')->with('msg','Despesa atualizada com sucesso.');
    }

    /** Excluir */
    public function excluir($id = null)
    {
        $id = (int) $id;
        if ($id <= 0) {
            return redirect()->to('/operacional/despesas')->with('msg_error', 'ID inválido.');
        }

        $db = \Config\Database::connect();
        $ok = $db->table('despesas')->where('id', $id)->delete();

        return $ok
            ? redirect()->to('/operacional/despesas')->with('msg','Despesa excluída.')
            : redirect()->to('/operacional/despesas')->with('msg_error','Falha ao excluir.');
    }

    // ----------------------------
    // Helpers privados
    // ----------------------------

    /** Validação mínima de campos */
    private function validar(array $d): array
    {
        $erros = [];

        if (empty($d['tipo']) || !in_array($d['tipo'], $this->tipos, true)) {
            $erros[] = 'Tipo inválido.';
        }
        if (empty($d['descricao']) || mb_strlen(trim($d['descricao'])) < 3) {
            $erros[] = 'Descrição deve ter pelo menos 3 caracteres.';
        }
        if (empty($d['mes']) || !preg_match('/^(0?[1-9]|1[0-2])$/', (string)$d['mes'])) {
            $erros[] = 'Mês deve ser de 1 a 12.';
        }
        if (empty($d['ano']) || !preg_match('/^\d{4}$/', (string)$d['ano'])) {
            $erros[] = 'Ano inválido (use AAAA).';
        }
        if (!isset($d['valor']) || $this->normalizaValor($d['valor']) === '') {
            $erros[] = 'Valor é obrigatório.';
        } else {
            $val = $this->normalizaValor($d['valor']);
            if (!is_numeric($val)) {
                $erros[] = 'Valor inválido.';
            }
        }

        return $erros;
    }

    /** Converte "1.234,56" para "1234.56" (string) */
    private function normalizaValor($v): string
    {
        if ($v === null) return '';
        $v = (string) $v;
        // remove qualquer coisa que não seja dígito, ponto ou vírgula
        $v = preg_replace('/[^\d\.,]/', '', $v);
        // remove separador de milhar (.)
        $v = str_replace('.', '', $v);
        // troca vírgula decimal por ponto
        $v = str_replace(',', '.', $v);
        return trim($v);
    }
}
