<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Attendance Reports</title>
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
        <a href="{{ route('admin.attendance') }}" class="block px-4 py-2 rounded-lg hover:bg-white/20 transition">
          <i class="fas fa-calendar-check mr-2"></i>Attendance
        </a>
        <a href="{{ route('admin.attendance.report') }}" class="block px-4 py-2 rounded-lg bg-white/30">
          <i class="fas fa-chart-bar mr-2"></i>Reports
        </a>
        <a href="{{ route('admin.logout') }}" class="block px-4 py-2 rounded-lg hover:bg-white/20 transition">
          <i class="fas fa-sign-out-alt mr-2"></i>Logout
        </a>
      </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto p-8">
      <div class="max-w-6xl">
        <h1 class="text-4xl font-bold mb-2">Attendance Reports</h1>
        <p class="text-gray-400 mb-8">View attendance statistics and trends</p>

        <!-- Filters -->
        <div class="bg-gray-800 rounded-lg border border-gray-700 p-6 mb-8">
          <h2 class="text-lg font-semibold mb-4">Filters</h2>
          <form method="GET" action="{{ route('admin.attendance.report') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
              <label class="block text-sm mb-2">Start Date</label>
              <input type="date" name="start_date" value="{{ $startDate }}" 
                class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded">
            </div>
            <div>
              <label class="block text-sm mb-2">End Date</label>
              <input type="date" name="end_date" value="{{ $endDate }}" 
                class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded">
            </div>
            <div>
              <label class="block text-sm mb-2">Staff Member</label>
              <select name="staff_id" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded">
                <option value="">All Staff</option>
                @foreach($staff as $person)
                  <option value="{{ $person->staff_id }}">{{ $person->staff_name }}</option>
                @endforeach
              </select>
            </div>
            <div class="flex items-end">
              <button type="submit" class="w-full px-4 py-2 bg-orange-600 hover:bg-orange-700 rounded transition">
                <i class="fas fa-search mr-2"></i>Filter
              </button>
            </div>
          </form>
        </div>

        <!-- Summary Stats -->
        <div class="grid grid-cols-5 gap-4 mb-8">
          <div class="bg-gray-800 p-4 rounded-lg border border-gray-700">
            <p class="text-gray-400 text-sm">Total Records</p>
            <p class="text-3xl font-bold">{{ $summary['total_records'] }}</p>
          </div>
          <div class="bg-green-900/30 p-4 rounded-lg border border-green-700">
            <p class="text-green-300 text-sm">Present</p>
            <p class="text-3xl font-bold text-green-300">{{ $summary['present'] }}</p>
          </div>
          <div class="bg-red-900/30 p-4 rounded-lg border border-red-700">
            <p class="text-red-300 text-sm">Absent</p>
            <p class="text-3xl font-bold text-red-300">{{ $summary['absent'] }}</p>
          </div>
          <div class="bg-yellow-900/30 p-4 rounded-lg border border-yellow-700">
            <p class="text-yellow-300 text-sm">Late</p>
            <p class="text-3xl font-bold text-yellow-300">{{ $summary['late'] }}</p>
          </div>
          <div class="bg-blue-900/30 p-4 rounded-lg border border-blue-700">
            <p class="text-blue-300 text-sm">Leave</p>
            <p class="text-3xl font-bold text-blue-300">{{ $summary['leave'] }}</p>
          </div>
        </div>

        <!-- Attendance Table -->
        <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
          <table class="w-full">
            <thead class="bg-gray-700 border-b border-gray-600">
              <tr>
                <th class="px-6 py-4 text-left">Date</th>
                <th class="px-6 py-4 text-left">Staff Name</th>
                <th class="px-6 py-4 text-left">Status</th>
                <th class="px-6 py-4 text-left">Check-in</th>
                <th class="px-6 py-4 text-left">Check-out</th>
                <th class="px-6 py-4 text-left">Duration</th>
              </tr>
            </thead>
            <tbody>
              @forelse($attendanceRecords as $record)
                <tr class="border-b border-gray-700 hover:bg-gray-750 transition">
                  <td class="px-6 py-4">{{ $record->attendance_date->format('Y-m-d') }}</td>
                  <td class="px-6 py-4">{{ $record->staff->staff_name }}</td>
                  <td class="px-6 py-4">
                    <span class="px-3 py-1 rounded-full text-sm capitalize
                      @if($record->status === 'present') bg-green-500/20 text-green-300
                      @elseif($record->status === 'late') bg-yellow-500/20 text-yellow-300
                      @elseif($record->status === 'leave') bg-blue-500/20 text-blue-300
                      @else bg-red-500/20 text-red-300 @endif">
                      {{ $record->status }}
                    </span>
                  </td>
                  <td class="px-6 py-4">{{ $record->check_in_time ?? '-' }}</td>
                  <td class="px-6 py-4">{{ $record->check_out_time ?? '-' }}</td>
                  <td class="px-6 py-4">
                    @if($record->check_in_time && $record->check_out_time)
                      {{ \Carbon\Carbon::createFromFormat('H:i:s', $record->check_in_time)->diffInHours(\Carbon\Carbon::createFromFormat('H:i:s', $record->check_out_time)) }} hrs
                    @else
                      -
                    @endif
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="px-6 py-8 text-center text-gray-400">
                    No records found for the selected period
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </main>
  </div>

</body>
</html>
