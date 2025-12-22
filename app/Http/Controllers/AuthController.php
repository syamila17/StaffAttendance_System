<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Staff;
use App\Models\StaffSession;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class AuthController extends Controller {
    //  Show the login page
    public function showLoginForm(Request $request)
    {
        // Handle language switching
        if ($request->has('lang')) {
            $lang = $request->query('lang');
            if (in_array($lang, ['en', 'ms'])) {
                app()->setLocale($lang);
                session(['locale' => $lang]);
            }
        } else if (session()->has('locale')) {
            app()->setLocale(session('locale'));
        }

        return view('login');
    }

    // Process login form when submitted - STAFF ONLY authentication
    // Accepts staff_id (st001 format) or email address
    public function login(Request $request)
    {
        // Validate input - accept either staff_id (lowercase st format) or email
        $request->validate([
            'login_credential' => [
                'required',
                'string',
                'max:255',
            ],
            'password' => [
                'required',
                'string',
                'min:3',
            ]
        ], [
            'login_credential.required' => 'Staff ID or Email is required.',
            'login_credential.max' => 'Staff ID or Email is too long.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 3 characters.',
        ]);

        // Get the input and prepare for search
        $loginInput = trim($request->input('login_credential'));
        $password = $request->input('password');

        // Debug: Log the attempt (STAFF LOGIN ONLY)
        \Log::info('STAFF LOGIN ATTEMPT with credential: ' . $loginInput);

        /**
         * Search strategy for STAFF-ONLY authentication:
         * 1. If input contains @ symbol, treat as email (case-insensitive)
         * 2. If input looks like staff_id, treat as staff_id (lowercase st format: st001, st002, etc.)
         * 3. Validate format and search database
         */
        $staff = null;
        
        // Check if input looks like an email
        if (filter_var($loginInput, FILTER_VALIDATE_EMAIL)) {
            // Search by email (case-insensitive)
            $staff = Staff::where('staff_email', strtolower($loginInput))->first();
            
            if (!$staff) {
                \Log::warning('STAFF LOGIN: Email not found: ' . $loginInput);
                return back()
                    ->withInput($request->only('login_credential'))
                    ->withErrors(['login_credential' => 'Email not found. This is staff login only.']);
            }
        } else {
            // Treat as staff_id - normalize to lowercase for consistency (st001, st002, etc.)
            $normalizedStaffId = strtolower($loginInput);
            
            // Validate staff_id format: must start with 'st' and contain alphanumeric
            if (!preg_match('/^st[a-z0-9]+$/i', $normalizedStaffId)) {
                // If not valid format, try as email anyway
                $staff = Staff::where('staff_email', strtolower($loginInput))->first();
                
                if (!$staff) {
                    \Log::warning('STAFF LOGIN: Invalid credential format: ' . $loginInput);
                    return back()
                        ->withInput($request->only('login_credential'))
                        ->withErrors(['login_credential' => 'Please enter a valid Staff ID or email.S']);
                }
            } else {
                // Search by staff_id with normalized format
                $staff = Staff::where('staff_id', $normalizedStaffId)->first();
                
                if (!$staff) {
                    \Log::warning('STAFF LOGIN: Staff ID not found: ' . $normalizedStaffId);
                    return back()
                        ->withInput($request->only('login_credential'))
                        ->withErrors(['login_credential' => 'Staff ID not found in the system. Please check and try again.']);
                }
            }
        }

        // At this point, staff exists - verify password
        if (!$staff->staff_password) {
            \Log::warning('STAFF LOGIN: Staff has no password set: ' . $staff->staff_id);
            return back()
                ->withInput($request->only('login_credential'))
                ->withErrors(['password' => 'Your account has not been properly configured. Please contact administration.']);
        }

        // Verify password - supports both bcrypt and plain text hashes
        if (!password_verify($password, $staff->staff_password)) {
            \Log::warning('STAFF LOGIN: Incorrect password for Staff ID: ' . $staff->staff_id);
            return back()
                ->withInput($request->only('login_credential'))
                ->withErrors(['password' => 'Incorrect password. Please try again.']);
        }

        // ✅ Password is correct - create session (STAFF ONLY)
        // Ensure session is started
        if (!session()->isStarted()) {
            session()->start();
        }
        
        // Store the session data with actual database values
        session()->put('staff_id', $staff->staff_id);
        session()->put('staff_name', $staff->staff_name ?? 'Staff Member');
        session()->put('staff_email', $staff->staff_email ?? '');
        session()->put('login_time', now()->timestamp);

        // Log successful STAFF login
        $authMethod = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'staff_id';
        \Log::info('STAFF LOGIN SUCCESS: Staff ID ' . $staff->staff_id . ' logged in via ' . $authMethod);
        
        // Track this session in database for multiple login support
        $sessionId = session()->getId();
        
        // Delete any existing session with this sessionId first (from previous user)
        try {
            StaffSession::where('session_id', $sessionId)->delete();
        } catch (\Exception $e) {
            \Log::warning('Error deleting old session: ' . $e->getMessage());
        }
        
        // Create new session record
        try {
            StaffSession::create([
                'staff_id' => $staff->staff_id,
                'session_id' => $sessionId,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'logged_in_at' => Carbon::now(),
                'last_activity_at' => Carbon::now(),
            ]);
        } catch (\Exception $e) {
            // Log error but don't fail login - session still works
            \Log::warning('StaffSession creation failed: ' . $e->getMessage());
        }
        
        // Save session to storage
        session()->save();

        return redirect()->route('staff.dashboard')->with('success', 'Login successful! Welcome ' . $staff->staff_name);
    }

    // Logout functionality
    public function logout(Request $request)
    {
        $sessionId = session()->getId();
        $staffId = session()->get('staff_id');
        
        // Remove session tracking
        try {
            StaffSession::where('session_id', $sessionId)->delete();
        } catch (\Exception $e) {
            \Log::warning('Error deleting session on logout: ' . $e->getMessage());
        }
        
        // Forget session data
        session()->forget(['staff_id', 'staff_name', 'staff_email']);
        session()->flush();

        \Log::info('Logout for Staff ID: ' . $staffId);
        
        return redirect('/login')->with('success', 'You have logged out successfully.');
    }
}
