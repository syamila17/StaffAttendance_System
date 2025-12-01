<?php

use Illuminate\Support\Facades\Route;
use App\Models\Department;
use App\Models\Team;

Route::get('/test-data', function() {
    $departments = Department::all();
    $teams = Team::all();
    
    return view('admin.test-data', [
        'departments' => $departments,
        'teams' => $teams
    ]);
});
