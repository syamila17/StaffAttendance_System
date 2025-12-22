<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\StaffProfile;
use App\Models\Staff;
use App\Models\Department;
use App\Models\Team;

class StaffProfileController extends Controller
{
    public function show(Request $request)
    {
        // ✅ Get staff_id from session (verified by middleware)
        $staffId = session('staff_id');
        
        // Get staff record with relationships
        $staff = Staff::with('department', 'team')->find($staffId);
        
        // Get or create profile - auto-fill from staff table on first access
        $profile = StaffProfile::where('staff_id', $staffId)->first();
        
        // If profile doesn't exist, create it with initial data from staff table
        if (!$profile) {
            // Explicitly instantiate and set staff_id (bypasses mass assignment guarding)
            $profile = new StaffProfile();
            $profile->staff_id = $staffId;  // Explicitly set before other attributes
            $profile->full_name = $staff->staff_name ?? '';
            $profile->email = $staff->staff_email ?? '';
            $profile->phone_number = '';
            $profile->address = '';
            $profile->position = '';
            $profile->department = $staff->department?->department_name ?? '';
            $profile->save();  // Save to database
        }
        
        $staffName = session('staff_name');
        $team = $staff->team;
        $department = $staff->department;

        return view('profile', compact('profile', 'staffName', 'staffId', 'staff', 'team', 'department'));
    }

    public function update(Request $request)
    {
        // ✅ Get staff_id from session (verified by middleware)
        $staffId = session('staff_id');
        
        // ✅ SECURITY: Staff ID is immutable and cannot be modified under any circumstances
        // 1. Get the staff_id from session (server-side authority)
        // 2. Extract ONLY allowed fields from the request
        // 3. Explicitly exclude staff_id from all update operations
        
        // Get only the editable fields - staff_id is deliberately excluded
        $editableData = $request->only([
            'full_name', 
            'email', 
            'phone_number', 
            'address', 
            'position', 
            'department'
        ]);
        
        // Additional security: Ensure staff_id is NOT in the request data and cannot be modified
        // This prevents any attempt to tamper with staff_id even if passed in the request
        $editableData = array_diff_key($editableData, array_flip(['staff_id']));
        
        // Get existing profile or create new one with staff_id from session
        $profile = StaffProfile::where('staff_id', $staffId)->first();
        
        if (!$profile) {
            // Create new profile if it doesn't exist
            $profile = new StaffProfile();
            $profile->staff_id = $staffId;  // Explicitly set staff_id (bypasses mass assignment guarding)
        }
        
        // Update only the editable fields
        $profile->fill($editableData)->save();

        // Handle profile image if uploaded
        if ($request->hasFile('profile_image')) {
            $imagePath = $request->file('profile_image')->store('profiles', 'public');
            $profile->update(['profile_image' => $imagePath]);
        }

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }
}
