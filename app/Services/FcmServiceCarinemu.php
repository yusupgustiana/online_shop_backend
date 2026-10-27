<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class FcmServiceCarinemu
{
    public function sendToToken(
        string $project,
        string $token,
        string $title,
        string $body,
        array $data = []
    ): array {
        $configKey = match ($project) {
            'carinemu' => 'firebase_carinemu',
            'project2' => 'firebase_project2',
            default => throw new \InvalidArgumentException(
                'Project Firebase tidak dikenal.'
            ),
        };

        $credentials = config("$configKey.credentials");

        $messaging = (new Factory())
            ->withServiceAccount($credentials)
            ->createMessaging();

        $data = array_map(
            fn ($value) => is_scalar($value)
                ? (string) $value
                : json_encode($value),
            $data
        );

        $message = CloudMessage::withTarget('token', $token)
            ->withNotification(
                Notification::create($title, $body)
            )
            ->withData($data);

        $messageId = $messaging->send($message);

        return [
            'success' => true,
            'message_id' => $messageId,
        ];
    }
}
