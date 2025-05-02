<?php
declare(strict_types=1);

namespace Tests\Unit;

use Exception;
use PHPUnit\Framework\TestCase;
use App\Repository\CartManager;
use App\Domain\Cart;
use App\Infrastructure\Connector;
use App\Infrastructure\ConnectorException;
use Psr\Log\LoggerInterface;
use ReflectionProperty;

class CartManagerTest extends TestCase
{
    private CartManager $manager;
    private $connectorMock;
    private $loggerMock;

    protected function setUp(): void
    {
        // Мокаем Connector и Logger
        $this->connectorMock = $this->getMockBuilder(Connector::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['set', 'get', 'has'])
            ->getMock();

        $this->loggerMock = $this->createMock(LoggerInterface::class);

        // Создаём "полуручный" экземпляр CartManager без вызова build()
        $this->manager = $this->getMockBuilder(CartManager::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();

        // Вкладываем мок-connector в свойство parent::$connector
        $ref = new ReflectionProperty(CartManager::class, 'connector');
        $ref->setValue($this->manager, $this->connectorMock);

        // Вкладываем мок-logger
        $refLog = new ReflectionProperty(CartManager::class, 'logger');
        $refLog->setValue($this->manager, $this->loggerMock);
    }

    public function testSaveCartSuccess(): void
    {
        $cart = new Cart('abc');

        // ожидаем, что connector->set будет вызван один раз
        $this->connectorMock
            ->expects($this->once())
            ->method('set')
            ->with(session_id(), $cart);

        $this->manager->saveCart($cart);
        // никаких исключений не должно быть
        $this->assertTrue(true);
    }

    public function testSaveCartThrowsConnectorExceptionAndLogs(): void
    {
        $cart = new Cart('abc');
        $ex   = new ConnectorException('error');

        $this->connectorMock
            ->expects($this->once())
            ->method('set')
            ->willThrowException($ex);

        // logger->error должен быть вызван
        $this->loggerMock
            ->expects($this->once())
            ->method('error')
            ->with('Error', ['exception' => $ex]);


        $this->manager->saveCart($cart);
        $this->assertTrue(true);
    }

    public function testGetCartReturnsCart(): void
    {
        $expected = new Cart('abc');

        $this->connectorMock
            ->expects($this->once())
            ->method('get')
            ->with(session_id())
            ->willReturn($expected);

        $cart = $this->manager->getCart();
        $this->assertSame($expected, $cart);
    }

    public function testGetCartOnExceptionLogsAndReturnsNull(): void
    {
        $ex = new ConnectorException('error');

        $this->connectorMock
            ->expects($this->once())
            ->method('get')
            ->willThrowException($ex);

        $this->loggerMock
            ->expects($this->once())
            ->method('error')
            ->with('Ошибка получения корзины из Redis', ['session_id' => session_id(), 'exception' => $ex]);

        $cart = $this->manager->getCart();
        $this->assertNull($cart);
    }
}
