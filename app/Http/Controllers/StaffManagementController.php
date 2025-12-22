<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Staff;
use App\Models\Department;
use App\Models\Team;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class StaffManagementController extends Controller
{
    // Display all staff members
    public function index(Request $request)
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

        if (!Session::has('admin_id')) {
            return redirect()->route('admin.login');
        }

        $query = Staff::with('department', 'team');
        
        // Handle search
        $search = request('search');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereRaw("LOWER(staff_name) LIKE ?", ['%' . strtolower($search) . '%'])
                  ->orWhereRaw("LOWER(staff_email) LIKE ?", ['%' . strtolower($search) . '%']);
            });
        }
        
        // Sort by staff name alphabetically
        $query->orderBy('staff_name', 'asc');
        
        $staff = $query->paginate(10);
        
        return view('admin.staff_management', [
            'staff' => $staff,
        ]);
    }

    // Show create form
    public function create()
    {
        if (!Session::has('admin_id')) {
            return redirect()->route('admin.login');
        }

        $departments = Department::where('status', 'active')->get();
        $teams = Team::where('status', 'active')->get();

        return view('admin.staff_create', [
            'departments' => $departments,
            'teams' => $teams
        ]);
    }

    // Store new staff member
    public function store(Request $request)
    {
        if (!Session::has('admin_id')) {
            return redirect()->route('admin.login');
        }

        $request->validate([
            'staff_name' => 'required|string|max:255',
            'staff_email' => 'required|email|unique:staff,staff_email',
            'staff_password' => 'required|string|min:6|confirmed',
            'department_id' => 'nullable|exists:departments,department_id',
            'team_id' => 'nullable|exists:teams,team_id'
        ]);

        $staffId = Staff::generateStaffId();
        
        Staff::create([
            'staff_id' => $staffId,
            'staff_name' => $request->staff_name,
            'staff_email' => $request->staff_email,
            'staff_password' => Hash::make($request->staff_password),
            'department_id' => $request->department_id,
            'team_id' => $request->team_id,
            'created_at' => now()
        ]);

        return redirect()->route('admin.staff.index')->with('success', 'Staff member added successfully!');
    }

    // Show edit form
    public function edit($id)
    {
        if (!Session::has('admin_id')) {
            return redirect()->route('admin.login');
        }

        $person = Staff::findOrFail($id);
        $departments = Department::where('status', 'active')->get();
        $teams = Team::where('status', 'active')->get();

        return view('admin.staff_edit', [
            'person' => $person,
            'departments' => $departments,
            'teams' => $teams
        ]);
    }

    // Update staff member
    public function update(Request $request, $id)
    {
        if (!Session::has('admin_id')) {
            return redirect()->route('admin.login');
        }

        $person = Staff::findOrFail($id);

        $request->validate([
            'staff_name' => 'required|string|max:255',
            'staff_email' => 'required|email|unique:staff,staff_email,' . $id . ',staff_id',
            'staff_password' => 'nullable|string|min:6|confirmed',
            'department_id' => 'nullable|exists:departments,department_id',
            'team_id' => 'nullable|exists:teams,team_id'
        ]);

        $person->staff_name = $request->staff_name;
        $person->staff_email = $request->staff_email;
        $person->department_id = $request->department_id;
        $person->team_id = $request->team_id;

        if ($request->filled('staff_password')) {
            $person->staff_password = Hash::make($request->staff_password);
        }

        $person->save();

        return redirect()->route('admin.staff.index')->with('success', 'Staff member updated successfully!');
    }

    // Delete staff member
    public function destroy($id)
    {
        if (!Session::has('admin_id')) {
            return redirect()->route('admin.login');
        }

        $person = Staff::findOrFail($id);
        $person->delete();

        return redirect()->route('admin.staff.index')->with('success', 'Staff member deleted successfully!');
    }
}

