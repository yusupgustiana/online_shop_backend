<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
class userController extends Controller
{
    //index
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

    //create
    public function create()
    {

        return view('pages.user.create');     
    }

    //store
    public function store(Request $request)
    {
        $data = $request->all();
        $data['password'] = Hash::make($request->password);
        \App\Models\User::create($data);
        return redirect()->route('user.index')->with('success', 'User created successfully');
    }
    //edit
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('pages.user.edit-user', compact('user'));
    }
    //update
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $data = $request->all();
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        } else {
            unset($data['password']);
        }
        $user->update($data);
        return redirect()->route('user.index')->with('success', 'User updated successfully');
    }

    //destroy
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->route('user.index')->with('success', 'User deleted successfully');
    }

}
