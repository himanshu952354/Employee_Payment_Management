<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * Display the unified company broadcast room.
     */
    public function index(Request $request)
    {
        $currentUser = Auth::user();

        // Fetch all group broadcast messages for this company
        $messages = Message::where('is_broadcast', true)
            ->whereHas('sender', function ($query) use ($currentUser) {
                $query->where('company_name', $currentUser->company_name);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        // Count total broadcast messages to help JS update the unread indicators (excluding self-sent)
        $totalBroadcastCount = Message::where('is_broadcast', true)
            ->where('sender_id', '!=', $currentUser->id)
            ->whereHas('sender', function ($query) use ($currentUser) {
                $query->where('company_name', $currentUser->company_name);
            })
            ->count();

        return view('chat.index', compact('messages', 'totalBroadcastCount'));
    }

    /**
     * Send a secure broadcast message inside the unified company channel (Admin only).
     */
    public function store(Request $request)
    {
        $currentUser = Auth::user();

        // Security check: Only admins can post broadcast announcements
        if ($currentUser->role !== 'admin') {
            abort(403, 'Unauthorized communication channel access. Only workspace directors can post broadcasts.');
        }

        $request->validate([
            'message' => 'required|string|max:5000',
        ]);

        // Resolve company admin for database constraint satisfaction in broadcast messages
        $admin = User::where('company_name', $currentUser->company_name)
            ->where('role', 'admin')
            ->first();
        $adminId = $admin ? $admin->id : $currentUser->id;

        // Group Broadcast Message
        Message::create([
            'sender_id' => $currentUser->id,
            'receiver_id' => $adminId, // satisfy FK constraint
            'message' => $request->message,
            'is_read' => false,
            'is_broadcast' => true,
        ]);

        return redirect()->route('chat.index');
    }
}
