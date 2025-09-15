<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;


class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::first();
        return response()->json([
            'setup_completed' => $settings?->setup_complete ?? false,
            'settings' => $settings
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'school_name' => 'required|string|max:255',
            'school_logo' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        $data = $request->only('school_name');
        if ($request->hasFile('school_logo')) {
            $data['school_logo'] = $request->file('school_logo')->store('logos', 'public');
        }
        $data['setup_complete'] = true;

        $settings = Setting::updateOrCreate(['id' => 1], $data);

        return response()->json([
            'message' => 'Settings saved successfully',
            'settings' => $settings
        ]);
    }
}
