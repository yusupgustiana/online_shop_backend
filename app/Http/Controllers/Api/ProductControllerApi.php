<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Validator;

class ProductControllerApi extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');

        // search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'LIKE', "%{$search}%");
        }

        // filter category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // only available product
        $query->where('is_available', 1)
              ->orderBy('name', 'asc');

        $products = $query->paginate($request->get('per_page', 10));

        return response()->json([
            'message' => 'Success',
            'data' => $products
        ]);
    }

    public function show($id)
    {
        $product = Product::with('category')->find($id);

        if (!$product) {
            return response()->json([
                'message' => 'Product not found',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'message' => 'Success',
            'data' => $product
        ]);
    }
}