<?php

/*
 * This file is part of Dual Optimus.
 *
 * (c) Mostafa Amin <ms.amin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Default Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the connections below you wish to use as
    | your default connection for all work. Of course, you may use many
    | connections at once using the manager class.
    |
    */

    'default' => 'main',

    /*
    |--------------------------------------------------------------------------
    | Dual Optimus Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the connections setup for your application. Each
    | connection supports both 32-bit and 64-bit configurations for
    | maximum flexibility and backward compatibility.
    |
    */

    'connections' => [

        'main' => [
            // 64-bit configuration
            'prime' => env('DUAL_OPTIMUS_PRIME_64', 9223372036854775783),
            'inverse' => env('DUAL_OPTIMUS_INVERSE_64', 9223372036854775783),
            'random' => env('DUAL_OPTIMUS_RANDOM_64', 4611686018427387904),
            'size' => 64,
        ],

        'legacy' => [
            // 32-bit configuration (for backward compatibility)
            'prime' => env('DUAL_OPTIMUS_PRIME_32', 1580030173),
            'inverse' => env('DUAL_OPTIMUS_INVERSE_32', 59260789),
            'random' => env('DUAL_OPTIMUS_RANDOM_32', 1163945558),
            'size' => 32,
        ],

    ],
];
