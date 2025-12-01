<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard - Attendance System</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-900 text-white font-sans">

  <div class="flex h-screen">
    <!-- Sidebar -->
    <aside class="w-64 bg-gradient-to-b from-orange-600 to-orange-700 text-white shadow-lg">
      <!-- Sidebar Header -->
      <div class="p-6 border-b border-orange-600">
        <div class="flex items-center gap-3">
          <div class="bg-white/20 w-12 h-12 rounded-lg flex items-center justify-center">
            <i class="fas fa-user-shield text-2xl text-white"></i>
          </div>
          <div>
            <h1 class="text-xl font-bold text-white">Admin Panel</h1>
            <p class="text-xs text-gray-200 font-medium">Attendance System</p>
          </div>
        </div>
      </div>

      <!-- Navigation -->
      <nav class="p-4 space-y-2">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg bg-white/20 hover:bg-white/30 transition">
          <i class="fas fa-home w-5"></i>
          <span>Dashboard</span>
        </a>
        <a href="{{ route('admin.staff.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/20 transition">
          <i class="fas fa-users w-5"></i>
          <span>Staff Management</span>
        </a>
        <a href="{{ route('admin.attendance') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/20 transition">
          <i class="fas fa-calendar-check w-5"></i>
          <span>Attendance</span>
        </a>
        <a href="{{ route('admin.attendance.report') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/20 transition">
          <i class="fas fa-chart-bar w-5"></i>
          <span>Reports</span>
        </a>
        <a href="{{ route('admin.departments') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/20 transition">
          <i class="fas fa-building w-5"></i>
          <span>Departments</span>
        </a>
        <a href="{{ route('admin.leave.requests') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/20 transition relative">
          <i class="fas fa-calendar-times w-5"></i>
          <span>Leave Requests</span>
          <span id="dashboardLeaveBadge" class="absolute top-2 right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center hidden font-bold">0</span>
        </a>
        <a href="{{ route('admin.logout') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/20 transition">
          <i class="fas fa-sign-out-alt w-5"></i>
          <span>Logout</span>
        </a>
      </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto p-8">
      <h1 class="text-3xl font-bold text-white mb-2">Welcome, {{ Session::get('admin_name') ?? 'Admin' }}</h1>
      <p class="text-gray-400 mb-8">Manage your staff, attendance, and reports from here.</p>

      <!-- Dashboard Stats -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-gray-800 p-6 rounded-lg shadow-md border-l-4 border-orange-500">
          <h3 class="text-gray-400 text-sm font-semibold">Total Staff</h3>
          <p class="text-3xl font-bold text-white mt-2">156</p>
        </div>
        <div class="bg-gray-800 p-6 rounded-lg shadow-md border-l-4 border-orange-500">
          <h3 class="text-gray-400 text-sm font-semibold">Present Today</h3>
          <p class="text-3xl font-bold text-white mt-2">142</p>
        </div>
        <div class="bg-gray-800 p-6 rounded-lg shadow-md border-l-4 border-orange-500">
          <h3 class="text-gray-400 text-sm font-semibold">On Leave</h3>
          <p class="text-3xl font-bold text-white mt-2">8</p>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="bg-gray-800 p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-bold text-white mb-4">Quick Actions</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          <a href="{{ route('admin.staff.index') }}" class="p-4 bg-gray-700 border border-gray-600 rounded-lg hover:bg-gray-650 transition text-center">
            <i class="fas fa-user-plus text-2xl text-orange-500 mb-2"></i>
            <p class="text-sm font-semibold text-gray-300">Add Staff</p>
          </a>
          <a href="{{ route('admin.attendance') }}" class="p-4 bg-gray-700 border border-gray-600 rounded-lg hover:bg-gray-650 transition text-center">
            <i class="fas fa-clipboard-list text-2xl text-orange-500 mb-2"></i>
            <p class="text-sm font-semibold text-gray-300">View Attendance</p>
          </a>
          <a href="{{ route('admin.departments') }}" class="p-4 bg-gray-700 border border-gray-600 rounded-lg hover:bg-gray-650 transition text-center">
            <i class="fas fa-sitemap text-2xl text-orange-500 mb-2"></i>
            <p class="text-sm font-semibold text-gray-300">Departments</p>
          </a>
          <a href="{{ route('admin.leave.requests') }}" class="p-4 bg-gray-700 border border-gray-600 rounded-lg hover:bg-gray-650 transition text-center">
            <i class="fas fa-file-pdf text-2xl text-orange-500 mb-2"></i>
            <p class="text-sm font-semibold text-gray-300">Leave Requests</p>
          </a>
        </div>
      </div>
    </main>
  </div>

  <script>
    // Function to update pending leave count badge on dashboard
    async function updateDashboardLeaveNotificationBadge() {
      try {
        const response = await fetch('{{ route("admin.leave.pending-count") }}');
        const data = await response.json();
        
        const dashboardBadge = document.getElementById('dashboardLeaveBadge');
        
        if (data.count > 0) {
          dashboardBadge.textContent = data.count;
          dashboardBadge.classList.remove('hidden');
        } else {
          dashboardBadge.classList.add('hidden');
        }
      } catch (error) {
        console.error('Failed to update dashboard notification badge:', error);
      }
    }
    
    // Load badge on page load only
    updateDashboardLeaveNotificationBadge();
  </script>

</body>
</html>
