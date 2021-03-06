<?php
declare(strict_types=1);

namespace app\DataTransferObjects\Commands\User\Roles;

use app\Entity\Role;
use app\Entity\User;

class RolesList
{
    /**
     * @var Role[]
     */
    private $roles;

    /**
     * @var User
     */
    private $user;

    /**
     * RoleList constructor.
     *
     * @param Role[] $roles
     * @param User   $user
     */
    public function __construct(array $roles, User $user)
    {
        $this->roles = $roles;
        $this->user = $user;
    }

    /**
     * @return Role[]
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }
}
