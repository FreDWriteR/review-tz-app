<?php
declare(strict_types=1);

namespace App\Infrastructure\Contracts;

use App\Domain\Cart;

interface CartStorageInterface
{
    public function saveCart(Cart $cart): void;
    public function getCart(string $sessionId): ?Cart;
}
