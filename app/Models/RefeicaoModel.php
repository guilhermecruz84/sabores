<?php
namespace App\Models;

use CodeIgniter\Model;

class RefeicaoModel extends Model
{
    protected $table          = 'refeicoes';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields  = ['empresa','servico','mes','ano','quantidade','valor'];

    protected $useTimestamps  = true;
    protected $createdField   = 'created_at';
    protected $updatedField   = 'updated_at';
    protected $deletedField   = 'deleted_at';

    protected $validationRules = [
        'empresa'    => 'required|min_length[2]|max_length[120]',
        'servico'    => 'required|min_length[2]|max_length[60]',
        'mes'        => 'required|integer|greater_than_equal_to[1]|less_than_equal_to[12]',
        'ano'        => 'required|integer|greater_than_equal_to[2000]|less_than_equal_to[2100]',
        'quantidade' => 'required|integer|greater_than_equal_to[0]',
        'valor'      => 'required|decimal',
    ];

    protected $validationMessages = [
        'empresa'    => ['required' => 'Selecione a empresa.'],
        'servico'    => ['required' => 'Selecione o serviço.'],
        'mes'        => ['required' => 'Informe o mês (1-12).'],
        'ano'        => ['required' => 'Informe o ano.'],
        'quantidade' => ['required' => 'Informe a quantidade.'],
        'valor'      => ['required' => 'Informe o valor.'],
    ];
}
