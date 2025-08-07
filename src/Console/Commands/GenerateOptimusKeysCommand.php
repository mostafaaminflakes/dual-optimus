<?php

namespace MostafaAminFlakes\DualOptimus\Console\Commands;

use Illuminate\Console\Command;

class GenerateOptimusKeysCommand extends Command
{
    protected $signature = 'dual-optimus:generate {size=32 : Bit size (32 or 64)}';
    protected $description = 'Generate a valid prime/inverse/random set for DualOptimus';

    public function handle(): int
    {
        $size = (int) $this->argument('size');

        if (!in_array($size, [32, 64])) {
            $this->error('Size must be 32 or 64.');
            return 1;
        }

        $modulus = gmp_init(bcpow('2', (string) $size));

        // Generate a large random prime
        do {
            $randomNumber = gmp_random_bits($size - 1);
            $prime = gmp_nextprime($randomNumber);
            $inverse = @gmp_invert($prime, $modulus);
        } while ($inverse === false);

        $random = random_int(0, $size === 32 ? 0xFFFFFFFF : PHP_INT_MAX);

        $this->line("Generated {$size}-bit Optimus keys:");
        $this->info("Prime:   " . gmp_strval($prime));
        $this->info("Inverse: " . gmp_strval($inverse));
        $this->info("Random:  " . $random);

        return 0;
    }
}