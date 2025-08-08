<?php

namespace MostafaAminFlakes\DualOptimus;

use InvalidArgumentException;
use Jenssegers\Optimus\Optimus;

class DualOptimus
{
    private Optimus $optimus32;

    private array $config64;

    private const MAX_32_BIT = 2147483647; // 2^31 - 1

    private const MAX_64_BIT = '18446744073709551615'; // 2^64 - 1 for GMP

    private const MODULUS_64_BIT = '18446744073709551616'; // MAX_64_BIT + 1

    public function __construct(array $config32, array $config64)
    {
        $this->optimus32 = new Optimus(
            $config32['prime'],
            $config32['inverse'],
            $config32['random']
        );

        $this->config64 = array_map(
            fn ($value) => is_string($value) ? $value : sprintf('%.0f', $value),
            $config64
        );
    }

    /**
     * Encode a positive integer using 32-bit or 64-bit logic.
     *
     * @param  int  $value  Positive integer value.
     * @return int|string Encoded value.
     *
     * @throws InvalidArgumentException
     */
    public function encode(int $value): int|string
    {
        if ($value < 0) {
            throw new InvalidArgumentException('Value must be a positive integer');
        }

        return $value <= self::MAX_32_BIT
            ? $this->optimus32->encode($value)
            : $this->encode64($value);
    }

    /**
     * Decode a previously encoded 32-bit or 64-bit value.
     *
     * @param  int|string  $value  Encoded value.
     * @return int|string Decoded integer value.
     */
    public function decode(int|string $value): int|string
    {
        // Try 32-bit decode first
        try {
            $decoded32 = $this->optimus32->decode((int) $value);
            if ($decoded32 <= self::MAX_32_BIT && $this->optimus32->encode($decoded32) === $value) {
                return $decoded32;
            }
        } catch (\Throwable) {
            // Continue to 64-bit decode
        }

        return $this->decode64($value);
    }

    /**
     * Encode a positive integer using 64-bit logic.
     *
     *
     * @throws InvalidArgumentException
     */
    public function encode64(int|string $value): string
    {
        if ($value < 0) {
            throw new InvalidArgumentException('Value must be a positive integer');
        }

        if (gmp_cmp($value, self::MAX_64_BIT) > 0) {
            throw new InvalidArgumentException('Value exceeds 64-bit limit');
        }

        return $this->transform64BitValue($value, 'encode');
    }

    /**
     * Decode a 64-bit encoded value.
     */
    public function decode64(int|string $value): string
    {
        return $this->transform64BitValue($value, 'decode');
    }

    /**
     * Transform a value using 64-bit encode or decode logic.
     *
     * @param  int|string  $value  Value to transform.
     * @param  'encode'|'decode'  $mode  Operation mode.
     *
     * @throws InvalidArgumentException
     */
    private function transform64BitValue(int|string $value, string $mode): string
    {
        if (! in_array($mode, ['encode', 'decode'], true)) {
            throw new InvalidArgumentException('Invalid GMP mode. Use "encode" or "decode".');
        }

        $max = gmp_init(self::MODULUS_64_BIT); // 2^64
        $random = gmp_init($this->config64['random']);
        $multiplier = gmp_init($this->config64[$mode === 'encode' ? 'prime' : 'inverse']);

        // For decode, we subtract random before multiply
        $operand = ($mode === 'encode')
            ? gmp_mod(gmp_mul($value, $multiplier), $max)
            : gmp_mod(gmp_sub($value, $random), $max);

        // Add random for encode, multiply inverse for decode
        $result = ($mode === 'encode')
            ? gmp_mod(gmp_add($operand, $random), $max)
            : gmp_mod(gmp_mul($operand, $multiplier), $max);

        return gmp_strval($result);
    }

    /**
     * Get the Optimus instance used for 32-bit operations.
     */
    public function getOptimus32(): Optimus
    {
        return $this->optimus32;
    }
}
