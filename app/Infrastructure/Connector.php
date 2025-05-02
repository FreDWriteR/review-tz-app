<?php

declare(strict_types=1);

namespace App\Infrastructure;

use App\Domain\Cart;
use Predis\Client;
use Predis\PredisException;

class Connector
{
    private Client $redis;

    public function __construct(Client $redis)
    {
        $this->redis = $redis;
    }

    /**
     * @throws ConnectorException
     */
    public function get(string $key): ?Cart
    {
        try {
            $cart = @unserialize($this->redis->get($key) ?? '');

            return $cart instanceof Cart
                ? $cart
                : null;
        } catch (PredisException $e) {
            throw new ConnectorException('Connector error', (int)$e->getCode(), $e);
        }
    }

    /**
     * @throws ConnectorException
     */
    public function set(string $key, Cart $value): void
    {
        try {
            // TTL 24 часа
            $this->redis->setex($key, 24 * 60 * 60, serialize($value));
        } catch (PredisException $e) {
            throw new ConnectorException('Connector error', (int) $e->getCode(), $e);
        }
    }

    public function has($key): bool
    {
        try {
            return (bool) $this->redis->exists($key);
        } catch (PredisException $e) {
            // В случае проблем с соединением считаем, что ключа нет
            return false;
        }
    }
}
