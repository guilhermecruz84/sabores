<?php
namespace App\Controllers\Operacional;

use App\Controllers\Operacional\OperacionalBase;

class Cmo extends OperacionalBase
{
    public function index()
    {
        $db       = db_connect();                 // atalho de \Config\Database::connect()
        $ano      = (int) ($this->request->getGet('ano') ?? date('Y'));
        $mesAtual = (int) date('n');

        // Nomes p/ card e labels p/ gráfico
        $mesesNomes = [
            1=>'Janeiro', 2=>'Fevereiro', 3=>'Março', 4=>'Abril',
            5=>'Maio', 6=>'Junho', 7=>'Julho', 8=>'Agosto',
            9=>'Setembro', 10=>'Outubro', 11=>'Novembro', 12=>'Dezembro'
        ];
        $labels = ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'];

        // ===== CMO (Total Folha) por mês =====
        $cmoRows = $db->table('despesas')
            ->select('mes, SUM(valor) AS total', false)
            ->where('ano', $ano)
            ->where('tipo', 'Folha')
            ->groupBy('mes')->orderBy('mes', 'ASC')
            ->get()->getResultArray();

        $cmoMeses = array_fill(1, 12, 0.0);
        foreach ($cmoRows as $r) {
            $mes = (int) $r['mes'];
            $cmoMeses[$mes] = (float) $r['total'];
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

        // ===== CMO PR (protege divisão por zero) =====
        $cmoPrMeses = array_fill(1, 12, 0.0);
        for ($m = 1; $m <= 12; $m++) {
            $cmoPrMeses[$m] = $baseMeses[$m] > 0 ? round($cmoMeses[$m] / $baseMeses[$m], 2) : 0.0;
        }

        // Para o card do topo
        $mesNomeCard = $mesesNomes[$mesAtual] ?? '';
        $totalMes    = $cmoMeses[$mesAtual]   ?? 0.0;

        $redirect = $this->verificarAdmin();
        if ($redirect) return $redirect;

        return $this->renderView('operacional/cmo/index', [
            'title'      => 'CMO - Histórico',
            'ano'        => $ano,
            'mesNome'    => $mesNomeCard,           // ✅ usado no card
            'total'      => $totalMes,              // ✅ usado no card
            'labels'     => $labels,
            'cmoMeses'   => array_values($cmoMeses),   // 12 posições
            'cmoPrMeses' => array_values($cmoPrMeses), // 12 posições
        ]);
    }
}
