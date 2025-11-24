<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;


class StaffController extends Controller
{
    public function dashboard()
    {
        // ✅ No need to check here - middleware already verified staff_id exists
        $staffName = Session::get('staff_name');
        $staffEmail = Session::get('staff_email');

        return view('staff_dashboard', compact('staffName', 'staffEmail'));
    }

    public function logout(Request $request)
    {
        $request->session()->forget(['staff_id','staff_name','staff_email']);
        $request->session()->flush();

        return redirect('/login')->with('success', 'You have successfully logged out. ');
    }
}
