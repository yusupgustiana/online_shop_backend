<?php



namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * GET /api/notifications
     *
     * Mengambil semua notifikasi milik pengguna yang login.
     */
    public function index(Request $request): JsonResponse
    {
        return $this->notificationList($request, false);
    }

    /**
     * GET /api/notifications/unread
     *
     * Mengambil daftar notifikasi yang belum dibaca.
     */
    public function unread(Request $request): JsonResponse
    {
        return $this->notificationList($request, true);
    }

    /**
     * GET /api/notifications/unread-count
     *
     * Mengambil jumlah notifikasi yang belum dibaca.
     */
    public function unreadCount(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'message' => 'Jumlah notifikasi berhasil diambil',
            'unread_count' => $user->unreadNotifications()->count(),
        ]);
    }

    /**
     * PATCH /api/notifications/{id}/read
     *
     * Menandai satu notifikasi sebagai sudah dibaca.
     */
    public function markAsRead(
        Request $request,
        string $id
    ): JsonResponse {
        $user = $request->user();

        $notification = $user->notifications()
            ->whereKey($id)
            ->first();

        if (!$notification) {
            return response()->json([
                'message' => 'Notifikasi tidak ditemukan',
            ], 404);
        }

        if ($notification->read_at === null) {
            $notification->markAsRead();
        }

        return response()->json([
            'message' => 'Notifikasi berhasil ditandai sudah dibaca',
            'unread_count' => $user->unreadNotifications()->count(),
        ]);
    }

    /**
     * PATCH /api/notifications/read-all
     *
     * Menandai seluruh notifikasi sebagai sudah dibaca.
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        $user = $request->user();

        $updated = $user->unreadNotifications()
            ->update([
                'read_at' => now(),
            ]);

        return response()->json([
            'message' => 'Semua notifikasi berhasil ditandai sudah dibaca',
            'updated_count' => $updated,
            'unread_count' => $user->unreadNotifications()->count(),
        ]);
    }

    /**
     * DELETE /api/notifications/{id}
     *
     * Menghapus satu notifikasi milik pengguna yang login.
     */
    public function destroy(
        Request $request,
        string $id
    ): JsonResponse {
        $user = $request->user();

        $notification = $user->notifications()
            ->whereKey($id)
            ->first();

        if (!$notification) {
            return response()->json([
                'message' => 'Notifikasi tidak ditemukan',
            ], 404);
        }

        $notification->delete();

        return response()->json([
            'message' => 'Notifikasi berhasil dihapus',
            'unread_count' => $user->unreadNotifications()->count(),
        ]);
    }

    /**
     * Mengambil daftar notifikasi dengan pagination.
     *
     * $unreadOnly = false: semua notifikasi.
     * $unreadOnly = true : hanya notifikasi belum dibaca.
     */
    private function notificationList(
        Request $request,
        bool $unreadOnly
    ): JsonResponse {
        $validated = $request->validate([
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ]);

        $user = $request->user();
        $perPage = (int) ($validated['per_page'] ?? 20);

        $query = $unreadOnly
            ? $user->unreadNotifications()
            : $user->notifications();

        $notifications = $query
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return response()->json([
            'message' => $unreadOnly
                ? 'Notifikasi belum dibaca berhasil diambil'
                : 'Daftar notifikasi berhasil diambil',

            'unread_count' => $user->unreadNotifications()->count(),

            'notifications' => $notifications,
        ]);
    }
}
