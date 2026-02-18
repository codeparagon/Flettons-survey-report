<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Pagination\LengthAwarePaginator;

class UserService
{
    protected UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Get all users with pagination.
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllUsers(int $perPage = 15): LengthAwarePaginator
    {
        return $this->userRepository->paginate($perPage);
    }

    /**
     * Create a new user.
     *
     * @param array $data
     * @return User
     */
    public function createUser(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        return $this->userRepository->create($data);
    }

    /**
     * Update user.
     *
     * @param int $id
     * @param array $data
     * @return User
     */
    public function updateUser(int $id, array $data): User
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        return $this->userRepository->update($id, $data);
    }

    /**
     * Delete user.
     *
     * @param int $id
     * @return bool
     */
    public function deleteUser(int $id): bool
    {
        return $this->userRepository->delete($id);
    }

    /**
     * Find user by ID.
     *
     * @param int $id
     * @return User|null
     */
    public function findUser(int $id): ?User
    {
        return $this->userRepository->find($id);
    }

    /**
     * Activate a user.
     *
     * @param int $id
     * @return User
     */
    public function activateUser(int $id): User
    {
        return $this->userRepository->update($id, ['status' => 'active']);
    }

    /**
     * Deactivate a user.
     *
     * @param int $id
     * @return User
     */
    public function deactivateUser(int $id): User
    {
        return $this->userRepository->update($id, ['status' => 'inactive']);
    }

    /**
     * Reset user password.
     *
     * @param int $id
     * @param string $password
     * @return User
     */
    public function resetUserPassword(int $id, string $password): User
    {
        return $this->userRepository->update($id, ['password' => Hash::make($password)]);
    }
}


