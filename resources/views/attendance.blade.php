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
        <div class="flex gap-4">
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
      </div>

      <!-- Attendance History -->
      <div class="bg-white/10 p-8 rounded-xl shadow-lg border border-white/20">
        <h2 class="text-2xl font-semibold mb-6">Recent Attendance History</h2>

        <div class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="border-b border-white/20">
                <th class="text-left py-3 px-4">Date</th>
                <th class="text-left py-3 px-4">Check-in</th>
                <th class="text-left py-3 px-4">Check-out</th>
                <th class="text-left py-3 px-4">Status</th>
                <th class="text-left py-3 px-4">Duration</th>
              </tr>
            </thead>
            <tbody>
              @forelse($recentAttendance as $record)
                <tr class="border-b border-white/10 hover:bg-white/5">
                  <td class="py-3 px-4">{{ $record->attendance_date->format('Y-m-d (l)') }}</td>
                  <td class="py-3 px-4">{{ $record->check_in_time ?? '-' }}</td>
                  <td class="py-3 px-4">{{ $record->check_out_time ?? '-' }}</td>
                  <td class="py-3 px-4">
                    <span class="px-3 py-1 rounded-full text-sm capitalize
                      @if($record->status === 'present') bg-green-500/20 text-green-300
                      @elseif($record->status === 'late') bg-yellow-500/20 text-yellow-300
                      @elseif($record->status === 'leave') bg-blue-500/20 text-blue-300
                      @else bg-red-500/20 text-red-300 @endif">
                      {{ $record->status }}
                    </span>
                  </td>
                  <td class="py-3 px-4">
                    @if($record->check_in_time && $record->check_out_time)
                      {{ \Carbon\Carbon::createFromFormat('H:i:s', $record->check_in_time)->diffInHours(\Carbon\Carbon::createFromFormat('H:i:s', $record->check_out_time)) }} hrs
                    @else
                      -
                    @endif
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="py-6 text-center text-gray-400">
                    <i class="fas fa-inbox mr-2"></i>No attendance records yet
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </main>

</body>
</html>
