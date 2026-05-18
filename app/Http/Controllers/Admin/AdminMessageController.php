<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class AdminMessageController extends Controller
{
    public function index()
    {
        $messages = ContactMessage::orderByDesc('created_at')->get();
        return view('admins.messages.index', compact('messages'));
    }

    public function show(ContactMessage $message)
    {
        return view('admins.messages.show', compact('message'));
    }

    public function destroy(ContactMessage $message)
    {
        $message->delete();
        return redirect()->route('admin.messages.index')->with('success', 'Message deleted successfully.');
    }

    public function markRead(ContactMessage $message)
    {
        $message->update(['is_read' => true]);
        return redirect()->back()->with('success', 'Message marked as read.');
    }

    public function markUnread(ContactMessage $message)
    {
        $message->update(['is_read' => false]);
        return redirect()->back()->with('success', 'Message marked as unread.');
    }
}
