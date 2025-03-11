<?php

namespace App\Repositories;
use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\Hash;
use App\Core\Constants;

class UserRepository implements UserRepositoryInterface
{
    public function getUsers($role)
    {
        $query = User::query();
    
        if(!is_null($role)) {
            $query->where('role', $role);
        }
        
        return $query->paginate(Constants::PAGINATION);
    }

    public function getUser($id)
    {
        return User::find($id);
    }

    public function createUser($data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'address' => $data['address'],
            'role' => $data['role'],
            'password' => Hash::make($data['password']),
        ]);

        return $user;
    }

    public function updateUser($data, $id)
    {
        $user = $this->getUser($id);

        if(!$user) {
            throw new Exception('User not found');
        }

        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'address' => $data['address'],
            'role' => $data['role'],
            'password' => Hash::make($data['password']),
        ]);

        return $user;
    }


    
}