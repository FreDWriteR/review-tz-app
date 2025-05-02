<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\AddToCartRequest;
use Illuminate\Http\JsonResponse;
use App\Repository\CartManager;
use App\Repository\ProductRepository;
use App\View\CartView;

class AddToCartController
{
    public function __construct(
        private ProductRepository    $productRepository,
        private CartView             $cartView,
        private CartManager          $cartManager,
    ) {}

    public function post(AddToCartRequest $request): JsonResponse
    {
        $data = $request->validated();
        $cart = $this->cartManager->addItem(
            $data['productUuid'],
            $this->productRepository->getByUuid($data['productUuid'])->price,
            $data['quantity']
        );
        return response()->json([
            'status' => 'success',
            'cart'   => $this->cartView->toArray($cart),
        ]);
    }
}
