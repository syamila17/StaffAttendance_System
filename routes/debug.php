<?php

use Illuminate\Support\Facades\Route;
use App\Models\Department;
use App\Models\Team;

Route::get('/debug/departments', function() {
    $departments = Department::all();
    $teams = Team::all();
    
    return response()->json([
        'departments' => $departments,
        'teams' => $teams,
        'count' => [
            'departments' => $departments->count(),
            'teams' => $teams->count()
        ]
    ]);
});
