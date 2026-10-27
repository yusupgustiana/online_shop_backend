<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class FcmService
{
    public function send(
        string $token,
        string $title,
        string $body,
        array $data = []
    ): array {
  $credentials = config('services.firebase.credentials');

if (!$credentials || !file_exists($credentials)) {
    throw new \RuntimeException(
        'File Firebase service account tidak ditemukan: '
        . ($credentials ?? 'path belum dikonfigurasi')
    );
}

$messaging = (new Factory())
    ->withServiceAccount($credentials)
    ->createMessaging();

        // FCM mengharuskan nilai data berupa string.
        $data = array_map(
            fn ($value) => is_string($value)
                ? $value
                : json_encode($value, JSON_THROW_ON_ERROR),
            $data
        );

        // Buat pesan dengan API CloudMessage.
        $message = CloudMessage::new()
            ->toToken($token)
            ->withNotification(
                Notification::create($title, $body)
            )
            ->withData($data);

        $result = $messaging->send($message);

        return [
            'success' => true,
            'message_id' => $result,
        ];
    }

    public function sendToUser(
    \App\Models\User $user,
    string $title,
    string $body,
    array $data = []
): ?array {
    if (empty($user->fcm_token)) {
        \Illuminate\Support\Facades\Log::warning(
            'Notifikasi FCM tidak dikirim: token pengguna kosong.',
            ['user_id' => $user->id]
        );

        return null;
    }

    return $this->send(
        $user->fcm_token,
        $title,
        $body,
        $data
    );
}
}


