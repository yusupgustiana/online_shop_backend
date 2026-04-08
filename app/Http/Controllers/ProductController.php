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
        return redirect()->route('admin.product.index')->with('success', 'Product created successfully');
    }
        public function edit(Product $product)
    {
        $categories = Category::all();
        return view('pages.product.edit', compact('product', 'categories'));
    }
    public function update(Request $request, Product $product)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'price' => 'required|numeric',
        'stock' => 'required|integer',
        'category_id' => 'required|exists:categories,id',
        'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
    ]);

    $data = $request->all();

    // jika upload gambar baru
    if ($request->hasFile('image')) {
        // hapus gambar lama (opsional tapi recommended)
        if ($product->image && file_exists(public_path($product->image))) {
            unlink(public_path($product->image));
        }

        $file = $request->file('image');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('uploads/products'), $filename);
        $data['image'] = 'uploads/products/' . $filename;
    }

    $product->update($data);

    return redirect()->route('admin.product.index')
        ->with('success', 'Product updated successfully');
}
}
