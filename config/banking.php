<?php

use App\Services\Banking\Providers\BfaBankProvider;
use App\Services\Banking\Providers\BaiBankProvider;
use App\Services\Banking\Providers\BicBankProvider;
use App\Services\Banking\Providers\GenericBankProvider;

return [

    /*
    |--------------------------------------------------------------------------
    | Provedores bancários
    |--------------------------------------------------------------------------
    */

    'providers' => [

        'bfa' => BfaBankProvider::class,

        'bai' => BaiBankProvider::class,

        'bic' => BicBankProvider::class,

        'bpc' => GenericBankProvider::class,

        'atlantico' => GenericBankProvider::class,

        'standard_bank' => GenericBankProvider::class,

        'other' => GenericBankProvider::class,

    ],

    /*
    |--------------------------------------------------------------------------
    | Configurações gerais
    |--------------------------------------------------------------------------
    */

    'timeout' => 30,

    'connect_timeout' => 10,

    'retry_attempts' => 2,

    'retry_delay' => 1000,

];
