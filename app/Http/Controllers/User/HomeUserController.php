<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\Banner;

class HomeUserController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->paginate(10);
        $categories = Category::limit(4)->get();
        $banners = Banner::where('is_active', 1)
            ->orderBy('position', 'asc')
            ->get();

        return view('user.dashboard', compact(
            'products',
            'categories',
            'banners'
        ));
    }
}