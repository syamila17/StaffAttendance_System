<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Departments</title>
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
        <a href="{{ route('admin.attendance') }}" class="block px-4 py-2 rounded-lg hover:bg-white/20 transition">
          <i class="fas fa-calendar-check mr-2"></i>Attendance
        </a>
        <a href="{{ route('admin.attendance.report') }}" class="block px-4 py-2 rounded-lg hover:bg-white/20 transition">
          <i class="fas fa-chart-bar mr-2"></i>Reports
        </a>
        <a href="{{ route('admin.departments') }}" class="block px-4 py-2 rounded-lg bg-white/30">
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
      <div class="max-w-7xl">
        <h1 class="text-4xl font-bold mb-2">Departments</h1>
        <p class="text-gray-400 mb-8">View staff members organized by departments and teams</p>

        @if($departments->isEmpty())
          <div class="bg-gray-800 rounded-lg border border-gray-700 p-8 text-center">
            <i class="fas fa-inbox text-4xl text-gray-600 mb-3"></i>
            <p class="text-gray-400">No departments found</p>
          </div>
        @else
          @foreach($departments as $department)
            <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden shadow-xl mb-6">
              <!-- Department Header -->
              <div class="bg-gradient-to-r from-orange-600 to-orange-700 p-6">
                <h2 class="text-2xl font-bold flex items-center">
                  <i class="fas fa-building mr-3"></i>{{ $department->department_name }}
                </h2>
                <p class="text-orange-100 text-sm mt-2">Department Code: {{ $department->department_code }}</p>
              </div>

              <!-- Department Content -->
              <div class="p-6">
                @if($department->teams->isEmpty())
                  <p class="text-gray-400 text-center py-4">No teams in this department</p>
                @else
                  @foreach($department->teams as $team)
                    <div class="mb-8 last:mb-0">
                      <!-- Team Header -->
                      <div class="flex items-center gap-3 mb-4 pb-3 border-b border-gray-700">
                        <i class="fas fa-users text-blue-400"></i>
                        <h3 class="text-lg font-semibold">{{ $team->team_name }}</h3>
                        <span class="ml-auto bg-blue-500/20 text-blue-300 px-3 py-1 rounded-full text-sm">
                          {{ $team->staff->count() }} staff
                        </span>
                      </div>

                      <!-- Team Staff Table -->
                      @if($team->staff->isEmpty())
                        <p class="text-gray-500 text-sm italic">No staff members in this team</p>
                      @else
                        <div class="overflow-x-auto">
                          <table class="w-full text-sm">
                            <thead>
                              <tr class="border-b border-gray-700">
                                <th class="text-left py-2 px-4 text-gray-400">Name</th>
                                <th class="text-left py-2 px-4 text-gray-400">Email</th>
                                <th class="text-left py-2 px-4 text-gray-400">ID</th>
                              </tr>
                            </thead>
                            <tbody>
                              @foreach($team->staff as $staff)
                                <tr class="border-b border-gray-700 hover:bg-gray-750 transition">
                                  <td class="py-3 px-4">{{ $staff->staff_name }}</td>
                                  <td class="py-3 px-4">{{ $staff->staff_email }}</td>
                                  <td class="py-3 px-4 text-gray-400 text-xs">{{ $staff->staff_id }}</td>
                                </tr>
                              @endforeach
                            </tbody>
                          </table>
                        </div>
                      @endif
                    </div>
                  @endforeach
                @endif
              </div>
            </div>
          @endforeach
        @endif
      </div>
    </main>
  </div>

</body>
</html>
