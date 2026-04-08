<?php

// namespace App\Http\Controllers;

// use App\Models\Category;
// use Illuminate\Http\Request;

// class CategoryController extends Controller
// {
//     // INDEX
//     public function index()
//     {
//         $categories = Category::paginate(10);
//         return view('pages.category.index', compact('categories'));
//     }

//     // CREATE
//     public function create()
//     {
//         return view('pages.category.create');
//     }

//     // STORE
//     public function store(Request $request)
//     {
//         $data = $request->validate([
//             'name'        => 'required|string|max:255',
//             'description' => 'nullable|string',
//             'image'       => 'nullable|image|max:2048',
//         ]);

//         if ($request->hasFile('image')) {
//             $file = $request->file('image');
//             $filename = time().'_'.$file->getClientOriginalName();
//             $file->move(public_path('uploads/categories'), $filename);
//             $data['image'] = 'uploads/categories/'.$filename;
//         }

//         Category::create($data);

//         return redirect()
//             ->route('admin.category.index')
//             ->with('success', 'Category created successfully');
//     }

//     // EDIT
//     public function edit(Category $category)
//     {
//         return view('pages.admin.category.edit', compact('category'));
//     }

//     // UPDATE
//     public function update(Request $request, Category $category)
//     {
//         $data = $request->validate([
//             'name'        => 'required|string|max:255',
//             'description' => 'nullable|string',
//             'image'       => 'nullable|image|max:2048',
//         ]);

//         if ($request->hasFile('image')) {

//             // hapus gambar lama (jika ada)
//             if ($category->image && file_exists(public_path($category->image))) {
//                 unlink(public_path($category->image));
//             }

//             $file = $request->file('image');
//             $filename = time().'_'.$file->getClientOriginalName();
//             $file->move(public_path('uploads/categories'), $filename);
//             $data['image'] = 'uploads/categories/'.$filename;
//         }

//         $category->update($data);

//         return redirect()
//             ->route('admin.category.index')
//             ->with('success', 'Category updated successfully');
//     }

//     // DESTROY
//     public function destroy(Category $category)
//     {
//         if ($category->image && file_exists(public_path($category->image))) {
//             unlink(public_path($category->image));
//         }

//         $category->delete();

//         return redirect()
//             ->route('admin.category.index')
//             ->with('success', 'Category deleted successfully');
//     }
// }
