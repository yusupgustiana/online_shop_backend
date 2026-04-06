<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RedirectController extends Controller
{
    public function index()
    {
        // Jika user belum login, redirect ke login
        if (!auth()->check()) {
            return redirect('/login');
        }

        // User sudah login, cek role
        $user = auth()->user();

        if ($user->roles === 'ADMIN') {
            return redirect('/admin/dashboard');
        }

        // Default untuk user biasa
        return redirect('/home');
    }
}