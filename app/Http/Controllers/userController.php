<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // INDEX
    public function index(Request $request)
    {
        $users = DB::table('users');

        if ($request->has('search')) {
            $search = $request->input('search');
            $users = $users->where('name', 'like', '%' . $search . '%');
        }

        $users = $users->paginate(5);
        return view('pages.user.index', compact('users'));
    }

    // CREATE
    public function create()
    {
        return view('pages.user.create');
    }

    // STORE
    public function store(Request $request)
    {
        $data = $request->all();

        // HASH PASSWORD
        $data['password'] = Hash::make($request->password);

        // SET ROLE (default USER, simpan huruf besar)
        $data['roles'] = strtoupper($request->roles ?? 'USER');

        User::create($data);

        return redirect()->route('admin.user.index')->with('success', 'User created successfully');
    }

    // EDIT
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('pages.user.edit-user', compact('user'));
    }

    // UPDATE
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $data = $request->all();

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        } else {
            unset($data['password']);
        }

        // UPDATE ROLE (pastikan huruf besar)
        if ($request->filled('roles')) {
            $data['roles'] = strtoupper($request->roles);
        }

        $user->update($data);

        return redirect()->route('admin.user.index')->with('success', 'User updated successfully');
    }

    // DESTROY
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->route('admin.user.index')->with('success', 'User deleted successfully');
    }
}