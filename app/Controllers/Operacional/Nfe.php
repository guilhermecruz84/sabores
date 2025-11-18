<?php
namespace App\Controllers\Operacional;

use App\Controllers\Operacional\OperacionalBase;
use App\Models\NfeModel;

class Nfe extends OperacionalBase
{
    // Rotas públicas configuradas em app/Config/Routes.php (sem "dash")
    private const BASE = 'nfe';

    // Formulário de upload + listagem de últimos lotes
    public function index()
    {
        $model = new NfeModel();
        $data = [
            'title'   => 'Importar NF-e (XML)',
            'imports' => $model->list_last_imports(20),
        ];
        $redirect = $this->verificarAdmin();
        if ($redirect) return $redirect;

        return $this->renderView('operacional/nfe/import_form', $data);
    }

    // Recebe múltiplos XMLs
    public function upload()
    {
        // garanta que o name do input é "xmls[]" na view
        $filesBag  = $this->request->getFiles();
        $filesList = $filesBag['xmls'] ?? null;

        if (!$filesList) {
            return redirect()->to(site_url(self::BASE))
                ->with('erro', 'Nenhum arquivo enviado.');
        }

        if (!is_array($filesList)) {
            $filesList = [$filesList];
        }

        $model      = new NfeModel();
        $importId   = $model->create_import();
        $countXML   = 0;
        $countItens = 0;

        foreach ($filesList as $file) {
            if (!$file || !$file->isValid()) continue;

            $tempName = $file->getTempName();
            if (!$tempName || !is_file($tempName)) continue;

            $xmlStr = @file_get_contents($tempName);
            if (!$xmlStr) continue;

            try {
                $parsed = $this->parseNFe($xmlStr);
                if (!$parsed || empty($parsed['itens'])) continue;

                $docId = $model->insert_doc($importId, array_merge($parsed['doc'], [
                    'arquivo' => $file->getClientName()
                ]));

                foreach ($parsed['itens'] as $nItem => $it) {
                    $model->insert_item($importId, $docId, $nItem, $it);
                    $countItens++;
                }

                $countXML++;
            } catch (\Throwable $e) {
                log_message('error', 'Erro parse NFe '.$file->getClientName().' => '.$e->getMessage());
            }
        }

        $model->finish_import_counts($importId, $countXML, $countItens);

        return redirect()
            ->to(site_url(self::BASE.'/review/'.$importId))
            ->with('ok', "Importação criada (#{$importId}): {$countXML} XML(s), {$countItens} item(ns).");
    }

    // Tela para associar: competência (mês), empresa e SERVIÇO POR ITEM
    public function review($importId)
    {
        $model   = new NfeModel();
        $import  = $model->get_import($importId);
        if (!$import) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $docs     = $model->get_docs($importId) ?? [];
        $totais   = $model->sum_import($importId) ?? ['docs'=>0,'itens'=>0,'vNF'=>0];
        $items    = $model->get_items_by_import($importId) ?? [];
        $empresas = $model->list_empresas();
        if ($empresas instanceof \Traversable) {
            $empresas = iterator_to_array($empresas, false);
        } elseif (!is_array($empresas)) {
            $empresas = $empresas ? [$empresas] : [];
        }
        $servicos = $model->list_servicos();
        if ($servicos instanceof \Traversable) {
            $servicos = iterator_to_array($servicos, false);
        } elseif (!is_array($servicos)) {
            $servicos = $servicos ? [$servicos] : [];
        }

        $data = [
            'title'    => "Associar Lote #{$importId}",
            'import'   => $import,
            'docs'     => $docs,
            'totais'   => $totais,
            'items'    => $items,    // <- itens do lote
            'empresas' => $empresas,
            'servicos' => $servicos,
        ];

        $redirect = $this->verificarAdmin();
        if ($redirect) return $redirect;

        return $this->renderView('operacional/nfe/review', $data);
    }

