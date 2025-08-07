<?php

namespace MostafaAminFlakes\DualOptimus\Tests\Feature;

use MostafaAminFlakes\DualOptimus\Tests\TestCase;

class GenerateOptimusKeysCommandTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function test_generate_optimus_keys_command()
    {
        $this->artisan('dual-optimus:generate', ['size' => 64])
            ->expectsOutput('Generated 64-bit Optimus keys:')
            ->assertExitCode(0);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_fails_if_invalid_size_is_passed()
    {
        $this->artisan('dual-optimus:generate', ['size' => 128])
            ->expectsOutput('Size must be 32 or 64.')
            ->assertExitCode(1);
    }
}
