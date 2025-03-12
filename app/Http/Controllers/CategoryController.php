<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CategoryService;

class CategoryController extends Controller
{
    public function __construct(private CategoryService $categoryService)
    {}

    public function index()
    {
        return response()->json($this->categoryService->getCategories(), 200);
    }


    public function show($id)
    {
        return response()->json($this->categoryService->getCategory($id), 200);
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string'
        ]);

        try {
            $result = $this->categoryService->addCategory($request->all());

            return response()->json([
                'status' => 201,
                'message' => 'Category created successfully',
            ], 201);
        } catch(Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string'
        ]);

        try {
            $result = $this->categoryService->updateCategory($id, $request->all());
    
            return response()->json([
                'status' => 200,
                'message' => 'Category updated successfully',
            ], 200);
        }catch(Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage()
            ], 500);
        }

    }

    public function delete($id)
    {
        $result = $this->categoryService->deleteCategory($id);
        return response()->json([
            'status' => 200,
            'message' => 'Category deleted successfully',
        ], 200);
    }


}