    // Salva: competência/empresa + serviço POR ITEM => insere em `refeicoes`
    public function finalize($importId)
    {
        $comp         = (string) $this->request->getPost('competencia'); // YYYY-MM preferido
        $empresaNome  = trim((string) $this->request->getPost('empresa'));
        $itensServico = $this->request->getPost('itens_servico') ?? [];   // [itemId => servico_id]

        if ($empresaNome === '') {
            return redirect()->to(site_url(self::BASE.'/review/'.$importId))
                ->withInput()
                ->with('erro', 'Informe a empresa.');
        }

        // Normaliza competência
        [$ano, $mes] = $this->normalizeCompetencia($comp);
        if ($ano === null || $mes === null) {
            return redirect()->to(site_url(self::BASE.'/review/'.$importId))
                ->withInput()
                ->with('erro', 'Formato de competência inválido. Use YYYY-MM ou MM/YYYY.');
        }

        $model = new NfeModel();

        // Pega todos os itens do lote
        $items = $model->get_items_by_import((int)$importId);
        if (empty($items)) {
            return redirect()->to(site_url(self::BASE.'/review/'.$importId))
                ->withInput()
                ->with('erro', 'Este lote não possui itens para associar.');
        }

        // Mapa de serviços (id => titulo) para preencher servico/servico_nome
        $servicoIds = array_values(array_unique(array_map('intval', is_array($itensServico) ? array_filter($itensServico, fn($v) => (int)$v > 0) : [])));
        $servMapa   = $model->get_servicos_mapa($servicoIds); // [id => titulo]

        $inseridos = 0;
        foreach ($items as $it) {
            $itemId = (int) ($it['id'] ?? 0);
            $sid    = (int) ($itensServico[$itemId] ?? 0);
            if ($sid <= 0) {
                // sem serviço selecionado -> ignora
                continue;
            }
            $servNome = $servMapa[$sid] ?? null;

            // quantidade: qCom (decimal) -> inteiro (arredondado)
            $qCom = isset($it['qCom']) && $it['qCom'] !== null ? (float) $it['qCom'] : 0.0;
            $quantidade = (int) round($qCom);
            if ($quantidade < 0) $quantidade = 0;

            // valor total do item: prioriza vItem; senão, vProd
            $vItem = isset($it['vItem']) && $it['vItem'] !== null ? (float)$it['vItem'] : null;
            $vProd = isset($it['vProd']) && $it['vProd'] !== null ? (float)$it['vProd'] : 0.0;
            $valor = $vItem !== null ? $vItem : $vProd;

            $ok = $model->insert_refeicao([
                'empresa'      => $empresaNome,
                'servico'      => $servNome,    // também grava no campo curto
                'servico_id'   => $sid,
                'servico_nome' => $servNome,
                'mes'          => $mes,
                'ano'          => $ano,
                'quantidade'   => $quantidade,
                'valor'        => $valor,
                'created_at'   => date('Y-m-d H:i:s'),
            ]);

            if ($ok) $inseridos++;
        }

        // Atualiza cabeçalho do lote (competência/empresa) e status
        $status = $inseridos > 0 ? 'FINALIZADO' : 'PARCIAL';
        $model->set_associations_header((int)$importId, sprintf('%04d-%02d', $ano, $mes), $empresaNome, $status);

        return redirect()->to(site_url(self::BASE))
            ->with('ok', "Itens processados: {$inseridos}. Lote #{$importId} atualizado.");
    }

    /**
     * Normaliza competência e retorna [ano, mes] como inteiros.
     * Aceita: "YYYY-MM", "YYYY-MM-DD", "MM/YYYY", "MM-YYYY".
     * Retorna [null, null] se inválido.
     */
    private function normalizeCompetencia(?string $input): array
    {
        $s = trim((string)$input);
        if ($s === '') {
            return [null, null];
        }

        // Substitui "/" por "-" para facilitar parsing e remove espaços
        $s = str_replace('/', '-', $s);
        $s = preg_replace('/\s+/', '', $s);

        // YYYY-MM(-DD)
        if (preg_match('/^(?<y>\d{4})-(?<m>\d{2})(?:-\d{2})?$/', $s, $m)) {
            $y  = (int)$m['y'];
            $mm = (int)$m['m'];
            if ($mm >= 1 && $mm <= 12) {
                return [$y, $mm];
            }
        }

        // MM-YYYY
        if (preg_match('/^(?<m>\d{2})-(?<y>\d{4})$/', $s, $m)) {
            $y  = (int)$m['y'];
            $mm = (int)$m['m'];
            if ($mm >= 1 && $mm <= 12) {
                return [$y, $mm];
            }
        }

        return [null, null];
    }

