<?php

declare(strict_types = 1);

namespace App\View;

use App\Domain\Cart;
use App\Repository\ProductRepository;
use Illuminate\Support\Facades\Log;

readonly class CartView
{
    public function __construct(
        private ProductRepository $productRepository
    ) {
    }

    public function toArray(Cart $cart): array
    {
        $data = [
            'uuid' => $cart->getUuid(),
            // если покупателя нет, сразу убираем ключ customer
        ];

        // заполняем customer, только если он есть
        $customer = $cart->getCustomer();
        if ($customer !== null) {
            $data['customer'] = [
                'id'    => $customer->getId(),
                'name'  => implode(' ', [
                    $customer->getLastName(),
                    $customer->getFirstName(),
                    $customer->getMiddleName(),
                ]),
                'email' => $customer->getEmail(),
            ];
        }

        $data['payment_method'] = $cart->getPaymentMethod();

        $total = 0;
        $data['items'] = [];
        $uuid = $cart->getUuid();
        Log::channel('review-tz-app')->debug($uuid);
        $last_uuid = last($cart->getItems())->getProductUuid();
        Log::channel('review-tz-app')->debug("Последний: $last_uuid");
        foreach ($cart->getItems() as $item) {
            $itemTotal = $item->getPrice() * $item->getQuantity();
            $price = $item->getPrice();
            $total    += $itemTotal;
            $quantity  = $item->getQuantity();
            $uuid      = $item->getUuid();
            $product_uuid = $item->getProductUuid();
            Log::channel('review-tz-app')->debug("$total \n $quantity \n $price \n элемент корзины: $uuid \n продукт: $product_uuid");

            $product = $this->productRepository->getByUuid($item->getProductUuid());

            $data['items'][] = [
                'uuid'      => $item->getUuid(),
                'price'     => $item->getPrice(),
                'quantity'  => $item->getQuantity(),
                'itemTotal' => $itemTotal,
                'product'   => [
                    'id'        => $product->id,
                    'uuid'      => $product->uuid,
                    'name'      => $product->name,
                    'thumbnail' => $product->thumbnail,
                    'price'     => $product->price,
                ],
            ];
        }

        $data['total'] = $total;

        return $data;
    }
}
