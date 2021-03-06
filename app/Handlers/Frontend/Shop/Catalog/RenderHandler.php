<?php
declare(strict_types=1);

namespace app\Handlers\Frontend\Shop\Catalog;

use app\DataTransferObjects\Frontend\Shop\Catalog\Category as CategoryDTO;
use app\DataTransferObjects\Frontend\Shop\Catalog\Product;
use app\DataTransferObjects\Frontend\Shop\Catalog\Result;
use app\DataTransferObjects\Frontend\Shop\Catalog\Server as ServerDTO;
use app\Entity\Category;
use app\Entity\Server;
use app\Exceptions\Category\CategoryNotFoundException;
use app\Exceptions\Category\CategoryNotFoundException as CategoryDoesNotExistException;
use app\Exceptions\Server\ServerNotFoundException;
use app\Exceptions\UnexpectedValueException;
use app\Repository\Product\ProductRepository;
use app\Repository\Server\ServerRepository;
use app\Services\Auth\Auth;
use app\Services\Auth\Permissions;
use app\Services\Cart\Cart;
use app\Services\Cart\Item;
use app\Services\Product\Order;
use app\Services\Server\Persistence\Persistence;
use app\Services\Settings\DataType;
use app\Services\Settings\Settings;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class RenderHandler
{
    /**
     * @var Auth
     */
    private $auth;

    /**
     * @var ServerRepository
     */
    private $serverRepository;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var Settings
     */
    private $settings;

    /**
     * @var Persistence
     */
    private $persistence;

    /**
     * @var Cart
     */
    private $cart;

    public function __construct(
        Auth $auth,
        ServerRepository $serverRepository,
        ProductRepository $productRepository,
        Settings $settings,
        Persistence $persistence,
        Cart $cart)
    {
        $this->auth = $auth;
        $this->serverRepository = $serverRepository;
        $this->productRepository = $productRepository;
        $this->settings = $settings;
        $this->persistence = $persistence;
        $this->cart = $cart;
    }

    /**
     * @param int $page
     * @param int $serverId
     * @param int $categoryId
     *
     * @return Result
     * @throws ServerNotFoundException
     * @throws CategoryDoesNotExistException
     */
    public function handle(int $page, int $serverId, ?int $categoryId): Result
    {
        $server = $this->checkServerAndCategory($serverId, $categoryId);

        $currentCategory = $server->getCategories()->get(0);

        if ($categoryId !== 0) {
            /** @var Category $category */
            foreach ($server->getCategories() as $category) {
                if ($category->getId() === $categoryId) {
                    $currentCategory = $category;

                    break;
                }
            }
        }

        if ($currentCategory === null) {
            return new Result(new ServerDTO($server), null, [], null);
        }

        $orderBy = $this->settings->get('system.catalog.pagination.order_by')->getValue();
        if (!in_array($orderBy, Order::availableFields())) {
            throw new UnexpectedValueException('$orderBy has invalid value `' . $orderBy . '`');
        }

        $paginator = $this->productRepository->findForCategoryPaginated(
            $currentCategory,
            $orderBy,
            $this->settings->get('system.catalog.pagination.descending')->getValue(DataType::BOOL),
            $page,
            $this->settings->get('system.catalog.pagination.per_page')->getValue(DataType::INT),
            $this->auth->check() && $this->auth->getUser()->hasPermission(Permissions::ACCESS_TO_HIDDEN_PRODUCTS)
        );
        $products = $this->fromPaginatorToDTO($paginator);

        return new Result(new ServerDTO($server), new CategoryDTO($currentCategory), $products, $paginator);
    }

    /**
     * @param int      $serverId
     * @param int|null $categoryId
     *
     * @return Server
     * @throws ServerNotFoundException
     * @throws CategoryNotFoundException
     */
    private function checkServerAndCategory(int $serverId, ?int $categoryId): Server
    {
        $server = $this->serverRepository->find($serverId);
        if ($server === null) {
            throw ServerNotFoundException::byId($serverId);
        }

        if ($categoryId === 0) {
            $this->persistence->persist($server);

            return $server;
        }

        if (count($server->getCategories()) === 0 && $categoryId !== null) {
            throw new CategoryNotFoundException();
        }

        /** @var Category $category */
        foreach ($server->getCategories() as $category) {
            if ($category->getId() === $categoryId) {
                $this->persistence->persist($server);

                return $server;
            }
        }

        throw CategoryNotFoundException::byId($categoryId);
    }

    private function fromPaginatorToDTO(LengthAwarePaginator $paginator): array
    {
        $products = $paginator->items();
        $result = [];
        foreach ($products as $product) {
            $result[] = new Product($product, $this->cart->exist(new Item($product, 0)));
        }

        return $result;
    }
}
