<?php

namespace MostafaAminFlakes\DualOptimus\Tests\Unit;

use InvalidArgumentException;
use MostafaAminFlakes\DualOptimus\DualOptimus;
use MostafaAminFlakes\DualOptimus\DualOptimusManager;
use MostafaAminFlakes\DualOptimus\DualOptimusServiceProvider;
use MostafaAminFlakes\DualOptimus\Tests\TestCase;

class ManagerTest extends TestCase
{
    private DualOptimusManager $manager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->manager = app('dual-optimus');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_manager_instance(): void
    {
        $this->assertInstanceOf(DualOptimusManager::class, $this->manager);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_get_connections(): void
    {
        $connections = $this->manager->getConnections();

        $this->assertContains('main', $connections);
        $this->assertContains('legacy', $connections);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_default_connection(): void
    {
        $connection = $this->manager->connection();

        $this->assertInstanceOf(DualOptimus::class, $connection);

        // Test that it works
        $value = 12345;
        $encoded = $connection->encode($value);
        $decoded = $connection->decode($encoded);

        $this->assertEquals($value, $decoded);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_specific_connection(): void
    {
        $mainConnection = $this->manager->connection('main');
        $testConnection = $this->manager->connection('legacy');

        $this->assertInstanceOf(DualOptimus::class, $mainConnection);
        $this->assertInstanceOf(DualOptimus::class, $testConnection);

        // Both should work the same way with same config
        $value = 54321;
        $mainEncoded = $mainConnection->encode($value);
        $testEncoded = $testConnection->encode($value);

        $this->assertEquals($mainEncoded, $testEncoded);
        $this->assertEquals($value, $mainConnection->decode($mainEncoded));
        $this->assertEquals($value, $testConnection->decode($testEncoded));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_invalid_connection(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Optimus connection [nonexistent] not configured.');

        $this->manager->connection('nonexistent');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_manager_magic_methods(): void
    {
        // Test that manager delegates to default connection
        $value = 98765;

        $encoded = $this->manager->encode($value);
        $decoded = $this->manager->decode($encoded);

        $this->assertEquals($value, $decoded);
        $this->assertNotEquals($value, $encoded);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_throws_if_no_connection_matches_size(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('No connection configured for 128-bit operations.');

        $app = new \Illuminate\Container\Container;

        $app['config'] = new \Illuminate\Config\Repository([
            'dual-optimus' => [
                'connections' => [
                    'main' => [
                        'prime' => 123,
                        'inverse' => 321,
                        'random' => 111,
                        'size' => 32,
                    ],
                ],
            ],
        ]);

        $manager = new \MostafaAminFlakes\DualOptimus\DualOptimusManager($app);

        $reflection = new \ReflectionClass($manager);
        $method = $reflection->getMethod('getConnectionBySize');
        $method->setAccessible(true);

        $method->invoke($manager, 128);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_provides_dual_optimus_services(): void
    {
        $provider = new DualOptimusServiceProvider(app());

        $this->assertEquals(
            ['dual-optimus', DualOptimusManager::class],
            $provider->provides()
        );
    }
}
