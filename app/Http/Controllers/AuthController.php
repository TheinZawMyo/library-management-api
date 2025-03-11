<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;


class AuthController extends Controller
{
    public function __construct(private AuthService $authService) {}

    //============= LOGIN ==============//
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $response = $this->authService->login($request);

        if($response['status'] == 401) {
            return response()->json($response, 401);
        }

        return response()->json($response, 200);
    }


    //============= REGISTER ==============//
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required',
            'address' => 'required',
            'password' => 'required|min:6'
        ]);

        $response = $this->authService->register($request);

        if($response['status'] == 500) {
            return response()->json($response, 500);
        }

        return response()->json($response, 201);
    }

    //============= LOGOUT ==============//
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Logged out'
        ], 200);
    }
}