    /**
     * Parser NF-e/NFC-e
     * Extrai: chave, número, série, dhEmi, emit/dest, vNF, modelo; e itens (cProd,xProd,NCM,CFOP,uCom,qCom,vUnCom,vProd,vDesc)
     */
    private function parseNFe(string $xmlStr): array
    {
        \libxml_use_internal_errors(true);

        $xml = \simplexml_load_string($xmlStr);
        if ($xml === false) {
            throw new \Exception('XML inválido');
        }

        // NFe pode vir com <nfeProc><NFe>...</NFe></nfeProc>
        $nfe    = $xml->NFe ?? $xml;
        $infNFe = $nfe->infNFe ?? ($nfe->children()->infNFe ?? null);
        if (!$infNFe) {
            throw new \Exception('infNFe não encontrado');
        }

        // Namespaces / XPath
        $dom = new \DOMDocument();
        $dom->loadXML($xmlStr);
        $xp = new \DOMXPath($dom);
        $xp->registerNamespace('nfe', 'http://www.portalfiscal.inf.br/nfe');

        // Cabeçalho (ide)
        $ide = $xp->query('//nfe:ide')->item(0);
        $getVal = function($tag) use ($xp, $ide) {
            if (!$ide) return null;
            $n = $xp->query('nfe:'.$tag, $ide);
            return $n->length ? $n->item(0)->nodeValue : null;
        };

        $mod   = $getVal('mod');    // 55/65
        $serie = $getVal('serie');
        $nNF   = $getVal('nNF');
        $dhEmi = $getVal('dhEmi') ?: $getVal('dEmi');

        // Emitente
        $emit = $xp->query('//nfe:emit')->item(0);
        $getEmit = function($tag) use ($xp, $emit) {
            if (!$emit) return null;
            $n = $xp->query('nfe:'.$tag, $emit);
            return $n->length ? $n->item(0)->nodeValue : null;
        };
        $emitCNPJ = $getEmit('CNPJ') ?: $getEmit('CPF');
        $emitNome = $getEmit('xNome');

        // Destinatário
        $dest = $xp->query('//nfe:dest')->item(0);
        $getDest = function($tag) use ($xp, $dest) {
            if (!$dest) return null;
            $n = $xp->query('nfe:'.$tag, $dest);
            return $n->length ? $n->item(0)->nodeValue : null;
        };
        $destDoc  = $getDest('CNPJ') ?: $getDest('CPF');
        $destNome = $getDest('xNome');

        // Totais
        $vNF = null;
        $tot = $xp->query('//nfe:total/nfe:ICMSTot')->item(0);
        if ($tot) {
            $n = $xp->query('nfe:vNF', $tot);
            $vNF = $n->length ? (float)$n->item(0)->nodeValue : null;
        }

        // Chave de acesso (atributo @Id) - cobre infNFe e infNfe
        $chave = null;
        $infNFeNode = $xp->query('//nfe:infNFe | //nfe:infNfe')->item(0);
        if ($infNFeNode && $infNFeNode->attributes && $infNFeNode->attributes->getNamedItem('Id')) {
            $idAttr = $infNFeNode->attributes->getNamedItem('Id')->nodeValue;
            $chave  = \preg_replace('/^NFe/i', '', $idAttr);
        }

        // Itens
        $itens    = [];
        $detNodes = $xp->query('//nfe:det');
        foreach ($detNodes as $idx => $det) {
            $prod = $xp->query('nfe:prod', $det)->item(0);
            if (!$prod) continue;

            $getProd = function($tag) use ($xp, $prod) {
                $n = $xp->query('nfe:'.$tag, $prod);
                return $n->length ? $n->item(0)->nodeValue : null;
            };

            $vDesc = $getProd('vDesc');
            $vProd = $getProd('vProd');
            $vUn   = $getProd('vUnCom');

            $itens[$idx + 1] = [
                'cProd'  => $getProd('cProd'),
                'xProd'  => $getProd('xProd'),
                'NCM'    => $getProd('NCM'),
                'CFOP'   => $getProd('CFOP'),
                'uCom'   => $getProd('uCom'),
                'qCom'   => $getProd('qCom'),
                'vUnCom' => $vUn,
                'vProd'  => $vProd,
                'vDesc'  => $vDesc,
                'vItem'  => (\is_numeric($vProd) && \is_numeric($vDesc))
                              ? (float)$vProd - (float)$vDesc
                              : (\is_numeric($vProd) ? (float)$vProd : null),
            ];
        }

        return [
            'doc' => [
                'chave'        => $chave,
                'numero'       => $nNF,
                'serie'        => $serie,
                'dhEmi'        => $dhEmi ? \date('Y-m-d H:i:s', \strtotime((string)$dhEmi)) : null,
                'emit_cnpj'    => $emitCNPJ,
                'emit_nome'    => $emitNome,
                'dest_cnpjcpf' => $destDoc,
                'dest_nome'    => $destNome,
                'vNF'          => $vNF,
                'modelo'       => $mod
            ],
            'itens' => $itens
        ];
    }
}
