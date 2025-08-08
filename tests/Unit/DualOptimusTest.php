<?php

namespace MostafaAminFlakes\DualOptimus\Tests\Unit;

use InvalidArgumentException;
use Jenssegers\Optimus\Optimus;
use MostafaAminFlakes\DualOptimus\DualOptimus;
use MostafaAminFlakes\DualOptimus\DualOptimusManager;
use MostafaAminFlakes\DualOptimus\Tests\TestCase;

class DualOptimusTest extends TestCase
{
    private DualOptimusManager $manager;

    private DualOptimus $optimus;

    protected function setUp(): void
    {
        parent::setUp();

        $this->manager = app('dual-optimus');
        $this->optimus = $this->manager->connection('main');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_encode_decode_32bit_values(): void
    {
        $originalValues = [1, 100, 1000, 2147483647]; // Max 32-bit value

        foreach ($originalValues as $value) {
            $encoded = $this->optimus->encode($value);
            $decoded = $this->optimus->decode($encoded);

            $this->assertEquals($value, $decoded);
            $this->assertNotEquals($value, $encoded); // Should be obfuscated
        }
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_encode_decode_64bit_values(): void
    {
        $originalValues = [
            2147483648,  // Just above 32-bit limit
            4294967296,  // 2^32
            9223372036854775807,  // Max 64-bit value
        ];

        foreach ($originalValues as $value) {
            $encoded = $this->optimus->encode($value);
            $decoded = $this->optimus->decode($encoded);

            $this->assertEquals($value, $decoded);
            $this->assertNotEquals($value, $encoded);
        }
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_64bit_encoding(): void
    {
        $value = 9876543210;
        $encoded = $this->optimus->encode64($value);
        $decoded = $this->optimus->decode64($encoded);

        $this->assertEquals($value, $decoded);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_invalid_negative_value(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->optimus->encode(-1);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_manager_connections(): void
    {
        $connections = $this->manager->getConnections();
        $this->assertContains('main', $connections);
        $this->assertContains('legacy', $connections);

        $mainConnection = $this->manager->connection('main');
        $testConnection = $this->manager->connection('legacy');

        $this->assertInstanceOf(DualOptimus::class, $mainConnection);
        $this->assertInstanceOf(DualOptimus::class, $testConnection);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_manager_default_connection(): void
    {
        $defaultConnection = $this->manager->connection();
        $mainConnection = $this->manager->connection('main');

        // Both should encode the same way since they use the same config
        $value = 12345;
        $this->assertEquals(
            $defaultConnection->encode($value),
            $mainConnection->encode($value)
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_optimus_32_integration(): void
    {
        $optimus32 = $this->optimus->getOptimus32();
        $this->assertInstanceOf(Optimus::class, $optimus32);

        // Test that it works with the underlying Optimus
        $value = 12345;
        $encoded = $this->optimus->encode($value);
        $optimusEncoded = $optimus32->encode($value);

        $this->assertEquals($encoded, $optimusEncoded);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_throws_exception_if_value_is_negative(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Value must be a positive integer');

        $this->optimus->encode64(-1);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_throws_exception_if_value_exceeds_64bit_limit(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Value exceeds 64-bit limit');

        // 2^64 = 18446744073709551616 (just over the 64-bit limit)
        $overflowValue = gmp_strval(gmp_add('18446744073709551615', 1));

        $this->optimus->encode64($overflowValue);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_throws_exception_if_invalid_mode_is_passed(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid GMP mode. Use "encode" or "decode".');

        $reflection = new \ReflectionClass($this->optimus);
        $method = $reflection->getMethod('transform64BitValue');
        $method->setAccessible(true);

        $method->invokeArgs($this->optimus, [1, 'invalid']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_decode_catch_block_with_invalid_32bit_value(): void
    {
        // Create a scenario where 32-bit decode fails but 64-bit succeeds
        // We'll use a 64-bit encoded value that when passed to 32-bit decode will throw
        $originalValue = 123456789;

        // Force 64-bit encoding
        $encoded64 = $this->optimus->encode64($originalValue);

        // Now decode using the general decode method
        // This should try 32-bit first (fail), then fallback to 64-bit (succeed)
        $decoded = $this->optimus->decode($encoded64);

        $this->assertEquals($originalValue, $decoded);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_decode_catch_block_with_corrupted_value(): void
    {
        // Test with a value that will cause 32-bit decode to throw but 64-bit to handle
        // Use a string value that represents a large number
        $largeEncodedValue = '9223372036854775000'; // Large 64-bit value

        $decoded = $this->optimus->decode($largeEncodedValue);

        // Should successfully decode using 64-bit fallback
        $this->assertIsString($decoded);
    }
}
