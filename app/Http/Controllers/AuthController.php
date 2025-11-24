<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Staff;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller {
    //  Show the login page
    public function showLoginForm()
    {
        return view('login');
    }

    // Process login form when submitted
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Find staff by email
        $staff = Staff::where('staff_email', $request->email)->first();

        if (!$staff) {
            return back()->withInput()->withErrors(['email' => 'Staff email not found in database.']);
        }

        if (!Hash::check($request->password, $staff->staff_password)) {
            return back()->withInput()->withErrors(['password' => 'Incorrect password.']);
        }

        // Password is correct, create session
        session()->put('staff_id', $staff->staff_id);
        session()->put('staff_name', $staff->staff_name);
        session()->put('staff_email', $staff->staff_email);
        session()->regenerate();

        return redirect()->route('staff.dashboard')->with('success', 'Login successful!');
    }
    // logout
    public function logout(Request $request)
    {
        session()->forget(['staff_id', 'staff_name', 'staff_email']);
        session()->flush();
        
        return redirect('/login')->with('success', 'You have logged out successfully.');
    }
}
