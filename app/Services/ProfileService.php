<?php

namespace App\Services;

class ProfileService
{
    public function updateProfile($user, array $data)
    {
        if ($data['password_baru']) {
            $data['password'] = bcrypt($data['password_baru']);
            $user->update($data);
        } else {
            $user->update(collect($data)->only(['nama', 'username'])->toArray());
        }

        return $user;
    }
}
