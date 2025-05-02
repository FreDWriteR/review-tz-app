<?php
declare(strict_types=1);

namespace App\Repository;

use App\Domain\Cart;
use App\Domain\CartItem;
use App\Infrastructure\Contracts\CartStorageInterface;
use Psr\Log\LoggerInterface;
use Illuminate\Support\Facades\Session;
use Ramsey\Uuid\Uuid;
use Throwable;

class CartManager
{
    public function __construct(
        private CartStorageInterface $storage,
        private LoggerInterface      $logger
    ) {}

    public function addItem(string $productUuid, float $price, int $quantity): Cart
    {
        $sessionId = Session::getId();
        $cart = $this->storage->getCart($sessionId) ?? new Cart($sessionId);
        $cart->addItem(new CartItem(
            Uuid::uuid4()->toString(),
            $productUuid,
            $price,
            $quantity
        ));
        try {
            $this->storage->saveCart($cart);
        } catch (Throwable $e) {
            $this->logger->error('Cannot save cart', ['exception' => $e]);
            throw $e;
        }
        return $cart;
    }

    public function fetchCart(): ?Cart
    {
        $sessionId = Session::getId();
        return $this->storage->getCart($sessionId);
    }
}
