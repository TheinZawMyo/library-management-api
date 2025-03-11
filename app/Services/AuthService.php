<?php

namespace App\Services;
use App\Repositories\Interfaces\AuthRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Exception;

class AuthService
{
    public function __construct(private AuthRepositoryInterface $authRepository) {}
    

    public function login($request) : array
    {

        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return [
                'status' => 401,
                'message' => 'Unauthorized'
            ];
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'status' => 200,
            'token' => $token,
            'user' => $user,
            'message' => 'Login Success!'
        ];

    }

    public function register($request) : array
    {
        try {
            $user = $this->authRepository->createUser($request);

            $token = $user->createToken('auth_token')->plainTextToken;

            return [
                'status' => 201,
                'user' => $user,
                'message' => 'User created successfully!',
                'token' => $token
            ];
        } catch (Exception $e) {
            return [
                'status' => 500,
                'message' => $e->getMessage()
            ];
        }

    }

}