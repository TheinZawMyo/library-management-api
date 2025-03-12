<?php

namespace App\Services;

use App\Repositories\Interfaces\CategoryRepositoryInterface;

class CategoryService
{
    public function __construct(private CategoryRepositoryInterface $categoryRepository)
    {}

    public function getCategories()
    {
        return $this->categoryRepository->all();
    }

    public function getCategory($id)
    {
        return $this->categoryRepository->find($id);
    }

    public function addCategory($data)
    {
        return $this->categoryRepository->create($data);
    }

    public function updateCategory($id, $data)
    {
        return $this->categoryRepository->update($id, $data);
    }

    public function deleteCategory($id)
    {
        return $this->categoryRepository->delete($id);
    }
}