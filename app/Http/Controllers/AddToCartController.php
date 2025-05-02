<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use JsonException;
use App\Domain\Cart;
use App\Domain\CartItem;
use App\Repository\CartManager;
use App\Repository\ProductRepository;
use App\View\CartView;
use Ramsey\Uuid\Uuid;

class AddToCartController
{
    public function __construct(
        private ProductRepository $productRepository,
        private CartView         $cartView,
        private CartManager      $cartManager,
    ) {}

    public function post(Request $request): JsonResponse
    {
        try {
            $raw = json_decode(
                $request->getContent(),
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        } catch (JsonException $e) {
            return response()->json(
                ['error' => 'Invalid JSON'],
                400
            );
        }

        if (
            empty($raw['productUuid']) || ! is_string($raw['productUuid'])
            || ! isset($raw['quantity'])   || ! is_int($raw['quantity']) || $raw['quantity'] < 1
        ) {
            return response()->json(
                ['error' => 'productUuid and quantity are required'],
                400
            );
        }

        $product = $this->productRepository->getByUuid($raw['productUuid']);
        Log::channel('review-tz-app')->debug($product);
        $cart = $this->cartManager->getCart() ?? new Cart(Session::getId());
        $cart->addItem(new CartItem(
            Uuid::uuid4()->toString(),
            $product->uuid,
            $product->price,
            $raw['quantity'],
        ));
        $this->cartManager->saveCart($cart);
        return response()->json([
            'status' => 'success',
            'cart'   => $this->cartView->toArray($cart),
        ], 200);
    }
}
