<?php

namespace App\Http\Controllers\Admin\Hr;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class CommunicationController extends Controller
{
    /**
     * Inbox
     */
    public function index()
    {
        $messages = Message::where('recipient_id', auth()->id())
            ->orWhere('recipient_role', auth()->user()->role)
            ->orWhere('recipient_role', 'all')
            ->with(['sender'])
            ->latest()
            ->paginate(20);

        return view('admin.hr.communication.index', compact('messages'));
    }

    public function create()
    {
        $users = User::all();
        // Get unique roles
        $roles = User::distinct('role')->pluck('role');

        return view('admin.hr.communication.create', compact('users', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'recipient_type' => 'required|in:individual,role',
            'recipient_id' => 'required_if:recipient_type,individual',
            'recipient_role' => 'required_if:recipient_type,role',
        ]);

        $recipientId = $request->recipient_type === 'individual' ? $request->recipient_id : null;
        $recipientRole = $request->recipient_type === 'role' ? $request->recipient_role : null;

        Message::create([
            'sender_id' => auth()->id(),
            'recipient_id' => $recipientId,
            'recipient_role' => $recipientRole,
            'subject' => $request->subject,
            'body' => $request->body,
            'is_read' => false
        ]);

        return redirect()->route('admin.hr.communication.index')->with('success', 'Message sent successfully.');
    }

    public function show(Message $message)
    {
        // Mark as read if I am the recipient
        if ($message->recipient_id == auth()->id() && !$message->is_read) {
            $message->update(['is_read' => true]);
        }

        return view('admin.hr.communication.show', compact('message'));
    }
}
