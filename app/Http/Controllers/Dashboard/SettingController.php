<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    //   public function index()
    // {
    //     $settings = Setting::pluck('value', 'key')->toArray();
    //     return view('admin.settings.index', compact('settings'));
    // }

    // public function update(Request $request)
    // {
    //     foreach ($request->except('_token') as $key => $value) {
    //         Setting::updateOrCreate(['key' => $key], ['value' => $value]);
    //     }

    //     return back()->with('success', 'Settings updated successfully.');
    // }
}
