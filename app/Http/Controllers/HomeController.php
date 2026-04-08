<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
public function index()
{
    $role = strtoupper(auth()->user()->roles);

    if ($role === 'ADMIN') {
        return redirect()->route('admin.dashboard');
    }

    return redirect()->route('user.dashboard');
}
}