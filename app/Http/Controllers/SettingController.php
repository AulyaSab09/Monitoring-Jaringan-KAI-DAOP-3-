<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    /**
     * Display the settings page
     */
    public function index()
    {
        $settings = [
            'app_title' => AppSetting::get('app_title', 'Sistem Monitoring Jaringan'),
            'sound_connect' => AppSetting::get('sound_connect'),
            'sound_disconnect' => AppSetting::get('sound_disconnect'),
        ];

        return view('admin.settings', compact('settings'));
    }

    /**
     * Update the settings
     */
    public function update(Request $request)
    {
        $request->validate([
            'app_title' => 'required|string|max:255',
            'sound_connect' => 'nullable|file|mimes:mp3|max:5120', // max 5MB
            'sound_disconnect' => 'nullable|file|mimes:mp3|max:5120', // max 5MB
        ]);

        // Update app title
        AppSetting::set('app_title', $request->input('app_title'));

        // Handle sound_connect upload
        if ($request->hasFile('sound_connect')) {
            // Delete old file if exists
            $oldFile = AppSetting::get('sound_connect');
            if ($oldFile && Storage::disk('public')->exists($oldFile)) {
                Storage::disk('public')->delete($oldFile);
            }

            // Store new file
            $path = $request->file('sound_connect')->store('sounds', 'public');
            AppSetting::set('sound_connect', $path);
        }

        // Handle sound_disconnect upload
        if ($request->hasFile('sound_disconnect')) {
            // Delete old file if exists
            $oldFile = AppSetting::get('sound_disconnect');
            if ($oldFile && Storage::disk('public')->exists($oldFile)) {
                Storage::disk('public')->delete($oldFile);
            }

            // Store new file
            $path = $request->file('sound_disconnect')->store('sounds', 'public');
            AppSetting::set('sound_disconnect', $path);
        }

        return redirect()->route('admin.settings')->with('success', 'Pengaturan berhasil disimpan!');
    }
}
