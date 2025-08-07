<?php

namespace MostafaAminFlakes\DualOptimus\Tests\Feature;

use MostafaAminFlakes\DualOptimus\Tests\TestCase;
use MostafaAminFlakes\DualOptimus\Facades\DualOptimus;

class FacadeTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function test_facade_encode_decode(): void
    {
        $value = 12345;
        
        $encoded = DualOptimus::encode($value);
        $decoded = DualOptimus::decode($encoded);
        
        $this->assertEquals($value, $decoded);
        $this->assertNotEquals($value, $encoded);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_facade_64bit_methods(): void
    {
        $value = 9876543210;
        
        $encoded = DualOptimus::encode64($value);
        $decoded = DualOptimus::decode64($encoded);
        
        $this->assertEquals($value, $decoded);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_facade_connection_methods(): void
    {
        $connections = DualOptimus::getConnections();
        $this->assertContains('main', $connections);
        
        $connection = DualOptimus::connection('main');
        $this->assertInstanceOf(\MostafaAminFlakes\DualOptimus\DualOptimus::class, $connection);
        
        $value = 12345;
        $encoded = $connection->encode($value);
        $decoded = $connection->decode($encoded);
        
        $this->assertEquals($value, $decoded);
    }
}