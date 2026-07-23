<?php

namespace App\Http\Controllers;

use App\Models\FeeType;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeeTypeController extends Controller
{
    /**
     * Display a listing of fee heads / categories.
     */
    public function index()
    {
        $feeTypes = FeeType::with(['event', 'creator'])->orderBy('id', 'desc')->get();
        $events = Event::where('status', 'active')->orderBy('title', 'asc')->get();

        return view('fees.index', compact('feeTypes', 'events'));
    }

    /**
     * Store a newly created fee head.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:monthly,event,general',
            'default_amount' => 'required|numeric|min:0',
            'due_date' => 'nullable|date',
            'event_id' => 'nullable|exists:events,id',
        ]);

        FeeType::create([
            'title' => $request->title,
            'type' => $request->type,
            'default_amount' => $request->default_amount,
            'due_date' => $request->due_date,
            'event_id' => $request->event_id,
            'status' => 'active',
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('fees.index')->with('success', 'Fee category created successfully.');
    }

    /**
     * Update the specified fee head.
     */
    public function update(Request $request, FeeType $feeType)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:monthly,event,general',
            'default_amount' => 'required|numeric|min:0',
            'due_date' => 'nullable|date',
            'event_id' => 'nullable|exists:events,id',
            'status' => 'required|in:active,inactive',
        ]);

        $feeType->update([
            'title' => $request->title,
            'type' => $request->type,
            'default_amount' => $request->default_amount,
            'due_date' => $request->due_date,
            'event_id' => $request->event_id,
            'status' => $request->status,
        ]);

        return redirect()->route('fees.index')->with('success', 'Fee category updated successfully.');
    }

    /**
     * Remove or deactivate the specified fee head.
     */
    public function destroy(FeeType $feeType)
    {
        if ($feeType->items()->exists()) {
            $feeType->update(['status' => 'inactive']);
            return redirect()->route('fees.index')->with('success', 'Fee category deactivated because it has recorded transactions.');
        }

        $feeType->delete();
        return redirect()->route('fees.index')->with('success', 'Fee category deleted successfully.');
    }
}
