<?php

namespace App\Repositories;
use App\Repositories\Interfaces\AuthRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthRepository implements AuthRepositoryInterface
{
    public function createUser($request): User
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'password' => Hash::make($request->password),
        ]);

        return $user;
    }
}