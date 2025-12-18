<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Staff Management</title>
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
      <div class="max-w-7xl">
        <div class="flex justify-between items-center mb-8">
          <div>
            <h1 class="text-4xl font-bold mb-2">Staff Management</h1>
            <p class="text-gray-400">Manage all staff members in your organization</p>
          </div>
          <a href="{{ route('admin.staff.create') }}" class="px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 rounded-lg transition shadow-lg font-semibold">
            <i class="fas fa-plus mr-2"></i>Add New Staff
          </a>
        </div>

        @if(session('success'))
          <div class="bg-green-500/20 border border-green-500 text-green-300 px-4 py-3 rounded-lg mb-6">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
          </div>
        @endif

        <!-- Search & Filter -->
        <div class="mb-8 flex gap-4">
          <form method="GET" action="{{ route('admin.staff.index') }}" class="flex gap-4 flex-1">
            <input type="text" name="search" placeholder="Search by name or email..." 
              class="flex-1 px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-orange-500 transition">
            <button type="submit" class="px-6 py-2 bg-orange-600 hover:bg-orange-700 rounded-lg transition font-semibold">
              <i class="fas fa-search mr-2"></i>Search
            </button>
          </form>
        </div>

        <!-- Staff Table -->
        <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-x-auto shadow-xl">
          <table class="w-full min-w-max">
            <thead class="bg-gray-700 border-b border-gray-600">
              <tr>
                <th class="px-4 py-4 text-left text-lg font-semibold">Staff Name</th>
                <th class="px-4 py-4 text-left text-lg font-semibold">Email</th>
                <th class="px-4 py-4 text-left text-lg font-semibold">Department</th>
                <th class="px-4 py-4 text-left text-lg font-semibold">Team</th>
                <th class="px-4 py-4 text-left text-lg font-semibold">Created</th>
                <th class="px-4 py-4 text-center text-lg font-semibold">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($staff as $person)
                <tr class="border-b border-gray-700 hover:bg-gray-750 transition">
                  <td class="px-4 py-4 whitespace-nowrap text-base">{{ $person->staff_name }}</td>
                  <td class="px-4 py-4 whitespace-nowrap text-base">{{ $person->staff_email }}</td>
                  <td class="px-4 py-4 whitespace-nowrap">
                    @if($person->department)
                      <span class="px-2 py-1 bg-purple-500/20 text-purple-300 rounded text-base">{{ $person->department->department_name }}</span>
                    @else
                      <span class="text-gray-400 text-base">-</span>
                    @endif
                  </td>
                  <td class="px-4 py-4 whitespace-nowrap">
                    @if($person->team)
                      <span class="px-2 py-1 bg-blue-500/20 text-blue-300 rounded text-base">{{ $person->team->team_name }}</span>
                    @else
                      <span class="text-gray-400 text-base">-</span>
                    @endif
                  </td>
                  <td class="px-4 py-4 whitespace-nowrap text-base text-gray-400">
                    @if($person->created_at)
                      @if(is_string($person->created_at))
                        {{ \Carbon\Carbon::parse($person->created_at)->format('M d, Y') }}
                      @else
                        {{ $person->created_at->format('M d, Y') }}
                      @endif
                    @else
                      -
                    @endif
                  </td>
                  <td class="px-4 py-4 whitespace-nowrap text-center">
                    <div class="flex gap-2 justify-center">
                      <a href="{{ route('admin.staff.edit', $person->staff_id) }}" 
                        class="px-3 py-2 bg-blue-600 hover:bg-blue-700 rounded text-base transition" title="Edit">
                        <i class="fas fa-edit"></i>
                      </a>
                      <form method="POST" action="{{ route('admin.staff.destroy', $person->staff_id) }}" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-3 py-2 bg-red-600 hover:bg-red-700 rounded text-base transition" onclick="return confirm('Are you sure?')" title="Delete">
                          <i class="fas fa-trash"></i>
                        </button>
                      </form>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="px-4 py-8 text-center text-gray-400 text-base">
                    <i class="fas fa-inbox text-3xl mb-3"></i>
                    <p>No staff members found</p>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        @if($staff->hasPages())
          <div class="mt-8">
            {{ $staff->links() }}
          </div>
        @endif
      </div>
    </main>
  </div>

</body>
</html>
