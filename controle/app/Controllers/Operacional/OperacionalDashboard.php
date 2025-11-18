<?php
namespace App\Controllers\Operacional;

use App\Controllers\Operacional\OperacionalBase;
use Config\Empresas;

class OperacionalDashboard extends OperacionalBase
{
    public function index()
    {
        $redirect = $this->verificarAdmin();
        if ($redirect) return $redirect;

        $db   = \Config\Database::connect();
        $ano  = (int) date('Y');
        $mes  = (int) date('n');

        // ===== Totais (cards principais) =====
        $totMes = (int) ($db->table('refeicoes')
            ->selectSum('quantidade', 'qtd')
            ->where('ano', $ano)->where('mes', $mes)
            ->get()->getRow('qtd') ?? 0);

        $totAno = (int) ($db->table('refeicoes')
            ->selectSum('quantidade', 'qtd')
            ->where('ano', $ano)
            ->get()->getRow('qtd') ?? 0);

        // ===== Totais por serviço (cards novos) =====
        $SERV_ALMOCO = 'Almoço';
        $SERV_JANTAR = 'Jantar';
        $SERV_CAFE   = 'Café da manhã';

        $sAlmMes = (int) ($db->table('refeicoes')->selectSum('quantidade','qtd')->where([
            'ano'=>$ano,'mes'=>$mes,'servico'=>$SERV_ALMOCO
        ])->get()->getRow('qtd') ?? 0);
        $sAlmAno = (int) ($db->table('refeicoes')->selectSum('quantidade','qtd')->where([
            'ano'=>$ano,'servico'=>$SERV_ALMOCO
        ])->get()->getRow('qtd') ?? 0);

        $sJanMes = (int) ($db->table('refeicoes')->selectSum('quantidade','qtd')->where([
            'ano'=>$ano,'mes'=>$mes,'servico'=>$SERV_JANTAR
        ])->get()->getRow('qtd') ?? 0);
        $sJanAno = (int) ($db->table('refeicoes')->selectSum('quantidade','qtd')->where([
            'ano'=>$ano,'servico'=>$SERV_JANTAR
        ])->get()->getRow('qtd') ?? 0);

        $sCafMes = (int) ($db->table('refeicoes')->selectSum('quantidade','qtd')->where([
            'ano'=>$ano,'mes'=>$mes,'servico'=>$SERV_CAFE
        ])->get()->getRow('qtd') ?? 0);
        $sCafAno = (int) ($db->table('refeicoes')->selectSum('quantidade','qtd')->where([
            'ano'=>$ano,'servico'=>$SERV_CAFE
        ])->get()->getRow('qtd') ?? 0);

        // ===== Listas fixas (empresas e serviços) =====
        $cfg       = new Empresas();
        $empresas  = $cfg->empresas;
        $servicos  = $cfg->servicos;

        // ===== Faturamento por mês (TOTAL) =====
        $fatRows = $db->table('refeicoes')
            ->select('mes, SUM(valor) AS total', false)
            ->where('ano', $ano)
            ->groupBy('mes')->orderBy('mes', 'ASC')
            ->get()->getResultArray();

        $faturamentoMeses = array_fill(1, 12, 0.0);
        foreach ($fatRows as $r) {
            $faturamentoMeses[(int)$r['mes']] = (float)$r['total'];
        }

        // ===== Faturamento por mês POR EMPRESA =====
        $fatEmpRows = $db->table('refeicoes')
            ->select('empresa, mes, SUM(valor) AS total', false)
            ->where('ano', $ano)
            ->groupBy('empresa, mes')
            ->orderBy('empresa ASC, mes ASC')
            ->get()->getResultArray();

        $fatPorEmpresa = [];
        foreach ($empresas as $e) {
            $fatPorEmpresa[$e] = array_fill(1, 12, 0.0);
        }
        foreach ($fatEmpRows as $r) {
            $e = (string) $r['empresa'];
            $m = (int) $r['mes'];
            if (!isset($fatPorEmpresa[$e])) {
                $fatPorEmpresa[$e] = array_fill(1, 12, 0.0);
            }
            $fatPorEmpresa[$e][$m] = (float) $r['total'];
        }

        // ===== Quantidade por serviço por mês =====
        $qtdServRows = $db->table('refeicoes')
            ->select('servico, mes, SUM(quantidade) AS total', false)
            ->where('ano', $ano)
            ->groupBy('servico, mes')
            ->orderBy('servico ASC, mes ASC')
            ->get()->getResultArray();

        $qtdPorServico = [];
        foreach ($servicos as $s) {
            $qtdPorServico[$s] = array_fill(1, 12, 0);
        }
        foreach ($qtdServRows as $r) {
            $s = (string) $r['servico'];
            $m = (int) $r['mes'];
            if (!isset($qtdPorServico[$s])) {
                $qtdPorServico[$s] = array_fill(1, 12, 0);
            }
            $qtdPorServico[$s][$m] = (int) $r['total'];
        }

        // ===== CMO (Folha do mês) e CMO PR =====
        $cmoTotal = (float) ($db->table('despesas')
            ->select('SUM(valor) AS total', false)
            ->where(['tipo' => 'Folha', 'ano' => $ano, 'mes' => $mes])
            ->get()->getRow('total') ?? 0);

        $cmoRefBase = (int) ($db->table('refeicoes')
            ->select('SUM(quantidade) AS qtd', false)
            ->where(['ano' => $ano, 'mes' => $mes])
            ->whereIn('servico', [$SERV_ALMOCO, $SERV_JANTAR])
            ->get()->getRow('qtd') ?? 0);

        $cmoPr = $cmoRefBase > 0 ? ($cmoTotal / $cmoRefBase) : 0.0;

        // ===== CMV (Insumos do mês) e CMV PR =====
        $cmvTotal = (float) ($db->table('despesas')
            ->select('SUM(valor) AS total', false)
            ->where(['tipo' => 'Insumos', 'ano' => $ano, 'mes' => $mes])
            ->get()->getRow('total') ?? 0);

        $cmvRefBase = (int) ($db->table('refeicoes')
            ->select('SUM(quantidade) AS qtd', false)
            ->where(['ano' => $ano, 'mes' => $mes])
            ->whereIn('servico', [$SERV_ALMOCO, $SERV_JANTAR])
            ->get()->getRow('qtd') ?? 0);

        $cmvPr = $cmvRefBase > 0 ? ($cmvTotal / $cmvRefBase) : 0.0;

        $labels = ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'];

        return $this->renderView('operacional/dashboard/index', [
            'title'   => 'Dashboard Operacional',
            'ano'     => $ano,
            'mes'     => $mes,
            'totMes'  => $totMes,
            'totAno'  => $totAno,
            'sAlmMes' => $sAlmMes, 'sAlmAno' => $sAlmAno,
            'sJanMes' => $sJanMes, 'sJanAno' => $sJanAno,
            'sCafMes' => $sCafMes, 'sCafAno' => $sCafAno,
            'labels'           => $labels,
            'faturamentoMeses' => array_values($faturamentoMeses),
            'fatPorEmpresa'    => $fatPorEmpresa,
            'qtdPorServico'    => $qtdPorServico,
            'cmoTotal'         => $cmoTotal,
            'cmoRefBase'       => $cmoRefBase,
            'cmoPr'            => $cmoPr,
            'cmvTotal'         => $cmvTotal,
            'cmvRefBase'       => $cmvRefBase,
            'cmvPr'            => $cmvPr,
        ]);
    }
}
