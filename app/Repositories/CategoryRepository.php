<?php

namespace App\Repositories;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use App\Models\Category;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function all()
    {
        return Category::all();
    }

    public function find($id)
    {
        return Category::find($id);
    }

    public function create($data)
    {
        return Category::create([
            'name' => $data['name'],
            'description' => $data['description']
        ]);
    }

    public function update($id, $data)  
    {
        $category = Category::find($id);
        if(!$category) {
            return false;
        }
        return $category->update([
            'name' => $data['name'],
            'description' => $data['description']
        ]);
    }

    public function delete($id)
    {
        return Category::destroy($id);
    }
}