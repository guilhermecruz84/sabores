<?php
namespace App\Controllers\Operacional;

use App\Controllers\Operacional\OperacionalBase;

class Cmv extends OperacionalBase
{
    public function index()
    {
        $db       = db_connect();
        $ano      = (int) ($this->request->getGet('ano') ?? date('Y'));
        $mesAtual = (int) date('n');

        // Nomes para card e labels para gráfico
        $mesesNomes = [
            1=>'Janeiro', 2=>'Fevereiro', 3=>'Março', 4=>'Abril',
            5=>'Maio', 6=>'Junho', 7=>'Julho', 8=>'Agosto',
            9=>'Setembro', 10=>'Outubro', 11=>'Novembro', 12=>'Dezembro'
        ];
        $labels = ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'];

        // ===== CMV (Total Insumos) por mês =====
        $cmvRows = $db->table('despesas')
            ->select('mes, SUM(valor) AS total', false)
            ->where('ano', $ano)
            ->where('tipo', 'Insumos')
            ->groupBy('mes')->orderBy('mes', 'ASC')
            ->get()->getResultArray();

        $cmvMeses = array_fill(1, 12, 0.0);
        foreach ($cmvRows as $r) {
            $mes = (int) $r['mes'];
            $cmvMeses[$mes] = (float) $r['total'];
        }

        // ===== Base (Almoço + Jantar) por mês =====
        $baseRows = $db->table('refeicoes')
            ->select('mes, SUM(quantidade) AS qtd', false)
            ->where('ano', $ano)
            ->whereIn('servico', ['Almoço', 'Jantar'])
            ->groupBy('mes')->orderBy('mes', 'ASC')
            ->get()->getResultArray();

        $baseMeses = array_fill(1, 12, 0);
        foreach ($baseRows as $r) {
            $mes = (int) $r['mes'];
            $baseMeses[$mes] = (int) $r['qtd'];
        }

        // ===== CMV PR (custo por refeição - protege divisão por zero) =====
        $cmvPrMeses = array_fill(1, 12, 0.0);
        for ($m = 1; $m <= 12; $m++) {
            $cmvPrMeses[$m] = $baseMeses[$m] > 0 ? round($cmvMeses[$m] / $baseMeses[$m], 2) : 0.0;
        }

        // ===== VERDURAS (Total Verduras) por mês =====
        $verdurasRows = $db->table('despesas')
            ->select('mes, SUM(valor) AS total', false)
            ->where('ano', $ano)
            ->where('tipo', 'Verduras')
            ->groupBy('mes')->orderBy('mes', 'ASC')
            ->get()->getResultArray();

        $verdurasMeses = array_fill(1, 12, 0.0);
        foreach ($verdurasRows as $r) {
            $mes = (int) $r['mes'];
            $verdurasMeses[$mes] = (float) $r['total'];
        }

        // ===== VERDURAS PR (custo por refeição) =====
        $verdurasPrMeses = array_fill(1, 12, 0.0);
        for ($m = 1; $m <= 12; $m++) {
            $verdurasPrMeses[$m] = $baseMeses[$m] > 0 ? round($verdurasMeses[$m] / $baseMeses[$m], 2) : 0.0;
        }

        // ===== PROTEÍNAS/CARNE (Total Carne) por mês =====
        $carneRows = $db->table('despesas')
            ->select('mes, SUM(valor) AS total', false)
            ->where('ano', $ano)
            ->where('tipo', 'Carne')
            ->groupBy('mes')->orderBy('mes', 'ASC')
            ->get()->getResultArray();

        $carneMeses = array_fill(1, 12, 0.0);
        foreach ($carneRows as $r) {
            $mes = (int) $r['mes'];
            $carneMeses[$mes] = (float) $r['total'];
        }

        // ===== PROTEÍNAS PR (custo por refeição) =====
        $carnePrMeses = array_fill(1, 12, 0.0);
        for ($m = 1; $m <= 12; $m++) {
            $carnePrMeses[$m] = $baseMeses[$m] > 0 ? round($carneMeses[$m] / $baseMeses[$m], 2) : 0.0;
        }

        // Para o card do topo
        $mesNomeCard = $mesesNomes[$mesAtual] ?? '';
        $totalMes    = $cmvMeses[$mesAtual]   ?? 0.0;
        $baseMesAtual = $baseMeses[$mesAtual] ?? 0;
        $cmvPrMesAtual = $baseMesAtual > 0 ? round($totalMes / $baseMesAtual, 2) : 0.0;

        $verdurasTotal = $verdurasMeses[$mesAtual] ?? 0.0;
        $verdurasPr = $baseMesAtual > 0 ? round($verdurasTotal / $baseMesAtual, 2) : 0.0;

        $carneTotal = $carneMeses[$mesAtual] ?? 0.0;
        $carnePr = $baseMesAtual > 0 ? round($carneTotal / $baseMesAtual, 2) : 0.0;

        $redirect = $this->verificarAdmin();
        if ($redirect) return $redirect;

        return $this->renderView('operacional/cmv/index', [
            'title'         => 'CMV - Histórico',
            'ano'           => $ano,
            'mesNome'       => $mesNomeCard,
            'total'         => $totalMes,
            'baseMes'       => $baseMesAtual,
            'cmvPr'         => $cmvPrMesAtual,
            'verdurasTotal' => $verdurasTotal,
            'verdurasPr'    => $verdurasPr,
            'carneTotal'    => $carneTotal,
            'carnePr'       => $carnePr,
            'labels'        => $labels,
            'cmvMeses'      => array_values($cmvMeses),
            'cmvPrMeses'    => array_values($cmvPrMeses),
            'baseMeses'     => array_values($baseMeses),
            'verdurasMeses' => array_values($verdurasMeses),
            'verdurasPrMeses' => array_values($verdurasPrMeses),
            'carneMeses'    => array_values($carneMeses),
            'carnePrMeses'  => array_values($carnePrMeses),
        ]);
    }
}
