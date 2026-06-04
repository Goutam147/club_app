<?php

namespace App\Http\Controllers;

use App\Models\ClubMaster;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingController extends Controller
{
    public function index()
    {
        $club = ClubMaster::first();
        if (!$club) {
            $club = ClubMaster::create([
                'name' => 'Bhimchak Sunrise Club',
                'logo' => 'bsc_logo.jpeg',
            ]);
        }
        
        $maintenanceMode = Setting::where('key', 'maintenance_mode')->value('value') ?? '0';
        $maintenanceMessage = Setting::where('key', 'maintenance_message')->value('value') ?? '';

        return view('settings.index', compact('club', 'maintenanceMode', 'maintenanceMessage'));
    }

    public function updateClub(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'estd' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
        ]);

        $club = ClubMaster::first();

        $logoPath = $club->logo ?? 'bsc_logo.jpeg';
        if ($request->hasFile('logo')) {
            // Delete old custom logo if not default
            if ($club->logo && $club->logo !== 'bsc_logo.jpeg' && file_exists(public_path('uploads/logo/' . $club->logo))) {
                @unlink(public_path('uploads/logo/' . $club->logo));
            }
            
            $file = $request->file('logo');
            $fileName = 'logo_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/logo'), $fileName);
            $logoPath = 'uploads/logo/' . $fileName;
        }

        $club->update([
            'name' => $request->name,
            'address' => $request->address,
            'estd' => $request->estd,
            'logo' => $logoPath,
        ]);

        return redirect()->route('settings.index')->with('success', 'Club profile updated successfully.');
    }

    public function toggleMaintenance(Request $request)
    {
        $request->validate([
            'maintenance_mode' => 'required|in:0,1',
            'maintenance_message' => 'nullable|string',
        ]);

        Setting::updateOrCreate(
            ['key' => 'maintenance_mode'],
            [
                'value' => $request->maintenance_mode,
                'updated_by' => Auth::id(),
            ]
        );

        Setting::updateOrCreate(
            ['key' => 'maintenance_message'],
            [
                'value' => $request->maintenance_message ?? 'Site is currently undergoing scheduled maintenance. Please check back later.',
                'updated_by' => Auth::id(),
            ]
        );

        $status = $request->maintenance_mode === '1' ? 'enabled (lockdown active)' : 'disabled (site live)';
        return redirect()->route('settings.index')->with('success', "Maintenance mode {$status} successfully.");
    }
}
