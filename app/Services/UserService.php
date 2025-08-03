<?php

namespace App\Services;

use App\Repositories\UserRepository;

class UserService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getAllUsers($search = null)
    {
        $users = $this->userRepository->getAllUsers($search);

        if ($search) {
            $users->appends(['search' => $search]);
        }

        return $users;
    }

    public function createUser(array $data)
    {
        $data['password'] = bcrypt($data['password']);
        return $this->userRepository->createUser($data);
    }

    public function updateUser($user, array $data)
    {
        if (isset($data['password_baru']) && $data['password_baru']) {
            $data['password'] = bcrypt($data['password_baru']);
            return $this->userRepository->updateUser($user, $data);
        } else {
            return $this->userRepository->updateUser(
                $user,
                array_intersect_key($data, array_flip(['nama', 'username', 'role']))
            );
        }
    }

    public function deleteUser($user)
    {
        return $this->userRepository->deleteUser($user);
    }
}
