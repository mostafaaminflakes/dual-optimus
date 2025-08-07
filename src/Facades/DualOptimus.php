<?php

namespace MostafaAminFlakes\DualOptimus\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static int encode(int $value)
 * @method static int decode(int $value)
 * @method static int encode64(int $value)
 * @method static int decode64(int $value)
 * @method static \MostafaAminFlakes\DualOptimus\DualOptimus connection(string $name = null)
 * @method static array getConnections()
 * @method static \Jenssegers\Optimus\Optimus getOptimus32()
 */
class DualOptimus extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'dual-optimus';
    }
}