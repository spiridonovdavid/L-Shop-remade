<?php
declare(strict_types = 1);

namespace app\Http\Controllers\Frontend\Profile;

use app\Handlers\Frontend\Profile\Purchases\PaginationHandler;
use app\Http\Controllers\Controller;
use app\Services\Auth\Permissions;
use app\Services\Response\JsonResponse;
use app\Services\Response\Status;
use Illuminate\Http\Request;
use function app\permission_middleware;

/**
 * Class PurchasesController
 * Processes requests from the user's purchase history page.
 */
class PurchasesController extends Controller
{
    public function __construct()
    {
        $this->middleware(permission_middleware(Permissions::PROFILE_PURCHASE_HISTORY_ACCESS));
    }

    /**
     * Returns data to paginate the data on the user's purchase history page.
     *
     * @param Request           $request
     * @param PaginationHandler $handler
     *
     * @return JsonResponse
     */
    public function pagination(Request $request, PaginationHandler $handler): JsonResponse
    {
        $page = is_numeric($request->get('page')) ? (int)$request->get('page') : 1;
        $orderBy = $request->get('order_by');
        $descending = (bool)$request->get('descending');

        $dto = $handler->handle($page, $orderBy, $descending);

        return new JsonResponse(Status::SUCCESS, [
            'canComplete' => $dto->canComplete(),
            'paginator' => $dto->getPaginator(),
            'items' => $dto->getItems()
        ]);
    }
}
