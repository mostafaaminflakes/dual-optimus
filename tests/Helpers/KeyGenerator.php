<?php

namespace MostafaAminFlakes\DualOptimus\Tests\Helpers;

use InvalidArgumentException;

/**
 * Helper class for generating prime/inverse/random keys for Optimus usage.
 */
class KeyGenerator
{
    /**
     * Generate a prime, its modular inverse, and a random offset for a given bit size.
     *
     * @param  int  $size  The key size in bits (must be 32 or 64).
     * @return array{
     *     prime: string,
     *     inverse: string,
     *     random: int,
     *     size: int
     * }
     *
     * @throws InvalidArgumentException If the size is not 32 or 64.
     */
    public static function generate(int $size = 32): array
    {
        if (! in_array($size, [32, 64])) {
            throw new InvalidArgumentException('Size must be 32 or 64.');
        }

        $modulus = gmp_init(bcpow('2', (string) $size));

        // Generate a large random prime and inverse
        do {
            $randomNumber = gmp_random_bits($size - 1);
            $prime = gmp_nextprime($randomNumber);
            $inverse = @gmp_invert($prime, $modulus);
        } while ($inverse === false);

        $random = random_int(0, $size === 32 ? 0xFFFFFFFF : PHP_INT_MAX);

        return [
            'prime' => gmp_strval($prime),
            'inverse' => gmp_strval($inverse),
            'random' => $random,
            'size' => $size,
        ];
    }
}
