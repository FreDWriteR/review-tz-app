<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Repository\CartManager;
use App\View\CartView;
use Illuminate\Support\Facades\Log;

class GetCartController
{
    public function __construct(
        private CartView    $cartView,
        private CartManager $cartManager,
    ) {}

    public function get(Request $request): JsonResponse
    {
        Log::channel('review-tz-app')->debug("Вход");
        $cart = $this->cartManager->getCart();

        if (! $cart) {
            return response()->json(
                ['message' => 'Cart not found'],
                404
            );
        }

        return response()->json(
            $this->cartView->toArray($cart)
        );
    }
}
