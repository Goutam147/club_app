<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GalleryController extends Controller
{
    public function index()
    {
        $galleries = Gallery::with(['event', 'creator'])->orderBy('created_at', 'desc')->get();
        $events = Event::where('status', 'active')->orderBy('title', 'asc')->get();
        return view('gallery.index', compact('galleries', 'events'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'event_id' => 'nullable|exists:events,id',
            'document' => 'required|file|image|max:4096', // 4MB image max
            'description' => 'nullable|string',
        ]);

        $docPath = null;
        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $fileName = 'gallery_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/gallery'), $fileName);
            $docPath = 'uploads/gallery/' . $fileName;
        }

        Gallery::create([
            'title' => $request->title,
            'event_id' => $request->event_id,
            'doc_url' => $docPath,
            'description' => $request->description,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('gallery.index')->with('success', 'Gallery item uploaded successfully.');
    }

    public function destroy(Gallery $gallery)
    {
        // Optional: delete physical file from public folder
        if ($gallery->doc_url && file_exists(public_path($gallery->doc_url))) {
            @unlink(public_path($gallery->doc_url));
        }

        $gallery->delete();
        return redirect()->route('gallery.index')->with('success', 'Gallery item deleted successfully.');
    }
}
