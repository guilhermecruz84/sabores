<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Empresas extends BaseConfig
{
    public array $empresas = [
        'Artely',
        'DAF',
        'JEA',
        'PSCA',
        'Facchini',
        'Cerro Azul',
        'Aramesul',
    ];

    public array $servicos = [
        'Almoço',
        'Jantar',
        'Café da manhã',
        'Lanche',
    ];
}
