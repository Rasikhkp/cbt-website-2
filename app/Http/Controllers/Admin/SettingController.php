<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $registrationEnabled = Setting::where('key', 'registration_enabled')->value('value') === '1';
        return view('admin.settings.index', compact('registrationEnabled'));
    }

    public function update(Request $request)
    {
        $enabled = $request->has('registration_enabled') ? '1' : '0';
        
        Setting::updateOrCreate(
            ['key' => 'registration_enabled'],
            ['value' => $enabled]
        );

        return redirect()->back()->with('success', 'Settings updated successfully.');
    }
}
