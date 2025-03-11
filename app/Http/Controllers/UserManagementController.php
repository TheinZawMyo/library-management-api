<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Exception;

class UserManagementController extends Controller
{
    public function __construct(private UserService $userService)
    {}

    //========= get librarians =====//
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'role' => 'string|in:librarian,members,admin'
        ]);
        try {
            $role = $request->role ?? null;
            $librarians = $this->userService->getUsers($role);

            return response()->json($librarians, 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    //========= get user by id =====//
    public function show($id): JsonResponse
    {
        try {
            $user = $this->userService->getUser($id);

            return response()->json($user, 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    //======== create user ======//
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required',
            'address' => 'required',
            'role' => 'required|string|in:member,librarian,admin',
            'password' => 'required|min:6'
        ]);

        try {
            $user = $this->userService->createUser($request->all());

            return response()->json([
                'status' => 201,
                'message' => 'User created successfully',
                'user' => $user
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    //======== update user ======//
    public function update(Request $request, $id): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'required',
            'address' => 'required',
            'role' => 'required|string|in:member,librarian,admin',
            'password' => 'required|min:6'
        ]);

        try {
            $user = $this->userService->updateUser($request->all(), $id);

            return response()->json([
                'status' => 200,
                'message' => 'User updated successfully',
                'user' => $user
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    //======== delete user ======//
    public function destroy($id): JsonResponse
    {
        $user = $this->userService->getUser($id);

        if (!$user) {
            return response()->json([
                'status' => 404,
                'message' => 'User not found'
            ], 404);
        }

        $user->delete();

        return response()->json([
            'status' => 200,
            'message' => 'User deleted successfully'
        ]);
    }
}
