<?php
namespace App\Controllers\Operacional;
use App\Controllers\Operacional\OperacionalBase;


use App\Models\RefeicaoModel;
use Config\Empresas;

class Refeicoes extends OperacionalBase
{
    protected $refeicoes;
    protected $cfg;

    public function __construct()
    {
        parent::__construct();
        $this->refeicoes = new RefeicaoModel();
        $this->cfg = new Empresas();
    }

    // ===== CRIAR (form + últimos) =====
    public function index()
    {
        $redirect = $this->verificarAdmin();
        if ($redirect) return $redirect;

        $ultimos = $this->refeicoes
            ->orderBy('ano','DESC')->orderBy('mes','DESC')->orderBy('id','DESC')
            ->findAll(12);

        return $this->renderView('operacional/refeicoes/form', [
            'title'     => 'Lançar Refeições do Mês',
            'empresas'  => $this->cfg->empresas,
            'servicos'  => $this->cfg->servicos,
            'ultimos'   => $ultimos,
            'item'      => null, // modo criar
        ]);
    }

    public function salvar()
    {
        $data = $this->request->getPost(['empresa','servico','mes','ano','quantidade','valor']);

        // normaliza separador decimal (BR -> ponto)
        if (isset($data['valor'])) {
            $data['valor'] = str_replace(['.',','], ['', '.'], preg_replace('/[^\d,\.]/','',$data['valor']));
        }

        if (!$this->refeicoes->save($data)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->refeicoes->errors())
                ->with('msg_error', 'Verifique os campos abaixo.');
        }

        // após salvar, mande para a lista filtrando pela mesma empresa (qualquer necessidade de muitos registros)
        $empresa = urlencode($data['empresa'] ?? '');
        return redirect()->to('/operacional/refeicoes/listar?f_empresa='.$empresa)
            ->with('msg', 'Lançamento salvo com sucesso.');
    }

    // ===== LISTAR (com filtro + paginação) =====
    public function listar()
    {
        $fEmpresa = trim($this->request->getGet('f_empresa') ?? '');
        $fMes     = trim($this->request->getGet('f_mes') ?? '');
        $fAno     = trim($this->request->getGet('f_ano') ?? '');
        $perPage  = (int)($this->request->getGet('per_page') ?? 20);
        if ($perPage <= 0 || $perPage > 200) $perPage = 20;

        $builder = $this->refeicoes->orderBy('ano','DESC')->orderBy('mes','DESC')->orderBy('id','DESC');

        if ($fEmpresa !== '') {
            $builder->where('empresa', $fEmpresa);
        }
        if ($fMes !== '' && ctype_digit($fMes)) {
            $builder->where('mes', (int)$fMes);
        }
        if ($fAno !== '' && ctype_digit($fAno)) {
            $builder->where('ano', (int)$fAno);
        }

        $lista   = $builder->paginate($perPage, 'refeicoes'); // grupo 'refeicoes' para o pager
        $pager   = $this->refeicoes->pager;

        // ordenar empresas e serviços para o filtro
        $empresas = $this->cfg->empresas;
        sort($empresas, SORT_NATURAL | SORT_FLAG_CASE);
        $servicos = $this->cfg->servicos;
        sort($servicos, SORT_NATURAL | SORT_FLAG_CASE);

        return $this->renderView('operacional/refeicoes/lista', [
            'title'     => 'Lançamentos',
            'empresas'  => $empresas,
            'servicos'  => $servicos,
            'registros' => $lista,
            'pager'     => $pager,

            // filtros atuais
            'f_empresa' => $fEmpresa,
            'f_mes'     => $fMes,
            'f_ano'     => $fAno,
            'per_page'  => $perPage,
        ]);
    }

    // ===== EDITAR/ATUALIZAR =====
    public function editar($id)
    {
        $item = $this->refeicoes->find($id);
        if (!$item) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Lançamento #{$id} não encontrado.");
        }

        return $this->renderView('operacional/refeicoes/form', [
            'title'     => 'Editar Lançamento',
            'empresas'  => $this->cfg->empresas,
            'servicos'  => $this->cfg->servicos,
            'ultimos'   => [], // não precisa na edição
            'item'      => $item, // modo edição
        ]);
    }

    public function atualizar($id)
    {
        $data = $this->request->getPost(['empresa','servico','mes','ano','quantidade','valor']);
        $data['id'] = (int)$id;

        if (isset($data['valor'])) {
            $data['valor'] = str_replace(['.',','], ['', '.'], preg_replace('/[^\d,\.]/','',$data['valor']));
        }

        if (!$this->refeicoes->save($data)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->refeicoes->errors())
                ->with('msg_error', 'Verifique os campos abaixo.');
        }

        $empresa = urlencode($data['empresa'] ?? '');
        return redirect()->to('/operacional/refeicoes/listar?f_empresa='.$empresa)
            ->with('msg', 'Lançamento atualizado com sucesso.');
    }

    // ===== EXCLUIR =====
    public function excluir($id)
    {
        // mantém filtros ao voltar
        $qs = $this->request->getGet() ?? [];
        $queryString = $qs ? ('?'.http_build_query($qs)) : '';

        $this->refeicoes->delete((int)$id, true); // soft delete
        return redirect()->to('/operacional/refeicoes/listar'.$queryString)
            ->with('msg', 'Lançamento excluído.');
    }
}
