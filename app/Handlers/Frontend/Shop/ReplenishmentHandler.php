<?php
declare(strict_types = 1);

namespace app\Handlers\Frontend\Shop;

use app\Services\Auth\Auth;
use app\Services\Purchasing\ReplenishmentCreator;

class ReplenishmentHandler
{
    /**
     * @var Auth
     */
    private $auth;

    /**
     * @var ReplenishmentCreator
     */
    private $creator;

    public function __construct(Auth $auth, ReplenishmentCreator $creator)
    {
        $this->auth = $auth;
        $this->creator = $creator;
    }

    public function handle(float $sum, string $ip): int
    {
        $purchase = $this->creator->create($sum, $this->auth->getUser(), $ip);

        return $purchase->getId();
    }
}
