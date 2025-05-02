<?php

declare(strict_types = 1);

namespace App\Domain;

final class Cart
{
    public function __construct(
        private string $uuid,
        private ?Customer $customer = null,
        private string $paymentMethod = '',
        private array $items = [],
    ) {
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function getPaymentMethod(): string
    {
        return $this->paymentMethod;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function addItem(CartItem $item): void
    {
        $this->items[] = $item;
    }
}
