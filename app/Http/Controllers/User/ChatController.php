<?php

namespace App\Http\Controllers\User;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function send(Request $request): JsonResponse
    {
        /** @var User|null $sender */
        $sender = Auth::user();
        if (! $sender) {
            return response()->json(['error' => 'Bạn cần đăng nhập để gửi tin nhắn.'], 401);
        }

        $validated = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ], [
            'message.required' => 'Nội dung tin nhắn không được để trống.',
            'message.max' => 'Tin nhắn tối đa 2000 ký tự.',
        ]);

        $admin = User::query()->where('is_admin', true)->oldest('id')->first();
        if (! $admin) {
            return response()->json(['error' => 'Hiện chưa có admin để nhận tin nhắn.'], 422);
        }

        $message = Message::query()->create([
            'sender_id' => $sender->id,
            'receiver_id' => $admin->id,
            'content' => trim((string) $validated['message']),
            'is_read' => false,
        ]);

        $message->load('sender:id,name');
        event(new MessageSent($message));

        return response()->json($message);
    }

    public function getMessages(): JsonResponse
    {
        /** @var User|null $user */
        $user = Auth::user();
        if (! $user) {
            return response()->json(['error' => 'Bạn cần đăng nhập để xem hội thoại.'], 401);
        }

        $admin = User::query()->where('is_admin', true)->oldest('id')->first();
        if (! $admin) {
            return response()->json([]);
        }

        Message::query()
            ->where('sender_id', $admin->id)
            ->where('receiver_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $messages = Message::query()
            ->with(['sender:id,name', 'receiver:id,name'])
            ->where(function ($query) use ($user, $admin): void {
                $query->where('sender_id', $user->id)
                    ->where('receiver_id', $admin->id);
            })
            ->orWhere(function ($query) use ($user, $admin): void {
                $query->where('sender_id', $admin->id)
                    ->where('receiver_id', $user->id);
            })
            ->orderBy('created_at')
            ->get();

        return response()->json($messages);
    }

    public function unreadCount(): JsonResponse
    {
        /** @var User|null $user */
        $user = Auth::user();
        if (! $user) {
            return response()->json(['unread' => 0]);
        }

        $count = Message::query()
            ->where('receiver_id', $user->id)
            ->where('is_read', false)
            ->count();

        return response()->json(['unread' => (int) $count]);
    }
}
