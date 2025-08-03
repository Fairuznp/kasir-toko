<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    public function getAllUsers($search = null)
    {
        return User::orderBy('id')
            ->when($search, function ($q, $search) {
                return $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%");
            })
            ->paginate();
    }

    public function createUser(array $data)
    {
        return User::create($data);
    }

    public function updateUser($user, array $data)
    {
        return $user->update($data);
    }

    public function deleteUser($user)
    {
        return $user->delete();
    }

    public function getUserById($id)
    {
        return User::find($id);
    }
}
