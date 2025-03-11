<?php

namespace App\Repositories\Interfaces;
use App\Models\User;

interface AuthRepositoryInterface
{
    public function createUser($request): User;
}