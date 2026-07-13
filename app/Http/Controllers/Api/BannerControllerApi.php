<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
class BannerControllerApi extends Controller
{
    // LIST BANNER
    public function index()
    {
        $banners = Banner::where('is_active', 1)
            ->orderBy('position', 'asc')
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Success',
            'data' => $banners
        ]);
    }

    // DETAIL BANNER
    public function show($id)
    {
        $banner = Banner::find($id);

        if (!$banner) {
            return response()->json([
                'status' => false,
                'message' => 'Banner not found',
                'data' => null
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Success',
            'data' => $banner
        ]);
    }
}