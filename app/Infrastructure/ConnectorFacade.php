<?php

declare(strict_types=1);

namespace App\Infrastructure;

use Predis\Client;
use Predis\Connection\ConnectionException;

class ConnectorFacade
{
    public string $host;
    public int $port = 6379;
    public ?string $password = null;
    public ?int $dbindex = null;

    protected ?Connector $connector = null;

    public function __construct(string $host, int $port = 6379, ?string $password = null, ?int $dbindex = null)
    {
        $this->host     = $host;
        $this->port     = $port;
        $this->password = $password;
        $this->dbindex  = $dbindex;
    }

    protected function build(): void
    {
        $redis = new Client([
            'scheme'   => 'tcp',
            'host'     => $this->host,
            'port'     => $this->port,
            'password' => $this->password,
            'database' => $this->dbindex,
        ]);

        try {
            $isConnected = $redis->isConnected();
            if (! $isConnected && $redis->ping('Pong')) {
                $redis->connect();
            }
        } catch (ConnectionException $e) {
            throw new ConnectorException(
                'Redis connection failed: ' . $e->getMessage(),
                (int)$e->getCode(),
                $e
            );
        }

        $this->connector = new Connector($redis);
    }

    /**
     * Проверяет, что коннектор собран и Redis отвечает.
     */
    public function isAlive(): bool
    {
        return $this->connector !== null
            && $this->connector->has('any_health_check_key');
    }
}
