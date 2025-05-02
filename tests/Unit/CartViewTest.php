<?php
declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Illuminate\Database\Capsule\Manager as Capsule;
use App\View\CartView;
use App\Repository\ProductRepository;
use App\Domain\Cart;
use App\Domain\CartItem;
use App\Domain\Customer;
use App\Models\Product;

class CartViewTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Поднимаем Eloquent на SQLite in-memory
        $capsule = new Capsule;
        $capsule->addConnection([
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
    }

    public function testToArrayComputesItemsAndTotal(): void
    {
        $customer = new Customer(1, 'Иван', 'Иванов', 'Петрович', 'a@b.c');
        $cart     = new Cart('uuid', $customer, 'card', [
            new CartItem('i1', 'p1', 10.0, 2),
            new CartItem('i2', 'p2', 5.0, 3),
        ]);

        $productRepo = $this->createMock(ProductRepository::class);
        $productRepo->method('getByUuid')
            ->willReturnMap([
                ['p1', (new Product)->forceFill([
                    'id'=>10,'uuid'=>'p1','name'=>'T','thumbnail'=>null,'price'=>10.0
                ])],
                ['p2', (new Product)->forceFill([
                    'id'=>20,'uuid'=>'p2','name'=>'U','thumbnail'=>null,'price'=>5.0
                ])],
            ]);

        $view = new CartView($productRepo);
        $out  = $view->toArray($cart);

        // Первая позиция: 10*2 = 20
        $this->assertSame(20.0, $out['items'][0]['itemTotal']);
        // Вторая: 5*3 = 15
        $this->assertSame(15.0, $out['items'][1]['itemTotal']);
        // Общий total = 35
        $this->assertSame(35.0, $out['total']);
        // customer.name корректно собран
        $this->assertStringContainsString('Иванов', $out['customer']['name']);
    }
}
