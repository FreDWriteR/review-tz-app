<?php

declare(strict_types=1);

namespace App\View;

use App\Repository\ProductRepository;
use App\Models\Product;

readonly class ProductsView
{
    public function __construct(
        private ProductRepository $productRepository
    ) {
    }

    /**
     * @return array<int, array{id:int,uuid:string,category:string,description:string,thumbnail:string,price:float}>
     */
    public function toArray(string $category): array
    {
        return $this->productRepository
            ->getByCategory($category)
            ->map(fn(Product $product) => [
                'id' => $product->id,
                'uuid' => $product->uuid,
                'category' => $product->category,
                'description' => $product->description,
                'thumbnail' => $product->thumbnail,
                'price' => $product->price,
            ])
            ->toArray();
    }
}
