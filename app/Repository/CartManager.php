<?php

declare(strict_types = 1);

namespace App\Repository;

use App\Infrastructure\ConnectorException;
use Illuminate\Support\Facades\Log;
use Psr\Log\LoggerInterface;
use App\Domain\Cart;
use App\Infrastructure\ConnectorFacade;
use Illuminate\Support\Facades\Session;

class CartManager extends ConnectorFacade
{
    public function __construct(
        $host,
        $port,
        $password,
        private readonly LoggerInterface $logger
    )
    {
        parent::__construct($host, $port, $password, 1);
        parent::build();
    }

    /**
     * @inheritdoc
     */
    public function saveCart(Cart $cart): void
    {
        try {
            $this->connector->set(Session::getId(), $cart);
        } catch (ConnectorException $e) {
            $this->logger->error('Error', ['exception' => $e]);
        }
    }

    /**
     * @return ?Cart
     */
    public function getCart(): ?Cart
    {
        try {
            return $this->connector->get(Session::getId());
        } catch (ConnectorException $e) {
            $this->logger->error(
                'Ошибка получения корзины из Redis',
                ['session_id' => Session::getId(), 'exception' => $e]
            );

            // Явно возвращаем null, чтобы контроллер понял: это именно ошибка, а не пустая корзина
            return null;
        }
    }
}
