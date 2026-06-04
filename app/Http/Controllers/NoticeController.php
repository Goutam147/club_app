<?php

namespace App\Http\Controllers;

use App\Models\Notice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NoticeController extends Controller
{
    public function index()
    {
        $notices = Notice::with('creator')->orderBy('created_at', 'desc')->get();
        return view('notices.index', compact('notices'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'note' => 'nullable|string',
            'start_at' => 'nullable|date',
            'expiry_at' => 'nullable|date|after_or_equal:start_at',
            'status' => 'required|in:active,inactive',
        ]);

        Notice::create([
            'title' => $request->title,
            'description' => $request->description,
            'note' => $request->note,
            'start_at' => $request->start_at,
            'expiry_at' => $request->expiry_at,
            'status' => $request->status,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('notices.index')->with('success', 'Notice created successfully.');
    }

    public function update(Request $request, Notice $notice)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'note' => 'nullable|string',
            'start_at' => 'nullable|date',
            'expiry_at' => 'nullable|date|after_or_equal:start_at',
            'status' => 'required|in:active,inactive',
        ]);

        $notice->update([
            'title' => $request->title,
            'description' => $request->description,
            'note' => $request->note,
            'start_at' => $request->start_at,
            'expiry_at' => $request->expiry_at,
            'status' => $request->status,
        ]);

        return redirect()->route('notices.index')->with('success', 'Notice updated successfully.');
    }

    public function destroy(Notice $notice)
    {
        $notice->delete();
        return redirect()->route('notices.index')->with('success', 'Notice deleted successfully.');
    }
}
