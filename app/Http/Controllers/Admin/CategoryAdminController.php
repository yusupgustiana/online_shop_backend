<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryAdminController extends Controller
{

    // INDEX
    public function index()
    {
        $categories = Category::paginate(10);
        return view('admin.category.index', compact('categories'));
    }

    // CREATE
    public function create()
    {
        return view('pages.category.create');
    }

    // STORE
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time().'_'.$file->getClientOriginalName();
            $file->move(public_path('categories'), $filename);
            $data['image'] = 'categories/'.$filename;
        }

        Category::create($data);

        return redirect()
            ->route('admin.category.index')
            ->with('success', 'Category created successfully');
    }

    // EDIT
    public function edit(Category $category)
    {
        return view('admin.category.edit', compact('category'));
    }

    // UPDATE
    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {

            // hapus gambar lama (jika ada)
            if ($category->image && file_exists(public_path($category->image))) {
                unlink(public_path($category->image));
            }

            $file = $request->file('image');
            $filename = time().'_'.$file->getClientOriginalName();
            $file->move(public_path('categories'), $filename);
            $data['image'] = 'categories/'.$filename;
        }

        $category->update($data);

        return redirect()
            ->route('admin.category.index')
            ->with('success', 'Category updated successfully');
    }

    // DESTROY
    public function destroy(Category $category)
    {
        if ($category->image && file_exists(public_path($category->image))) {
            unlink(public_path($category->image));
        }

        $category->delete();

        return redirect()
            ->route('admin.category.index')
            ->with('success', 'Category deleted successfully');
    }
}
