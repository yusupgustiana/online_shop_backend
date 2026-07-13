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
    $products = Product::query()
        ->join('categories', 'products.category_id', '=', 'categories.id')
        ->select(
            'products.*',
            'categories.name as category_name'
        )
        ->when($request->filled('search'), function ($query) use ($request) {
            $query->where('products.name', 'like', '%' . $request->search . '%');
        })
        ->when($request->filled('category_id'), function ($query) use ($request) {
            $query->where('products.category_id', $request->category_id);
        })
        ->where('products.is_available', true)
        ->orderBy('products.name')
        ->paginate($request->integer('per_page', 10));

    return response()->json([
        'success' => true,
        'message' => 'Success',
        'data' => $products,
    ]);
}
   public function show($id)
{
    $product = Product::with('category')->find($id);

    if (!$product) {
        return response()->json([
            'success' => false,
            'message' => 'Product not found',
            'data' => null,
        ], 404);
    }

    return response()->json([
        'success' => true,
        'message' => 'Success',
        'data' => $product,
    ]);
}
    
}