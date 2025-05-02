<?php
declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Facades\Log;
use Tests\TestCase;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AddToCartTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // прогоняем миграции
        $this->artisan('migrate', ['--database' => 'sqlite'])->run();
    }

    public function test_add_to_cart_success(): void
    {
        $product = Product::factory()->create();
        $payload = [
            'productUuid' => $product->uuid,
            'quantity'    => 2,
        ];

        $response = $this->postJson('/api/cart/items', $payload);
        $response->assertStatus(200)
            ->assertJsonFragment(['status' => 'success'])
            ->assertJsonStructure([
                'status',
                'cart' => ['uuid', 'items', 'total']
            ]);
    }

    public function test_add_to_cart_invalid_json(): void
    {
        $response = $this->post('/api/cart/items', [], ['CONTENT_TYPE' => 'application/json']);
        $response->assertStatus(400)
            ->assertExactJson(['error' => 'Invalid JSON']);
    }

    public function test_add_to_cart_missing_fields(): void
    {
        $response = $this->postJson('/api/cart/items', []);
        $response->assertStatus(400)
            ->assertExactJson(['error' => 'productUuid and quantity are required']);
    }
}
