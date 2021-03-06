<?php
declare(strict_types = 1);

namespace app\Services\Auth\Acl;

use Doctrine\Common\Collections\Collection;

/**
 * Interface HasPermissions
 * Represents entities that can have permissions.
 */
interface HasPermissions
{
    /**
     * Checks for the presence of a user's permissions.
     * Consider both the user's personal permissions and the permissions related to the
     * roles that the user has.
     *
     * @param string|PermissionInterface $permission Permission name or permission object.
     *
     * @return bool True - user has a permission.
     */
    public function hasPermission($permission): bool;

    /**
     * Checks if the user has all of the specified permissions.
     * Consider both the user's personal permissions and the permissions related to the
     * roles that the user has.
     *
     * @param string[]|PermissionInterface[] $permissions Array with permission names or
     *                                                    permission objects.
     *
     * @return bool True - the user has all roles.
     */
    public function hasAllPermissions(array $permissions): bool;

    /**
     * Checks if the user has at least one of the transferred roles.
     * Consider both the user's personal permissions and the permissions related to the
     * roles that the user has.
     *
     * @param string[]|PermissionInterface[] $permissions Array with permission names or
     *                                                    permission objects.
     *
     * @return bool True - the user has at least one permission.
     */
    public function hasAtLeastOnePermission(array $permissions): bool;

    /**
     * Returns the permissions that belong to an entity.
     *
     * @return Collection
     */
    public function getPermissions(): Collection;
}
