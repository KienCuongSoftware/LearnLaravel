<?php

namespace App\Http\Controllers\Admin;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function getUsers(): JsonResponse
    {
        $adminId = (int) Auth::id();
        if ($adminId <= 0) {
            return response()->json([]);
        }

        $userIds = Message::query()
            ->where(function ($query) use ($adminId): void {
                $query->where('receiver_id', $adminId)
                    ->orWhere('sender_id', $adminId);
            })
            ->orderByDesc('created_at')
            ->get(['sender_id', 'receiver_id'])
            ->map(function (Message $message) use ($adminId): int {
                return $message->sender_id === $adminId ? (int) $message->receiver_id : (int) $message->sender_id;
            })
            ->filter(fn (int $id): bool => $id !== $adminId)
            ->unique()
            ->values();

        if ($userIds->isEmpty()) {
            return response()->json([]);
        }

        $users = User::query()
            ->whereIn('id', $userIds->all())
            ->orderByRaw('FIELD(id, '.implode(',', $userIds->all()).')')
            ->get(['id', 'name'])
            ->map(function (User $user) use ($adminId) {
                $user->unread_count = Message::query()
                    ->where('sender_id', $user->id)
                    ->where('receiver_id', $adminId)
                    ->where('is_read', false)
                    ->count();

                return $user;
            });

        return response()->json($users);
    }

    public function getMessages(int $userId): JsonResponse
    {
        $adminId = (int) Auth::id();
        if ($adminId <= 0) {
            return response()->json([]);
        }

        Message::query()
            ->where('sender_id', $userId)
            ->where('receiver_id', $adminId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $messages = Message::query()
            ->with('sender:id,name')
            ->where(function ($query) use ($userId, $adminId): void {
                $query->where('sender_id', $userId)->where('receiver_id', $adminId);
            })
            ->orWhere(function ($query) use ($userId, $adminId): void {
                $query->where('sender_id', $adminId)->where('receiver_id', $userId);
            })
            ->orderBy('created_at')
            ->get();

        return response()->json($messages);
    }

    public function send(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'message' => ['required', 'string', 'max:2000'],
        ], [
            'user_id.required' => 'Thiếu người nhận.',
            'user_id.exists' => 'Người nhận không tồn tại.',
            'message.required' => 'Nội dung tin nhắn không được để trống.',
            'message.max' => 'Tin nhắn tối đa 2000 ký tự.',
        ]);

        $adminId = (int) Auth::id();
        $message = Message::query()->create([
            'sender_id' => $adminId,
            'receiver_id' => (int) $validated['user_id'],
            'content' => trim((string) $validated['message']),
            'is_read' => false,
        ]);

        $message->load('sender:id,name');
        event(new MessageSent($message));

        return response()->json($message);
    }

    public function unreadCount(): JsonResponse
    {
        $adminId = (int) Auth::id();
        if ($adminId <= 0) {
            return response()->json(['unread' => 0]);
        }

        $count = Message::query()
            ->where('receiver_id', $adminId)
            ->where('is_read', false)
            ->count();

        return response()->json(['unread' => (int) $count]);
    }
}
