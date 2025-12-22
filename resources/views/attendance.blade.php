<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Attendance Tracking</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="flex h-screen bg-gradient-to-br from-red-800 to-red-950 text-white">

  <!-- Sidebar -->
  <aside class="w-64 bg-gradient-to-b from-red-900 to-red-950 shadow-lg p-6 space-y-6">
    <div class="flex items-center gap-3">
      <div class="bg-white/20 w-12 h-12 rounded-lg flex items-center justify-center overflow-hidden">
        @if($profile && !empty($profile->profile_image))
          <img src="{{ asset('storage/' . $profile->profile_image) }}" alt="Profile" class="w-full h-full object-cover">
        @else
          <i class="fas fa-user text-2xl text-white"></i>
        @endif
      </div>
      <div>
        <h2 class="text-lg font-bold">Staff Panel</h2>
        <p class="text-xs text-gray-300">{{ session('staff_name') }}</p>
      </div>
    </div>

    <nav class="mt-8 space-y-4">
      <a href="{{ route('staff.dashboard') }}" class="block px-4 py-2 rounded-lg hover:bg-white/20"><i class="fas fa-home mr-2"></i>Dashboard</a>
      <a href="{{ route('attendance.show') }}" class="block px-4 py-2 rounded-lg bg-white/20"><i class="fas fa-calendar-check mr-2"></i>Attendance</a>
      <a href="{{ route('staff.profile') }}" class="block px-4 py-2 rounded-lg hover:bg-white/20"><i class="fas fa-user-circle mr-2"></i>Profile</a>
      <a href="{{ route('staff.apply-leave') }}" class="block px-4 py-2 rounded-lg hover:bg-white/20"><i class="fas fa-calendar-times mr-2"></i>Apply Leave</a>
      <a href="{{ route('staff.leave.status') }}" class="block px-4 py-2 rounded-lg hover:bg-white/20"><i class="fas fa-list-check mr-2"></i>Leave Status</a>
      <a href="{{ route('staff.logout') }}" 
        class="block px-4 py-2 rounded-lg hover:bg-white/20 flex items-center">
        <i class="fas fa-sign-out-alt mr-2"></i> Logout
      </a>
    </nav>
  </aside>

  <!-- Main Content -->
  <main class="flex-1 p-10 overflow-y-auto">
    <div class="max-w-4xl">
      <h1 class="text-3xl font-bold mb-6">Attendance Tracking</h1>

      @if(session('success'))
        <div class="bg-green-500/20 border border-green-500 text-green-300 px-4 py-3 rounded-lg mb-6">
          <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
      @endif

      @if($errors->any())
        <div class="bg-red-500/20 border border-red-500 text-red-300 px-4 py-3 rounded-lg mb-6">
          <i class="fas fa-exclamation-circle mr-2"></i>{{ $errors->first() }}
        </div>
      @endif

      <!-- Today's Attendance Card -->
      <div class="bg-white/10 p-8 rounded-xl shadow-lg border border-white/20 mb-8">
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-2xl font-semibold">Today's Attendance</h2>
          <div class="text-right">
            <p class="text-xs text-gray-400">Current Time</p>
            <p id="currentTime" class="text-2xl font-bold text-red-400">--:--:--</p>
          </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
          <!-- Check-in Status -->
          <div class="bg-white/5 p-4 rounded-lg border border-white/10">
            <p class="text-gray-300 text-sm mb-2">Check-in Time</p>
            @if($todayAttendance && $todayAttendance->check_in_time)
              <p class="text-2xl font-bold text-green-400">{{ substr($todayAttendance->check_in_time, 0, 8) }}</p>
              <p class="text-xs text-gray-400 mt-1">✓ Checked in</p>
            @else
              <p class="text-2xl font-bold text-gray-400">--:--:--</p>
              <p class="text-xs text-gray-400 mt-1">Not checked in</p>
            @endif
          </div>

          <!-- Check-out Status -->
          <div class="bg-white/5 p-4 rounded-lg border border-white/10">
            <p class="text-gray-300 text-sm mb-2">Check-out Time</p>
            @if($todayAttendance && $todayAttendance->check_out_time)
              <p class="text-2xl font-bold text-blue-400">{{ substr($todayAttendance->check_out_time, 0, 8) }}</p>
              <p class="text-xs text-gray-400 mt-1">✓ Checked out</p>
            @else
              <p class="text-2xl font-bold text-gray-400">--:--:--</p>
              <p class="text-xs text-gray-400 mt-1">Not checked out</p>
            @endif
          </div>

          <!-- Status -->
          <div class="bg-white/5 p-4 rounded-lg border border-white/10">
            <p class="text-gray-300 text-sm mb-2">Status</p>
            @if($todayAttendance)
              <p class="text-2xl font-bold capitalize 
                @if($todayAttendance->status === 'present') text-green-400 @elseif($todayAttendance->status === 'late') text-yellow-400 @else text-red-400 @endif">
                {{ $todayAttendance->status }}
              </p>
            @else
              <p class="text-2xl font-bold text-gray-400">-</p>
            @endif
          </div>
        </div>

            <!-- Duration Card (if both check-in and check-out exist) -->
        @if($todayAttendance && $todayAttendance->check_in_time && $todayAttendance->check_out_time)
          <div class="bg-blue-500/20 border border-blue-400/50 p-4 rounded-lg mb-8">
            <p class="text-gray-300 text-sm mb-2">Working Duration</p>
            <p class="text-xl font-semibold text-blue-300">
              <i class="fas fa-hourglass-half mr-2"></i>
              @php
                $checkIn = \Carbon\Carbon::createFromFormat('H:i:s', $todayAttendance->check_in_time);
                $checkOut = \Carbon\Carbon::createFromFormat('H:i:s', $todayAttendance->check_out_time);
                $minutes = abs($checkOut->diffInMinutes($checkIn));
                $hours = floor($minutes / 60);
                $remainingMinutes = $minutes % 60;
                $seconds = abs($checkOut->diffInSeconds($checkIn)) % 60;
                echo $hours . 'h ' . $remainingMinutes . 'm';
              @endphp
            </p>
          </div>
        @endif

        <!-- Check-in/Check-out Buttons -->
        <div class="flex gap-4 mb-8">
          @if($approvedLeaveToday)
            <div class="w-full bg-yellow-500/20 border border-yellow-500 text-yellow-300 px-6 py-3 rounded-lg font-semibold text-center">
              <i class="fas fa-calendar-times mr-2"></i>You are on approved leave today. Check-in/Check-out disabled.
            </div>
          @else
            @if(!$todayAttendance || !$todayAttendance->check_in_time)
              <form method="POST" action="{{ route('attendance.checkIn') }}" class="flex-1">
                @csrf
                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 px-6 py-3 rounded-lg font-semibold transition">
                  <i class="fas fa-sign-in-alt mr-2"></i>Check In
                </button>
              </form>
            @endif

            @if($todayAttendance && $todayAttendance->check_in_time && !$todayAttendance->check_out_time)
              <form method="POST" action="{{ route('attendance.checkOut') }}" class="flex-1">
                @csrf
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 px-6 py-3 rounded-lg font-semibold transition">
                  <i class="fas fa-sign-out-alt mr-2"></i>Check Out
                </button>
              </form>
            @endif
          @endif
        </div>

        <!-- Status Selection Form -->
        <div class="bg-white/5 p-6 rounded-lg border border-white/10 mb-8">
          <h3 class="text-xl font-semibold mb-4 flex items-center">
            <i class="fas fa-tasks mr-2 text-red-400"></i>Update Attendance Status
          </h3>

          <form method="POST" action="{{ route('attendance.updateStatus') }}" class="space-y-4">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <!-- Date -->
              <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Date</label>
                <input type="date" name="date" value="{{ now()->format('Y-m-d') }}" required
                  class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-red-500">
              </div>

              <!-- Status -->
              <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Status</label>
                <select id="statusSelect" name="status" required onchange="toggleTimeInputs()"
                  class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-red-500"
                  style="color-scheme: dark;">
                  <option value="" style="color: gray;">-- Select Status --</option>
                  <option value="present" style="color: black;" @if($todayAttendance && $todayAttendance->status === 'present') selected @endif>✓ Present</option>
                  <option value="absent" style="color: black;" @if($todayAttendance && $todayAttendance->status === 'absent') selected @endif>✗ Absent</option>
                  <option value="late" style="color: black;" @if($todayAttendance && $todayAttendance->status === 'late') selected @endif>⏰ Late</option>
                  <option value="el" style="color: black;" @if($todayAttendance && $todayAttendance->status === 'el') selected @endif>📋 EL (Emergency Leave)</option>
                  <option value="on leave" style="color: black;" @if($todayAttendance && $todayAttendance->status === 'on leave') selected @endif>🏖️ On Leave</option>
                  <option value="half day" style="color: black;" @if($todayAttendance && $todayAttendance->status === 'half day') selected @endif>⏱️ Half Day</option>
                </select>
              </div>
            </div>

            <!-- Check-in and Check-out Times -->
            <div id="timeInputsContainer" class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <!-- Check-in Time -->
              <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Check-in Time (Optional)</label>
                <input type="time" name="check_in_time" value="{{ $todayAttendance && $todayAttendance->check_in_time ? substr($todayAttendance->check_in_time, 0, 5) : '' }}"
                  class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-red-500">
              </div>

              <!-- Check-out Time -->
              <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Check-out Time (Optional)</label>
                <input type="time" name="check_out_time" value="{{ $todayAttendance && $todayAttendance->check_out_time ? substr($todayAttendance->check_out_time, 0, 5) : '' }}"
                  class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-red-500">
              </div>
            </div>

            <!-- EL Reason (Mandatory for EL) -->
            <div id="elReasonContainer" style="display: none;">
              <label class="block text-sm font-medium text-gray-300 mb-2">Reason for Emergency Leave <span class="text-red-400">*</span></label>
              <textarea name="el_reason" rows="3" placeholder="Provide reason for emergency leave..."
                class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-red-500 placeholder-gray-500"></textarea>
            </div>

            <!-- EL Proof (Optional for EL) -->
            <div id="elProofContainer" style="display: none;">
              <label class="block text-sm font-medium text-gray-300 mb-2">Supporting Document (Optional)</label>
              <input type="file" name="el_proof_file" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-red-500">
              <p class="text-xs text-gray-400 mt-1">Allowed formats: PDF, JPG, PNG, DOC, DOCX (Max 5MB)</p>
            </div>

            <!-- Remarks -->
            <div>
              <label class="block text-sm font-medium text-gray-300 mb-2">Remarks (Optional)</label>
              <textarea name="remarks" rows="3" placeholder="Add any remarks or notes..."
                class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-red-500 placeholder-gray-500">{{ $todayAttendance && $todayAttendance->remarks ? $todayAttendance->remarks : '' }}</textarea>
            </div>

            <!-- Submit Button -->
            <div class="flex gap-4">
              <button type="submit" class="flex-1 bg-red-600 hover:bg-red-700 px-6 py-2 rounded-lg font-semibold transition">
                <i class="fas fa-save mr-2"></i>Save Status
              </button>
              <button type="button" onclick="resetAttendanceForm()" class="flex-1 bg-blue-600 hover:bg-blue-700 px-6 py-2 rounded-lg font-semibold transition">
                <i class="fas fa-redo mr-2"></i>Clear All
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </main>

  <script>
    // Live clock display
    function updateClock() {
      const now = new Date();
      const timeString = now.toLocaleTimeString('en-US', { 
        hour: '2-digit', 
        minute: '2-digit', 
        second: '2-digit',
        hour12: false 
      });
      document.getElementById('currentTime').textContent = timeString;
    }

    // Update clock every second
    updateClock();
    setInterval(updateClock, 1000);

    function toggleTimeInputs() {
      const status = document.getElementById('statusSelect').value;
      const timeInputsContainer = document.getElementById('timeInputsContainer');
      const checkInInput = document.querySelector('input[name="check_in_time"]');
      const checkOutInput = document.querySelector('input[name="check_out_time"]');
      const elReasonContainer = document.getElementById('elReasonContainer');
      const elProofContainer = document.getElementById('elProofContainer');
      const elReasonField = document.querySelector('textarea[name="el_reason"]');

      // Show/hide time inputs
      if (status === 'absent' || status === 'on leave' || status === 'el') {
        timeInputsContainer.style.display = 'none';
        checkInInput.value = '';
        checkOutInput.value = '';
      } else {
        timeInputsContainer.style.display = 'grid';
      }

      // Show/hide EL specific fields
      if (status === 'el') {
        elReasonContainer.style.display = 'block';
        elProofContainer.style.display = 'block';
        elReasonField.required = true;
      } else {
        elReasonContainer.style.display = 'none';
        elProofContainer.style.display = 'none';
        elReasonField.required = false;
        elReasonField.value = '';
        document.querySelector('input[name="el_proof_file"]').value = '';
      }
    }

    function resetAttendanceForm() {
      const form = document.querySelector('form');
      // Reset all form fields
      form.reset();
      // Reset date to today's date
      const today = new Date().toISOString().split('T')[0];
      document.querySelector('input[name="date"]').value = today;
      // Reset status to empty
      document.getElementById('statusSelect').value = '';
      // Clear remarks textarea
      document.querySelector('textarea[name="remarks"]').value = '';
      // Clear EL specific fields
      document.querySelector('textarea[name="el_reason"]').value = '';
      document.querySelector('input[name="el_proof_file"]').value = '';
      // Show time inputs
      const timeInputsContainer = document.getElementById('timeInputsContainer');
      timeInputsContainer.style.display = 'grid';
      // Clear time inputs
      document.querySelector('input[name="check_in_time"]').value = '';
      document.querySelector('input[name="check_out_time"]').value = '';
      // Hide EL fields
      document.getElementById('elReasonContainer').style.display = 'none';
      document.getElementById('elProofContainer').style.display = 'none';
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
      toggleTimeInputs();
    });
  </script>

</body>
</html>
