<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;

class CategoryControllerApi extends Controller
{
    // GET /api/categories
    public function index()
    {
        $categories = Category::select('id', 'name', 'description', 'image')
            ->orderBy('id', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'List categories',
            'data' => $categories
        ]);
    }
}
