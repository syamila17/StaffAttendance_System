<!DOCTYPE html>
<!-- UPDATED: Check-in/out hidden for Absent, Reset button added, Status text is black -->
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Attendance Management</title>
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
          <p class="text-xs text-gray-200">{{ session('admin_name') }}</p>
        </div>
      </div>

      <nav class="space-y-3">
        <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 rounded-lg hover:bg-white/20 transition">
          <i class="fas fa-home mr-2"></i>Dashboard
        </a>
        <a href="{{ route('admin.staff.index') }}" class="block px-4 py-2 rounded-lg hover:bg-white/20 transition">
          <i class="fas fa-users mr-2"></i>Staff Management
        </a>
        <a href="{{ route('admin.attendance') }}" class="block px-4 py-2 rounded-lg bg-white/30">
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
      <div class="max-w-6xl">
        <h1 class="text-4xl font-bold mb-2">Attendance Management</h1>
        <p class="text-gray-400 mb-8">Manage daily staff attendance</p>

        @if(session('success'))
          <div class="bg-green-500/20 border border-green-500 text-green-300 px-4 py-3 rounded-lg mb-6">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
          </div>
        @endif

        <!-- Date Selector -->
        <div class="mb-8 flex gap-4">
          <form method="GET" action="{{ route('admin.attendance') }}" class="flex gap-4">
            <input type="date" name="date" value="{{ $selectedDate }}" 
              class="px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white">
            <button type="submit" class="px-6 py-2 bg-orange-600 hover:bg-orange-700 rounded-lg transition">
              <i class="fas fa-search mr-2"></i>Filter
            </button>
          </form>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-5 gap-4 mb-8">
          <div class="bg-gray-800 p-4 rounded-lg border border-gray-700">
            <p class="text-gray-400 text-sm">Total Staff</p>
            <p class="text-3xl font-bold text-white">{{ $stats['total_staff'] }}</p>
          </div>
          <div class="bg-green-900/30 p-4 rounded-lg border border-green-700">
            <p class="text-green-300 text-sm">Present</p>
            <p class="text-3xl font-bold text-green-300">{{ $stats['present'] }}</p>
          </div>
          <div class="bg-red-900/30 p-4 rounded-lg border border-red-700">
            <p class="text-red-300 text-sm">Absent</p>
            <p class="text-3xl font-bold text-red-300">{{ $stats['absent'] }}</p>
          </div>
          <div class="bg-yellow-900/30 p-4 rounded-lg border border-yellow-700">
            <p class="text-yellow-300 text-sm">Late</p>
            <p class="text-3xl font-bold text-yellow-300">{{ $stats['late'] }}</p>
          </div>
          <div class="bg-blue-900/30 p-4 rounded-lg border border-blue-700">
            <p class="text-blue-300 text-sm">Leave</p>
            <p class="text-3xl font-bold text-blue-300">{{ $stats['leave'] }}</p>
          </div>
        </div>

        <!-- Attendance Table -->
        <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden shadow-sm">
          <table class="w-full">
            <thead class="bg-gray-700 border-b border-gray-600">
              <tr>
                <th class="px-6 py-4 text-left">Staff Name</th>
                <th class="px-6 py-4 text-left">Email</th>
                <th class="px-6 py-4 text-left">Status</th>
                <th class="px-6 py-4 text-left">Check-in</th>
                <th class="px-6 py-4 text-left">Check-out</th>
                <th class="px-6 py-4 text-left">Action</th>
              </tr>
            </thead>
            <tbody>
              @forelse($staff as $person)
                @php
                  $attendance = $attendanceData->firstWhere('staff_id', $person->staff_id);
                @endphp
                <tr class="border-b border-gray-700 hover:bg-gray-750 transition">
                  <td class="px-6 py-4">{{ $person->staff_name }}</td>
                  <td class="px-6 py-4">{{ $person->staff_email }}</td>
                  <td class="px-6 py-4">
                    @if($attendance)
                      <span class="px-3 py-1 rounded-full text-sm capitalize
                        @if($attendance->status === 'present') bg-green-500/20 text-green-300
                        @elseif($attendance->status === 'late') bg-yellow-500/20 text-yellow-300
                        @elseif($attendance->status === 'leave') bg-blue-500/20 text-blue-300
                        @else bg-red-500/20 text-red-300 @endif">
                        {{ $attendance->status }}
                      </span>
                    @else
                      <span class="px-3 py-1 rounded-full text-sm bg-gray-600 text-gray-300">No Record</span>
                    @endif
                  </td>
                  <td class="px-6 py-4">{{ $attendance && $attendance->check_in_time ? substr($attendance->check_in_time, 0, 5) : '-' }}</td>
                  <td class="px-6 py-4">{{ $attendance && $attendance->check_out_time ? substr($attendance->check_out_time, 0, 5) : '-' }}</td>
                  <td class="px-6 py-4">
                    <button onclick="openModal({{ $person->staff_id }}, '{{ $person->staff_name }}')" 
                      class="px-3 py-1 bg-orange-600 hover:bg-orange-700 rounded text-sm transition">
                      Edit
                    </button>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="px-6 py-8 text-center text-gray-400">
                    No staff members found
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </main>
  </div>

  <!-- Modal -->
  <div id="modal" class="hidden fixed inset-0 bg-black/70 flex items-center justify-center z-50">
    <div class="bg-gray-800 rounded-lg p-6 max-w-md w-full border border-gray-700">
      <h2 class="text-2xl font-bold mb-4">Mark Attendance</h2>
      
      <form id="attendanceForm" method="POST" action="{{ route('admin.attendance.mark') }}">
        @csrf
        <input type="hidden" name="staff_id" id="staffId">
        <input type="hidden" name="attendance_date" value="{{ $selectedDate }}">

        <div class="mb-4">
          <label class="block text-sm mb-2">Staff</label>
          <input type="text" id="staffName" disabled class="w-full px-3 py-2 bg-gray-700 rounded text-gray-300">
        </div>

        <div class="mb-4">
          <label class="block text-sm mb-2">Status</label>
          <select id="statusSelect" name="status" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded text-black font-semibold" onchange="toggleTimeInputs()">
            <option value="present" class="text-black">Present</option>
            <option value="absent" class="text-black">Absent</option>
            <option value="late" class="text-black">Late</option>
            <option value="leave" class="text-black">Leave</option>
          </select>
        </div>

        <div id="timeInputsContainer" class="grid grid-cols-2 gap-4 mb-4">
          <div>
            <label class="block text-sm mb-2">Check-in</label>
            <input type="time" name="check_in_time" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded">
          </div>
          <div>
            <label class="block text-sm mb-2">Check-out</label>
            <input type="time" name="check_out_time" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded">
          </div>
        </div>

        <div class="mb-6">
          <label class="block text-sm mb-2">Remarks</label>
          <textarea name="remarks" rows="3" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded"></textarea>
        </div>

        <div class="flex gap-3">
          <button type="submit" class="flex-1 px-4 py-2 bg-orange-600 hover:bg-orange-700 rounded transition">
            Save
          </button>
          <button type="button" onclick="resetForm()" class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 rounded transition">
            Reset
          </button>
          <button type="button" onclick="closeModal()" class="flex-1 px-4 py-2 bg-gray-700 hover:bg-gray-600 rounded transition">
            Cancel
          </button>
        </div>
      </form>
    </div>
  </div>

  <script>
    function openModal(staffId, staffName) {
      document.getElementById('staffId').value = staffId;
      document.getElementById('staffName').value = staffName;
      document.getElementById('statusSelect').value = 'present';
      document.getElementById('modal').classList.remove('hidden');
      toggleTimeInputs(); // Show time inputs by default
    }

    function closeModal() {
      document.getElementById('modal').classList.add('hidden');
      resetForm();
    }

    function resetForm() {
      const form = document.getElementById('attendanceForm');
      // Clear all input fields
      const inputs = form.querySelectorAll('input[type="text"], input[type="time"], textarea');
      inputs.forEach(input => {
        if (input.name !== 'staff_id' && input.name !== 'attendance_date') {
          input.value = '';
        }
      });
      // Reset status to present
      document.getElementById('statusSelect').value = 'present';
      // Clear remarks
      document.querySelector('textarea[name="remarks"]').value = '';
      // Show time inputs
      toggleTimeInputs();
    }

    function toggleTimeInputs() {
      const status = document.getElementById('statusSelect').value;
      const timeInputsContainer = document.getElementById('timeInputsContainer');
      const checkInInput = document.querySelector('input[name="check_in_time"]');
      const checkOutInput = document.querySelector('input[name="check_out_time"]');

      if (status === 'absent') {
        timeInputsContainer.style.display = 'none';
        checkInInput.value = '';
        checkOutInput.value = '';
      } else {
        timeInputsContainer.style.display = 'grid';
      }
    }
  </script>

</body>
</html>
