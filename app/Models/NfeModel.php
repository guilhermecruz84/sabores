<?php
namespace App\Models;

use CodeIgniter\Model;

class NfeModel extends Model
{
    protected $DBGroup       = 'default';
    protected $table         = 'nfe_imports';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;

    protected $allowedFields = [
        'created_at', 'user_id',
        'total_xmls', 'total_itens',
        'competencia',
        'empresa_id', 'empresa_nome',
        'servico_id', 'servico_nome',
        'status',
    ];

    // ================= LISTAGEM (home) =================
    public function list_last_imports(int $limit = 20): array
    {
        return $this->builder('nfe_imports')
            ->select('id, created_at, total_xmls, total_itens, competencia, empresa_id, empresa_nome, servico_id, servico_nome, status')
            ->orderBy('id', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }

    // ================= FLUXO DE IMPORTAÇÃO =================
    public function create_import(?int $userId = null): int
    {
        $this->builder('nfe_imports')->insert([
            'created_at' => date('Y-m-d H:i:s'),
            'user_id'    => $userId,
            'total_xmls' => 0,
            'total_itens'=> 0,
            'status'     => 'PENDENTE',
        ]);
        return (int) $this->db->insertID();
    }

    public function insert_doc(int $importId, array $doc): int
    {
        $row = [
            'import_id'    => $importId,
            'chave'        => $doc['chave']        ?? null,
            'numero'       => $doc['numero']       ?? null,
            'serie'        => $doc['serie']        ?? null,
            'dhEmi'        => $doc['dhEmi']        ?? null,
            'emit_cnpj'    => $doc['emit_cnpj']    ?? null,
            'emit_nome'    => $doc['emit_nome']    ?? null,
            'dest_cnpjcpf' => $doc['dest_cnpjcpf'] ?? null,
            'dest_nome'    => $doc['dest_nome']    ?? null,
            'vNF'          => $doc['vNF']          ?? null,
            'modelo'       => $doc['modelo']       ?? null,
            'arquivo'      => $doc['arquivo']      ?? null,
        ];

        try {
            $this->builder('nfe_docs')->insert($row);
            return (int) $this->db->insertID();
        } catch (\Throwable $e) {
            if (!empty($row['chave'])) {
                $exists = $this->builder('nfe_docs')
                    ->select('id')
                    ->where('chave', $row['chave'])
                    ->get()->getRowArray();
                if ($exists && isset($exists['id'])) {
                    $this->builder('nfe_docs')->where('id', $exists['id'])->update(['import_id' => $importId]);
                    return (int) $exists['id'];
                }
            }
            throw $e;
        }
    }

    public function insert_item(int $importId, int $docId, int $nItem, array $it): void
    {
        $row = [
            'import_id' => $importId,
            'doc_id'    => $docId,
            'nItem'     => $nItem,
            'cProd'     => $it['cProd']  ?? null,
            'xProd'     => $it['xProd']  ?? null,
            'NCM'       => $it['NCM']    ?? null,
            'CFOP'      => $it['CFOP']   ?? null,
            'uCom'      => $it['uCom']   ?? null,
            'qCom'      => isset($it['qCom'])   && $it['qCom']   !== '' ? $it['qCom']   : null,
            'vUnCom'    => isset($it['vUnCom']) && $it['vUnCom'] !== '' ? $it['vUnCom'] : null,
            'vProd'     => isset($it['vProd'])  && $it['vProd']  !== '' ? $it['vProd']  : null,
            'vDesc'     => isset($it['vDesc'])  && $it['vDesc']  !== '' ? $it['vDesc']  : null,
            'vItem'     => isset($it['vItem'])  && $it['vItem']  !== '' ? $it['vItem']  : null,
        ];
        $this->builder('nfe_items')->insert($row);
    }

    public function finish_import_counts(int $importId, int $totalXmls, int $totalItens): void
    {
        $this->builder('nfe_imports')
            ->where('id', $importId)
            ->update([
                'total_xmls' => $totalXmls,
                'total_itens'=> $totalItens,
            ]);
    }

    // ================= REVISÃO / ITENS =================
    public function get_import(int $importId): ?array
    {
        $row = $this->builder('nfe_imports')
            ->where('id', $importId)
            ->get()
            ->getRowArray();

        return $row ?: null;
    }

    public function get_docs(int $importId): array
    {
        return $this->builder('nfe_docs')
            ->where('import_id', $importId)
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function sum_import(int $importId): array
    {
        $docs = (int) $this->builder('nfe_docs')
            ->select('COUNT(*) AS c')
            ->where('import_id', $importId)
            ->get()->getRow('c');

        $itens = (int) $this->builder('nfe_items')
            ->select('COUNT(*) AS c')
            ->where('import_id', $importId)
            ->get()->getRow('c');

        $vNF = (float) ($this->builder('nfe_docs')
            ->select('COALESCE(SUM(vNF),0) AS s')
            ->where('import_id', $importId)
            ->get()->getRow('s') ?? 0);

        return ['docs' => $docs, 'itens' => $itens, 'vNF' => $vNF];
    }

    public function get_items_by_import(int $importId): array
    {
        return $this->db->table('nfe_items i')
            ->select('i.id, i.doc_id, i.nItem, i.cProd, i.xProd, i.uCom, i.qCom, i.vUnCom, i.vProd, i.vDesc, i.vItem')
            ->where('i.import_id', $importId)
            ->orderBy('i.doc_id', 'ASC')
            ->orderBy('i.nItem', 'ASC')
            ->get()->getResultArray();
    }

    // ================= DADOS AUXILIARES =================
    public function list_empresas(): array
    {
        if (method_exists($this->db, 'tableExists') && $this->db->tableExists('empresas')) {
            return $this->db->table('empresas')
                ->select('id, nome_fantasia as nome')
                ->orderBy('nome_fantasia', 'ASC')
                ->get()
                ->getResultArray();
        }

        $rows = $this->builder('nfe_docs')
            ->select('DISTINCT dest_nome AS nome', false)
            ->where('dest_nome IS NOT NULL')
            ->orderBy('dest_nome', 'ASC')
            ->get()
            ->getResultArray();

        return array_map(fn($r) => ['id' => null, 'nome' => $r['nome']], $rows);
    }

    public function list_servicos(): array
    {
        // Usar Config\Empresas que contém a lista de serviços do sistema Sabores
        $cfg = new \Config\Empresas();
        $servicos = $cfg->servicos ?? [];

        // Retornar no formato esperado pela view: ['id' => index, 'nome' => nome_servico]
        $result = [];
        foreach ($servicos as $index => $servico) {
            $result[] = [
                'id' => $index + 1, // ID sequencial começando em 1
                'nome' => $servico
            ];
        }

        return $result;
    }

    public function get_servicos_mapa(array $ids): array
    {
        $ids = array_values(array_unique(array_map('intval', array_filter($ids))));
        if (empty($ids)) return [];

        $rows = $this->db->table('servicos')
            ->select('id, titulo')
            ->whereIn('id', $ids)
            ->get()->getResultArray();

        $map = [];
        foreach ($rows as $r) {
            $map[(int)$r['id']] = $r['titulo'];
        }
        return $map;
    }

    // ================= REF EIÇÕES / FINALIZAÇÃO =================
    public function insert_refeicao(array $row): bool
    {
        // row esperado: empresa, servico, servico_id, servico_nome, mes, ano, quantidade, valor, created_at
        return (bool) $this->db->table('refeicoes')->insert($row);
    }

    public function set_associations_header(int $importId, string $competenciaYYYYMM, string $empresaNome, string $status = 'PARCIAL'): void
    {
        $this->builder('nfe_imports')
            ->where('id', $importId)
            ->update([
                'competencia'  => $competenciaYYYYMM,
                'empresa_nome' => $empresaNome,
                'status'       => $status,
            ]);
    }
}
