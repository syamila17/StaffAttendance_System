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
      <div class="bg-white/20 w-12 h-12 rounded-lg flex items-center justify-center">
        <i class="fas fa-user text-2xl text-white"></i>
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
        <h2 class="text-2xl font-semibold mb-6">Today's Attendance</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
          <!-- Check-in Status -->
          <div class="bg-white/5 p-4 rounded-lg border border-white/10">
            <p class="text-gray-300 text-sm mb-2">Check-in Time</p>
            @if($todayAttendance && $todayAttendance->check_in_time)
              <p class="text-2xl font-bold text-green-400">{{ $todayAttendance->check_in_time }}</p>
              <p class="text-xs text-gray-400 mt-1">Checked in</p>
            @else
              <p class="text-2xl font-bold text-gray-400">--:--</p>
              <p class="text-xs text-gray-400 mt-1">Not checked in</p>
            @endif
          </div>

          <!-- Check-out Status -->
          <div class="bg-white/5 p-4 rounded-lg border border-white/10">
            <p class="text-gray-300 text-sm mb-2">Check-out Time</p>
            @if($todayAttendance && $todayAttendance->check_out_time)
              <p class="text-2xl font-bold text-blue-400">{{ $todayAttendance->check_out_time }}</p>
              <p class="text-xs text-gray-400 mt-1">Checked out</p>
            @else
              <p class="text-2xl font-bold text-gray-400">--:--</p>
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

        <!-- Check-in/Check-out Buttons -->
        <div class="flex gap-4 mb-8">
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
                <select name="status" required
                  class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-red-500">
                  <option value="">-- Select Status --</option>
                  <option value="present" @if($todayAttendance && $todayAttendance->status === 'present') selected @endif>✓ Present</option>
                  <option value="absent" @if($todayAttendance && $todayAttendance->status === 'absent') selected @endif>✗ Absent</option>
                  <option value="late" @if($todayAttendance && $todayAttendance->status === 'late') selected @endif>⏰ Late</option>
                  <option value="el" @if($todayAttendance && $todayAttendance->status === 'el') selected @endif>📋 EL (Emergency Leave)</option>
                  <option value="on leave" @if($todayAttendance && $todayAttendance->status === 'on leave') selected @endif>🏖️ On Leave</option>
                  <option value="half day" @if($todayAttendance && $todayAttendance->status === 'half day') selected @endif>⏱️ Half Day</option>
                </select>
              </div>
            </div>

            <!-- Check-in and Check-out Times -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <!-- Check-in Time -->
              <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Check-in Time (Optional)</label>
                <input type="time" name="check_in_time" value="{{ $todayAttendance && $todayAttendance->check_in_time ? $todayAttendance->check_in_time : '' }}"
                  class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-red-500">
              </div>

              <!-- Check-out Time -->
              <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Check-out Time (Optional)</label>
                <input type="time" name="check_out_time" value="{{ $todayAttendance && $todayAttendance->check_out_time ? $todayAttendance->check_out_time : '' }}"
                  class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-red-500">
              </div>
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
              <button type="reset" class="flex-1 bg-white/10 hover:bg-white/20 px-6 py-2 rounded-lg font-semibold transition">
                <i class="fas fa-undo mr-2"></i>Reset
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </main>

</body>
</html>
