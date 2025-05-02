<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use JsonException;
use App\View\ProductsView;

class GetProductsController
{
    public function __construct(
        private ProductsView $productsView
    ) {}

    public function get(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $raw = json_decode(
                $request->getContent(),
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        } catch (JsonException) {
            return response()->json(
                ['error' => 'Invalid JSON'],
                400
            );
        }

        if (empty($raw['category']) || ! is_string($raw['category'])) {
            return response()->json(
                ['error' => 'category is required'],
                400
            );
        }

        $products = $this->productsView->toArray($raw['category']);

        return response()->json(
            $products,
            200
        );
    }
}
