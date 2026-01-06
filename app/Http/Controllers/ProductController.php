<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class ProductController extends Controller
{
    //index
    public function index()
    {
        $products = Product::with('category')->paginate(10);
        return view('pages.product.index', compact('products'));
    }

    //create
    public function create()
    {
        $categories = Category::all();
        return view('pages.product.create', compact('categories'));
    }
    //store image
    public function store(Request $request)
    {
        $data = $request->all();
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/products'), $filename);
            $data['image'] = 'uploads/products/' . $filename;
        }
        Product::create($data);
        return redirect()->route('product.index')->with('success', 'Product created successfully');
    }
}
