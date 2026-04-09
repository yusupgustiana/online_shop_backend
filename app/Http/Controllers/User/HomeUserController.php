<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;



class HomeUserController extends Controller
{

    public function index()
    {
        $products   = Product::with('category')->paginate(10);
        $categories = Category::limit(4)->get();

        return view('user.home.dashboard', compact('products', 'categories'));
    }
   //home product
       public function productAll()
    {
        $products = Product::with('category')->paginate(10);
        return view('user.home.dashboard', compact('products'));
    }

          public function category()
    {
        $categories = Category::limit(4)->get();

        return view('user.home.dashboard', compact('categories'));
    }
}
