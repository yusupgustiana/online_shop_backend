<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\FcmService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;

class PushNotificationController extends Controller
{
    public function send(Request $request, FcmService $fcm)
    {
        $validator = Validator::make($request->all(), [
            'token' => ['required', 'string'],
            'title' => ['required', 'string', 'max:100'],
            'body' => ['required', 'string', 'max:1000'],
            'data' => ['nullable', 'array'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $result = $fcm->send(
                $request->token,
                $request->title,
                $request->body,
                $request->input('data', [])
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Notifikasi berhasil dikirim ke FCM',
                'data' => $result,
            ]);
        }  catch (Throwable $e) {
    \Illuminate\Support\Facades\Log::error(
        'FCM gagal mengirim notifikasi',
        [
            'exception' => get_class($e),
            'message' => $e->getMessage(),
        ]
    );

    return response()->json([
        'status' => 'error',
        'message' => 'Gagal mengirim notifikasi',
    ], 500);
}
    }
}
