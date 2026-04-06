<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        // Ambil semua kategori
        $categories = Category::all();

        // Ambil beberapa produk featured (misal 10 produk terbaru)
        $products = Product::latest()->take(10)->get();

        // Kirim ke view
        return view('home', compact('categories', 'products'));
    }
}