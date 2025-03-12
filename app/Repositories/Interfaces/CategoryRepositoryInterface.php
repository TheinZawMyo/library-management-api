<?php

namespace App\Repositories\Interfaces;

interface CategoryRepositoryInterface
{
    public function all();
    public function find($id);
    public function create($data);
    public function update($id, $data);
    public function delete($id);
}