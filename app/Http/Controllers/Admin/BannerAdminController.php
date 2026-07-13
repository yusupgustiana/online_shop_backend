<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;
use Illuminate\Support\Facades\Storage;


class BannerAdminController extends Controller
{
    public function index()
    {
        $banners = Banner::latest()->get();
        return view('admin.banners.index', compact('banners'));
    }

    public function create()
    {
       
        return view('admin.banners.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image',
        ]);

        $image = $request->file('image')->store('banners', 'public');

        Banner::create([
            'title' => $request->title,
            'description' => $request->description,
            'image' => $image,
            'link' => $request->link,
            'is_active' => $request->is_active ?? 0,
            'position' => $request->position ?? 0,
        ]);

        return redirect()->route('admin.banners.index')->with('success', 'Banner berhasil ditambahkan');
    }

    public function edit($id)
    {
        $banner = Banner::findOrFail($id);
        return view('admin.banners.edit', compact('banner'));
    }

   public function update(Request $request, $id)
{
    $banner = Banner::findOrFail($id);

    if ($request->hasFile('image')) {

        // hapus image lama (BENAR)
        if ($banner->image) {
            Storage::disk('public')->delete($banner->image);
        }

        // upload baru
        $image = $request->file('image')->store('banners', 'public');

    } else {
        $image = $banner->image;
    }

    $banner->update([
        'title' => $request->title,
        'description' => $request->description,
        'image' => $image,
        'link' => $request->link,
        'is_active' => $request->is_active ?? 0,
        'position' => $request->position ?? 0,
    ]);

    return redirect()->route('admin.banners.index')
        ->with('success', 'Banner berhasil diupdate');
}

public function destroy($id)
{
    $banner = Banner::findOrFail($id);

    // hapus file dari storage
    if ($banner->image) {
        Storage::disk('public')->delete($banner->image);
    }

    // hapus data database
    $banner->delete();

    return back()->with('success', 'Banner berhasil dihapus');
}
}
