<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository extends BaseRepository
{
    public function __construct(User $model)
    {
        $this->model = $model;
    }

    /**
     * Get users by role.
     *
     * @param int $roleId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUsersByRole(int $roleId)
    {
        return $this->model->where('role_id', $roleId)->get();
    }

    /**
     * Get active users.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveUsers()
    {
        return $this->model->where('status', 'active')->get();
    }
}








