<?php
declare(strict_types = 1);

namespace app\Handlers\Frontend\Shop\Cart;

use app\DataTransferObjects\Frontend\Shop\Cart\Purchase as PurchaseDTO;
use app\DataTransferObjects\Frontend\Shop\Catalog\Purchase as ResultDTO;
use app\DataTransferObjects\Frontend\Shop\Purchase;
use app\Exceptions\Distributor\DistributionException;
use app\Exceptions\ForbiddenException;
use app\Exceptions\LogicException;
use app\Exceptions\Server\ServerNotFoundException;
use app\Repository\Server\ServerRepository;
use app\Services\Auth\Auth;
use app\Services\Auth\Permissions;
use app\Services\Cart\Cart;
use app\Services\Purchasing\PurchaseProcessor;
use app\Services\Server\ServerAccess;

class PurchaseHandler
{
    /**
     * @var Auth
     */
    private $auth;

    /**
     * @var Cart
     */
    private $cart;

    /**
     * @var ServerRepository
     */
    private $serverRepository;

    /**
     * @var PurchaseProcessor
     */
    private $processor;

    public function __construct(Auth $auth, Cart $cart, ServerRepository $serverRepository, PurchaseProcessor $processor)
    {
        $this->auth = $auth;
        $this->cart = $cart;
        $this->serverRepository = $serverRepository;
        $this->processor = $processor;
    }

    /**
     * @param PurchaseDTO $dto
     *
     * @return ResultDTO
     *
     * @throws LogicException
     * @throws ForbiddenException
     */
    public function handle(PurchaseDTO $dto): ResultDTO
    {
        $server = $this->serverRepository->find($dto->getServerId());
        if ($server === null) {
            throw ServerNotFoundException::byId($dto->getServerId());
        }

        $items = $this->cart->retrieveServer($server);
        // If cart is empty for this server.
        if (count($items) === 0) {
            throw new LogicException("Cart can not been empty");
        }

        /** @var Purchase[] $DTOs */
        $DTOs = [];
        foreach ($items as $fromServerCart) {
            foreach ($dto->getItems() as $fromClientCartProduct => $fromClientCartAmount) {
                if ($fromServerCart->getProduct()->getId() === $fromClientCartProduct) {
                    $DTOs[] = new Purchase($fromServerCart->getProduct(), $fromClientCartAmount);
                }
            }
        }

        foreach ($DTOs as $DTO) {
            $product = $DTO->getProduct();
            $server = $product->getCategory()->getServer();

            if (!ServerAccess::isUserHasAccessTo($this->auth->getUser(), $server)) {
                throw new ForbiddenException("Server {$server} is disabled and the user does not have permissions to make a purchase");
            }

            if ($product->isHidden() && !($this->auth->check() && $this->auth->getUser()->hasPermission(Permissions::ACCESS_TO_HIDDEN_PRODUCTS))) {
                throw new ForbiddenException("Product {$product} is hidden and the user does not have permissions to make a purchase");
            }
        }

        try {
            $result = $this->processor->process($DTOs, $dto->getUsername(), $dto->getIp());
        } catch (DistributionException $e) {
            // Remove all data in cart for this server if an distribution exception is thrown.
            // The cart can be cleaned because the purchase has already been made, although not distributed.
            $this->cart->removeServer($server);

            throw $e;
        }

        // Remove all data in cart for this server.
        $this->cart->removeServer($server);

        return $result;
    }
}
