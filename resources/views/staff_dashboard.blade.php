<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Staff Dashboard</title>
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
        <p class="text-xs text-gray-300">{{ $staffName}}</p>
      </div>
    </div>

    <nav class="mt-8 space-y-4">
      <a href="{{ route('staff.dashboard') }}" class="block px-4 py-2 rounded-lg bg-white/20"><i class="fas fa-home mr-2"></i>Dashboard</a>
      <a href="{{ route('attendance.show') }}" class="block px-4 py-2 rounded-lg hover:bg-white/20"><i class="fas fa-calendar-check mr-2"></i>Attendance</a>
      <a href="{{ route('staff.profile') }}" class="block px-4 py-2 rounded-lg hover:bg-white/20"><i class="fas fa-user-circle mr-2"></i>Profile</a>
      <a href="{{ route('staff.apply-leave') }}" class="block px-4 py-2 rounded-lg hover:bg-white/20"><i class="fas fa-calendar-times mr-2"></i>Apply Leave</a>
      <a href="{{ route('staff.leave.status') }}" class="block px-4 py-2 rounded-lg hover:bg-white/20 relative group">
        <i class="fas fa-list-check mr-2"></i>Leave Status
        <span id="notificationBadge" class="absolute top-1 right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center hidden font-bold text-xs">0</span>
      </a>
      <a href="{{ route('staff.logout') }}" 
        class="block px-4 py-2 rounded-lg hover:bg-white/20 flex items-center">
        <i class="fas fa-sign-out-alt mr-2"></i> Logout
      </a>

    </nav>
  </aside>

  <!-- Main Content -->
  <main class="flex-1 p-10 overflow-y-auto">
    <h1 class="text-3xl font-bold mb-2">Welcome, {{ $staffName}}!</h1>
    <p class="text-gray-200 text-lg mb-8">Email: {{ $staffEmail }}</p>

    <!-- Today's Attendance Card -->
    <div class="bg-white/10 p-6 rounded-xl shadow-lg border border-white/20 mb-8">
      <h2 class="text-2xl font-semibold mb-4 flex items-center">
        <i class="fas fa-calendar-check mr-2 text-green-400"></i>Today's Attendance
      </h2>
      
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        @if($todayAttendance)
          <!-- Status -->
          <div class="bg-white/5 p-4 rounded-lg border border-white/10">
            <p class="text-gray-300 text-sm mb-2">Status</p>
            <p class="text-2xl font-bold capitalize 
              @if($todayAttendance->status === 'present') text-green-400
              @elseif($todayAttendance->status === 'absent') text-red-400
              @elseif($todayAttendance->status === 'late') text-yellow-400
              @elseif($todayAttendance->status === 'el') text-orange-400
              @elseif($todayAttendance->status === 'on leave') text-blue-400
              @elseif($todayAttendance->status === 'half day') text-purple-400
              @else text-gray-400 @endif">
              {{ ucfirst($todayAttendance->status) }}
            </p>
          </div>

          <!-- Check-in Time - Only show if Present -->
          @if($todayAttendance->status === 'present')
            <div class="bg-white/5 p-4 rounded-lg border border-white/10">
              <p class="text-gray-300 text-sm mb-2">Check-in Time</p>
              <p class="text-2xl font-bold @if($todayAttendance->check_in_time) text-green-400 @else text-gray-400 @endif">
                {{ $todayAttendance->check_in_time ? substr($todayAttendance->check_in_time, 0, 5) : '--:--' }}
              </p>
            </div>

            <!-- Check-out Time - Only show if Present -->
            <div class="bg-white/5 p-4 rounded-lg border border-white/10">
              <p class="text-gray-300 text-sm mb-2">Check-out Time</p>
              <p class="text-2xl font-bold @if($todayAttendance->check_out_time) text-blue-400 @else text-gray-400 @endif">
                {{ $todayAttendance->check_out_time ? substr($todayAttendance->check_out_time, 0, 5) : '--:--' }}
              </p>
            </div>

            <!-- Duration - Only show if Present -->
            <div class="bg-white/5 p-4 rounded-lg border border-white/10">
              <p class="text-gray-300 text-sm mb-2">Duration</p>
              @if($todayAttendance->check_in_time && $todayAttendance->check_out_time)
                <p class="text-2xl font-bold text-purple-400">
                  {{ \Carbon\Carbon::createFromFormat('H:i:s', $todayAttendance->check_in_time)->diffInHours(\Carbon\Carbon::createFromFormat('H:i:s', $todayAttendance->check_out_time)) }} hrs
                </p>
              @else
                <p class="text-2xl font-bold text-gray-400">-</p>
              @endif
            </div>
          @else
            <!-- Show placeholder boxes for non-present status -->
            <div class="bg-white/5 p-4 rounded-lg border border-white/10">
              <p class="text-gray-300 text-sm mb-2">Check-in Time</p>
              <p class="text-2xl font-bold text-gray-500">--:--</p>
            </div>

            <div class="bg-white/5 p-4 rounded-lg border border-white/10">
              <p class="text-gray-300 text-sm mb-2">Check-out Time</p>
              <p class="text-2xl font-bold text-gray-500">--:--</p>
            </div>

            <div class="bg-white/5 p-4 rounded-lg border border-white/10">
              <p class="text-gray-300 text-sm mb-2">Duration</p>
              <p class="text-2xl font-bold text-gray-500">-</p>
            </div>
          @endif
        @else
          <div class="col-span-4 bg-white/5 p-6 rounded-lg border border-white/10 text-center">
            <i class="fas fa-info-circle mr-2 text-blue-400"></i>
            <p class="text-gray-300">No attendance recorded yet for today</p>
            <a href="{{ route('attendance.show') }}" class="text-red-400 hover:text-red-300 mt-2 inline-block">
              <i class="fas fa-arrow-right mr-1"></i>Mark attendance now
            </a>
          </div>
        @endif
      </div>
    </div>

    <!-- Attendance Statistics -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      <div class="bg-white/10 p-6 rounded-xl shadow-lg border border-white/20">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-300 text-sm mb-1">Total Present</p>
            <p class="text-3xl font-bold text-green-400">{{ $totalPresent }}</p>
          </div>
          <i class="fas fa-check-circle text-green-400/20 text-4xl"></i>
        </div>
      </div>

      <div class="bg-white/10 p-6 rounded-xl shadow-lg border border-white/20">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-300 text-sm mb-1">Total Absent</p>
            <p class="text-3xl font-bold text-red-400">{{ $totalAbsent }}</p>
          </div>
          <i class="fas fa-times-circle text-red-400/20 text-4xl"></i>
        </div>
      </div>

      <div class="bg-white/10 p-6 rounded-xl shadow-lg border border-white/20">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-300 text-sm mb-1">Total Late</p>
            <p class="text-3xl font-bold text-yellow-400">{{ $totalLate }}</p>
          </div>
          <i class="fas fa-clock text-yellow-400/20 text-4xl"></i>
        </div>
      </div>

      <div class="bg-white/10 p-6 rounded-xl shadow-lg border border-white/20">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-300 text-sm mb-1">Quick Actions</p>
            <a href="{{ route('attendance.show') }}" class="text-red-400 hover:text-red-300 font-semibold text-sm">
              View Attendance <i class="fas fa-arrow-right ml-1"></i>
            </a>
          </div>
          <i class="fas fa-link text-red-400/20 text-4xl"></i>
        </div>
      </div>
    </div>

    <!-- Attendance History -->
    <div class="bg-white/10 p-8 rounded-xl shadow-lg border border-white/20 mt-8">
      <h2 class="text-2xl font-semibold mb-6 flex items-center">
        <i class="fas fa-history mr-2 text-blue-400"></i>Attendance History (Last 30 Days)
      </h2>

      <div class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="border-b border-white/20">
              <th class="text-left py-3 px-4">Date</th>
              <th class="text-left py-3 px-4">Status</th>
              <th class="text-left py-3 px-4">Check-in</th>
              <th class="text-left py-3 px-4">Check-out</th>
              <th class="text-left py-3 px-4">Duration</th>
              <th class="text-left py-3 px-4">Remarks</th>
            </tr>
          </thead>
          <tbody>
            @forelse($recentAttendance as $record)
              <tr class="border-b border-white/10 hover:bg-white/5">
                <td class="py-3 px-4">{{ $record->attendance_date->format('Y-m-d (l)') }}</td>
                <td class="py-3 px-4">
                  <span class="px-3 py-1 rounded-full text-sm font-medium
                    @if($record->status === 'present') bg-green-500/20 text-green-300
                    @elseif($record->status === 'absent') bg-red-500/20 text-red-300
                    @elseif($record->status === 'late') bg-yellow-500/20 text-yellow-300
                    @elseif($record->status === 'el') bg-orange-500/20 text-orange-300
                    @elseif($record->status === 'on leave') bg-blue-500/20 text-blue-300
                    @elseif($record->status === 'half day') bg-purple-500/20 text-purple-300
                    @else bg-gray-500/20 text-gray-300 @endif">
                    {{ ucfirst($record->status) }}
                  </span>
                </td>
                <td class="py-3 px-4 text-sm">
                  @if($record->status === 'present' && $record->check_in_time)
                    <span class="text-green-300">{{ substr($record->check_in_time, 0, 5) }}</span>
                  @else
                    <span class="text-gray-500">-</span>
                  @endif
                </td>
                <td class="py-3 px-4 text-sm">
                  @if($record->status === 'present' && $record->check_out_time)
                    <span class="text-blue-300">{{ substr($record->check_out_time, 0, 5) }}</span>
                  @else
                    <span class="text-gray-500">-</span>
                  @endif
                </td>
                <td class="py-3 px-4 text-sm">
                  @if($record->status === 'present' && $record->check_in_time && $record->check_out_time)
                    <span class="text-purple-300">{{ \Carbon\Carbon::createFromFormat('H:i:s', $record->check_in_time)->diffInHours(\Carbon\Carbon::createFromFormat('H:i:s', $record->check_out_time)) }} hrs</span>
                  @else
                    <span class="text-gray-500">-</span>
                  @endif
                </td>
                <td class="py-3 px-4 text-sm text-gray-400">
                  {{ $record->remarks ? $record->remarks : '-' }}
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="py-6 text-center text-gray-400">
                  <i class="fas fa-inbox mr-2"></i>No attendance records yet
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

  </main>

</body>

<script>
  // Fetch and display leave status update badge
  async function loadNotificationBadge() {
    try {
      const response = await fetch('{{ route("staff.leave.notifications") }}');
      const data = await response.json();
      
      const badge = document.getElementById('notificationBadge');
      
      if (data.count > 0) {
        badge.textContent = data.count;
        badge.classList.remove('hidden');
      } else {
        badge.classList.add('hidden');
      }
    } catch (error) {
      console.error('Failed to load notification badge:', error);
    }
  }
  
  // Load badge on page load and refresh every 30 seconds
  loadNotificationBadge();
  setInterval(loadNotificationBadge, 30000);
</script>

</html>