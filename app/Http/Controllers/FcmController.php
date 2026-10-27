<?php

namespace App\Http\Controllers;

use App\Models\FcmToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FcmController extends Controller
{
    public function registerToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_m_customer' => ['required', 'string', 'max:100'],
            'fcm_token' => ['required', 'string', 'max:5000'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Data token tidak valid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        FcmToken::updateOrCreate(
            ['fcm_token' => $request->fcm_token],
            ['id_m_customer' => $request->id_m_customer]
        );

        return response()->json([
            'status' => true,
            'message' => 'Token FCM berhasil didaftarkan.',
        ]);
    }
}
