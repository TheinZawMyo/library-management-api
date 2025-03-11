<?php

namespace App\Repositories\Interfaces;

interface UserRepositoryInterface
{
    public function getUsers($role);
    public function getUser($id);
    public function createUser($data);
    public function updateUser($data, $id);
    
}