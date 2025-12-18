<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\StaffProfile;

class StaffProfileController extends Controller
{
    public function show(Request $request)
    {
        // ✅ No need to check - middleware already verified staff_id exists
        $staffId = session('staff_id');
        $profile = StaffProfile::where('staff_id', $staffId)->first();
        $staffName = session('staff_name');

        return view('profile', compact('profile', 'staffName'));
    }

    public function update(Request $request)
    {
       // ✅ No need to check - middleware already verified staff_id exists
       $staffId = session('staff_id');        $profile = StaffProfile::updateOrCreate(
            ['staff_id' => $staffId],
            $request->only(['full_name', 'email', 'phone_number', 'address', 'position', 'department'])
        );

        if ($request->hasFile('profile_image')) {
            $imagePath = $request->file('profile_image')->store('profiles', 'public');
            $profile->update(['profile_image' => $imagePath]);
        }

        return redirect()->back()->with('success', 'Profile updated successful!');
    }
}
