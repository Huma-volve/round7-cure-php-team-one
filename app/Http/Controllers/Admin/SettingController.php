<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SettingRequest;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
      public function index()
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        return view('admin.settings.index', compact('settings'));
    }

    public function update(SettingRequest $request)
    {
        // dd($request->all());
        try{
           $data = $request->validated();

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        foreach ($data as $key => $value) {
            Setting::setValue($key, $value);
        }

        return redirect()->back()->with('success', 'Settings updated successfully');
        
        }catch(\Exception $e){
            return redirect()->back()->with('error', 'There was an error uploading the logo: ' . $e->getMessage());
        }

    }
}
