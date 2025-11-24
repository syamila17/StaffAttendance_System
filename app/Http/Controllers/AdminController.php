<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AdminController extends Controller
{
    public function showLoginForm()
    {
        return view('admin_login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $admin = Admin::where('admin_email', $request->email)->first();
        
        if (!$admin) {
            return back()->withInput()->withErrors(['email' => 'Admin email not found.']);
        }

        if (!Hash::check($request->password, $admin->admin_password)) {
            return back()->withInput()->withErrors(['password' => 'Incorrect password.']);
        }

        Session::put('admin_id', $admin->admin_id);
        Session::put('admin_name', $admin->admin_name);
        Session::put('admin_email', $admin->admin_email);
        session()->regenerate();

        return redirect()->route('admin.dashboard')->with('success', 'Welcome Admin!');
    }

    public function dashboard()
    {
        // ✅ No need to check here - middleware already verified admin_id exists
        return view('admin_dashboard' , [
            'admin_name' => Session::get('admin_name'),
            'admin_email' => Session::get('admin_email')
        ]);
    }

    public function logout()
    {
        session()->forget(['admin_id', 'admin_name', 'admin_email']);
        session()->flush();
        
        return redirect('/admin_login')->with('success', 'You have logged out successfully.');
    }
}
