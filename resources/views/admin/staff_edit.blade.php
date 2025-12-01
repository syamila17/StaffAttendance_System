<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Staff Member</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-900 text-white">

  <!-- Sidebar -->
  <div class="flex h-screen">
    <aside class="w-64 bg-gradient-to-b from-orange-600 to-orange-700 p-6 shadow-lg">
      <div class="flex items-center gap-3 mb-8">
        <div class="bg-white/20 w-12 h-12 rounded-lg flex items-center justify-center">
          <i class="fas fa-user-shield text-2xl"></i>
        </div>
        <div>
          <h2 class="text-lg font-bold">Admin Panel</h2>
          <p class="text-xs text-gray-200">Management</p>
        </div>
      </div>

      <nav class="space-y-3">
        <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 rounded-lg hover:bg-white/20 transition">
          <i class="fas fa-home mr-2"></i>Dashboard
        </a>
        <a href="{{ route('admin.staff.index') }}" class="block px-4 py-2 rounded-lg bg-white/30">
          <i class="fas fa-users mr-2"></i>Staff Management
        </a>
        <a href="{{ route('admin.attendance') }}" class="block px-4 py-2 rounded-lg hover:bg-white/20 transition">
          <i class="fas fa-calendar-check mr-2"></i>Attendance
        </a>
        <a href="{{ route('admin.attendance.report') }}" class="block px-4 py-2 rounded-lg hover:bg-white/20 transition">
          <i class="fas fa-chart-bar mr-2"></i>Reports
        </a>
        <a href="{{ route('admin.departments') }}" class="block px-4 py-2 rounded-lg hover:bg-white/20 transition">
          <i class="fas fa-building mr-2"></i>Departments
        </a>
        <a href="{{ route('admin.leave.requests') }}" class="block px-4 py-2 rounded-lg hover:bg-white/20 transition">
          <i class="fas fa-calendar-times mr-2"></i>Leave Requests
        </a>
        <a href="{{ route('admin.logout') }}" class="block px-4 py-2 rounded-lg hover:bg-white/20 transition">
          <i class="fas fa-sign-out-alt mr-2"></i>Logout
        </a>
      </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto p-8">
      <div class="max-w-2xl">
        <div class="mb-8">
          <a href="{{ route('admin.staff.index') }}" class="text-orange-400 hover:text-orange-300 mb-4 inline-block">
            <i class="fas fa-arrow-left mr-2"></i>Back to Staff List
          </a>
          <h1 class="text-4xl font-bold mb-2">Edit Staff Member</h1>
          <p class="text-gray-400">Update staff information</p>
        </div>

        @if($errors->any())
          <div class="bg-red-500/20 border border-red-500 text-red-300 px-4 py-3 rounded-lg mb-6">
            <i class="fas fa-exclamation-circle mr-2"></i>
            <strong>Please fix the following errors:</strong>
            <ul class="mt-2 list-disc list-inside">
              @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <!-- Form Card -->
        <div class="bg-gray-800 rounded-lg border border-gray-700 p-8">
          <form method="POST" action="{{ route('admin.staff.update', $person->staff_id) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Staff Name -->
            <div>
              <label class="block text-base font-semibold text-gray-300 mb-2"><i class="fas fa-user mr-2"></i>Staff Name *</label>
              <input type="text" name="staff_name" value="{{ $person->staff_name }}" required
                class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-orange-500 transition text-base">
              @error('staff_name')
                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
              @enderror
            </div>

            <!-- Email -->
            <div>
              <label class="block text-base font-semibold text-gray-300 mb-2"><i class="fas fa-envelope mr-2"></i>Email Address *</label>
              <input type="email" name="staff_email" value="{{ $person->staff_email }}" required
                class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-orange-500 transition text-base">
              @error('staff_email')
                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
              @enderror
            </div>

            <!-- Password (Optional) -->
            <div class="bg-gray-700 border border-gray-600 rounded-lg p-4">
              <label class="block text-base font-semibold text-gray-300 mb-2"><i class="fas fa-lock mr-2"></i>Change Password (Leave blank to keep current)</label>
              <input type="password" name="staff_password" 
                class="w-full px-4 py-3 bg-gray-600 border border-gray-500 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-orange-500 transition text-base"
                placeholder="New password">
              <input type="password" name="staff_password_confirmation" 
                class="w-full px-4 py-3 bg-gray-600 border border-gray-500 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-orange-500 transition text-base mt-2"
                placeholder="Confirm new password">
              @error('staff_password')
                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
              @enderror
            </div>

            <!-- Department (Optional) -->
            <div>
              <label class="block text-base font-semibold text-gray-300 mb-2"><i class="fas fa-building mr-2"></i>Department</label>
              <select id="departmentSelect" name="department_id" onchange="loadTeams()"
                class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-orange-500 transition text-base">
                <option value="">-- Select Department --</option>
                @foreach($departments as $dept)
                  <option value="{{ $dept->department_id }}" 
                    @if($person->department_id == $dept->department_id) selected @endif>{{ $dept->department_name }}</option>
                @endforeach
              </select>
              @error('department_id')
                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
              @enderror
            </div>

            <!-- Team (Optional) -->
            <div>
              <label class="block text-base font-semibold text-gray-300 mb-2"><i class="fas fa-users mr-2"></i>Team</label>
              <select id="teamSelect" name="team_id"
                class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-orange-500 transition text-base">
                <option value="">-- Select Team --</option>
                @foreach($teams as $team)
                  <option value="{{ $team->team_id }}"
                    @if($person->team_id == $team->team_id) selected @endif>{{ $team->team_name }}</option>
                @endforeach
              </select>
              @error('team_id')
                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
              @enderror
            </div>

            <!-- Staff ID (Read-only) -->
            <div>
              <label class="block text-base font-semibold text-gray-400 mb-2"><i class="fas fa-id-card mr-2"></i>Staff ID</label>
              <input type="text" value="{{ $person->staff_id }}" disabled
                class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-gray-400 cursor-not-allowed text-base">
            </div>

            <!-- Buttons -->
            <div class="flex gap-4 pt-6 border-t border-gray-700">
              <button type="submit" class="flex-1 px-6 py-3 bg-gradient-to-r from-orange-600 to-orange-700 hover:from-orange-700 hover:to-orange-800 rounded-lg font-semibold transition shadow-lg text-base">
                <i class="fas fa-save mr-2"></i>Update Staff Member
              </button>
              <a href="{{ route('admin.staff.index') }}" class="flex-1 px-6 py-3 bg-gray-700 hover:bg-gray-600 rounded-lg font-semibold transition text-center text-base">
                <i class="fas fa-times mr-2"></i>Cancel
              </a>
            </div>
          </form>
        </div>
      </div>
    </main>
  </div>

  <script>
    // Build teams data from blade template
    const allTeams = {
      @foreach($departments as $dept)
        @php
          $deptTeams = $teams->filter(fn($t) => $t->department_id == $dept->department_id);
        @endphp
        @if($deptTeams->count() > 0)
        '{{ $dept->department_id }}': [
          @foreach($deptTeams as $team)
            { id: '{{ $team->team_id }}', name: '{{ $team->team_name }}' }{{ !$loop->last ? ',' : '' }}
          @endforeach
        ]{{ !$loop->last ? ',' : '' }}
        @else
        '{{ $dept->department_id }}': []{{ !$loop->last ? ',' : '' }}
        @endif
      @endforeach
    };

    console.log('All Teams Data:', allTeams); // Debug log

    function loadTeams() {
      const departmentId = document.getElementById('departmentSelect').value;
      const teamSelect = document.getElementById('teamSelect');
      const currentTeamId = {{ $person->team_id ?? 'null' }};
      
      console.log('Selected Department ID:', departmentId); // Debug log
      console.log('Current Team ID:', currentTeamId); // Debug log
      
      // Clear existing options
      teamSelect.innerHTML = '<option value="">-- Select Team --</option>';
      
      if (departmentId && allTeams[departmentId] && allTeams[departmentId].length > 0) {
        allTeams[departmentId].forEach(team => {
          const option = document.createElement('option');
          option.value = team.id;
          option.textContent = team.name;
          option.style.color = 'black';
          if (team.id == currentTeamId) {
            option.selected = true;
          }
          teamSelect.appendChild(option);
        });
        console.log('Teams loaded for department:', departmentId); // Debug log
      } else {
        console.log('No teams found for department:', departmentId); // Debug log
      }
    }

    // Initialize teams on page load
    window.addEventListener('load', function() {
      if (document.getElementById('departmentSelect').value) {
        loadTeams();
      }
    });
  </script>

</body>
</html>
