<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryUserController extends Controller
{
      public function index()
    {
        $categories = Category::limit(4)->get();

        return view('pages.user.dashboard', compact('categories'));
    }
}
