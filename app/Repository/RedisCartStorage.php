<?php
declare(strict_types=1);

namespace App\Repository;

use App\Domain\Cart;
use App\Infrastructure\ConnectorFacade;
use App\Infrastructure\ConnectorException;
use App\Infrastructure\Contracts\CartStorageInterface;

class RedisCartStorage extends ConnectorFacade implements CartStorageInterface
{
    public function __construct(string $host, int $port, ?string $password, private readonly string $sessionId)
    {
        parent::__construct($host, $port, $password, 1);
        parent::build();
    }

    public function saveCart(Cart $cart): void
    {
        try {
            $this->connector->set($this->sessionId, $cart);
        } catch (ConnectorException $e) {
            throw $e;
        }
    }

    public function getCart(string $sessionId): ?Cart
    {
        try {
            return $this->connector->get($sessionId);
        } catch (ConnectorException $e) {
            throw $e;
        }
    }
}
