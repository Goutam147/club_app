<?php

namespace App\Http\Controllers;

use App\Models\Notice;
use App\Models\Event;
use App\Models\Gallery;
use App\Models\ClubMaster;
use App\Models\Setting;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Show the public welcome/landing page.
     */
    public function welcome()
    {
        $club = ClubMaster::first();
        $notices = Notice::where('status', 'active')->orderBy('created_at', 'desc')->take(5)->get();
        $events = Event::where('status', 'active')->orderBy('start_date', 'asc')->take(5)->get();
        $galleries = Gallery::orderBy('created_at', 'desc')->take(8)->get();

        return view('welcome', compact('club', 'notices', 'events', 'galleries'));
    }

    /**
     * Show the authenticated user dashboard.
     */
    public function index()
    {
        $club = ClubMaster::first();
        $notices = Notice::where('status', 'active')->orderBy('created_at', 'desc')->get();
        $events = Event::where('status', 'active')->orderBy('start_date', 'asc')->get();
        $galleries = Gallery::orderBy('created_at', 'desc')->get();

        return view('dashboard', compact('club', 'notices', 'events', 'galleries'));
    }
}
