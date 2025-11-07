<?php

namespace App\Traits;

use App\Models\Role;

trait HasRole
{
    /**
     * Check if user has a specific role.
     *
     * @param string|array $roles
     * @return bool
     */
    public function hasRole($roles): bool
    {
        if (is_array($roles)) {
            foreach ($roles as $role) {
                if ($this->checkRole($role)) {
                    return true;
                }
            }
            return false;
        }

        return $this->checkRole($roles);
    }

    /**
     * Check single role.
     *
     * @param string $role
     * @return bool
     */
    protected function checkRole(string $role): bool
    {
        return $this->role && $this->role->name === $role;
    }

    /**
     * Check if user has any of the given roles.
     *
     * @param array $roles
     * @return bool
     */
    public function hasAnyRole(array $roles): bool
    {
        return $this->hasRole($roles);
    }
}









