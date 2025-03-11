<?php

namespace App\Services;
use App\Repositories\UserRepository;

class UserService
{
    public function __construct(private UserRepository $userRepository) {}

    public function getUsers($role) {
        return $this->userRepository->getUsers($role);
    }

    public function getUser($id) {
        return $this->userRepository->getUser($id);
    }

    public function createUser($data) {
        return $this->userRepository->createUser($data);
    }

    public function updateUser($data, $id) {
        return $this->userRepository->updateUser($data, $id);
    }
}