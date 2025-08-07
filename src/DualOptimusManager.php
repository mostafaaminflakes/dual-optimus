<?php

namespace MostafaAminFlakes\DualOptimus;

use InvalidArgumentException;
use Illuminate\Support\Manager;

class DualOptimusManager extends Manager
{
    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver(): string
    {
        return $this->config->get('dual-optimus.default', 'main');
    }

    /**
     * Create a driver instance for the given connection name.
     *
     * @param string $name
     * @return \MostafaAminFlakes\DualOptimus\DualOptimus
     *
     * @throws \InvalidArgumentException
     */
    protected function createDriver($name): DualOptimus
    {
        if (!$this->config->get("dual-optimus.connections.{$name}")) {
            throw new InvalidArgumentException("Optimus connection [{$name}] not configured.");
        }

        // Get both 32-bit and 64-bit configurations
        $config32 = $this->getConnectionBySize(32);
        $config64 = $this->getConnectionBySize(64);

        return new DualOptimus($config32, $config64);
    }

    /**
     * Get the configuration for a connection of a specific size (32 or 64).
     *
     * @param int $size
     * @return array
     *
     * @throws InvalidArgumentException
     */
    protected function getConnectionBySize(int $size): array
    {
        $connections = $this->config->get('dual-optimus.connections');

        foreach ($connections as $name => $config) {
            if (isset($config['size']) && $config['size'] === $size) {
                return $config;
            }
        }

        throw new InvalidArgumentException("No connection configured for {$size}-bit operations.");
    }

    /**
     * Get the names of all configured connections.
     *
     * @return array<int, string>
     */
    public function getConnections(): array
    {
        return array_keys($this->config->get('dual-optimus.connections', []));
    }

    /**
     * Get a driver instance by connection name.
     *
     * @param string|null $name
     * @return \MostafaAminFlakes\DualOptimus\DualOptimus
     */
    public function connection(string $name = null): DualOptimus
    {
        return $this->driver($name);
    }

    /**
     * Proxy dynamic method calls to the default driver.
     *
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->driver()->$method(...$parameters);
    }
}