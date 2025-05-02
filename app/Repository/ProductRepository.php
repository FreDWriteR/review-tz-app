<?php

declare(strict_types=1);

namespace App\Repository;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProductRepository
{
    /**
     * @throws ModelNotFoundException
     */
    public function getByUuid(string $uuid): Product
    {
        return Product::where('uuid', $uuid)
            ->firstOrFail();
    }

    /**
     * @return Collection<Product>
     */
    public function getByCategory(string $category): Collection
    {
        return Product::query()
            ->where('is_active', true)
            ->where('category', $category)
            ->get();
    }
}
