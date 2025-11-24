<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard - Attendance System</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    /* Sidebar gradient & subtle texture */
    .sidebar {
      background: linear-gradient(180deg, #ff4500 0%, #b22222 100%);
      background-blend-mode: overlay;
      background-image: url('https://www.transparenttextures.com/patterns/asfalt-light.png');
    }

    

    /* Transition effect */
    .sidebar-transition {
      transition: margin-left 0.3s ease-in-out, width 0.3s ease-in-out;
    }
  </style>
</head>
<body class="bg-gray-100 font-sans">

  <!-- Sidebar -->
  <div id="sidebar" class="sidebar fixed left-0 top-0 h-full w-64 text-white shadow-xl z-50 sidebar-transition bg-gradient-to-b from-orange-600 via-orange-500 to-amber-400">
    <div class="p-6 border-b border-red-700/50">
      <div class="flex items-center gap-3">
        <div class="bg-white/20 w-12 h-12 rounded-lg flex items-center justify-center">
          <i class="fas fa-user-shield text-2xl text-white"></i>
        </div>
        <div>
          <h1 class="text-xl font-bold text-white">Admin Panel</h1>
          <p class="text-xs text-gray-200 font-medium">Attendance System</p>
          // <h2 class= "text-sm text-white/90 mt-1">Welcome, {{ $admin_name ?? $admin_email }}</h2>
        </div>
      </div>
    </div>

   <!-- Navigation -->
<nav class="p-4 min-h-screen bg-gradient-to-b from-orange-600 via-orange-500 to-orange-400">
  <ul class="space-y-2 text-white">
    <li>
      <a href="#" onclick="showPage('dashboard')" 
         class="flex items-center gap-3 px-4 py-3 rounded-lg bg-white/20 hover:bg-white/30 transition">
        <i class="fas fa-home w-5"></i>
        <span>Dashboard</span>
      </a>
    </li>
    <li>
      <a href="#" onclick="showPage('staff')" 
         class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/20 transition">
        <i class="fas fa-users w-5"></i>
        <span>Staff Management</span>
      </a>
    </li>
    <li>
      <a href="#" onclick="showPage('attendance')" 
         class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/20 transition">
        <i class="fas fa-calendar-check w-5"></i>
        <span>Attendance</span>
      </a>
    </li>
    <li>
      <a href="#" onclick="showPage('leaves')" 
         class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/20 transition">
        <i class="fas fa-calendar-times w-5"></i>
        <span>Leave Requests</span>
        <span class="ml-auto bg-white/40 px-2 py-1 rounded-full text-xs text-white">3</span>
      </a>
    </li>
    <li>
      <a href="#" onclick="showPage('reports')" 
         class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/20 transition">
        <i class="fas fa-chart-bar w-5"></i>
        <span>Reports</span>
      </a>
    </li>
    <li>
      <a href="#" onclick="showPage('departments')" 
         class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/20 transition">
        <i class="fas fa-building w-5"></i>
        <span>Departments</span>
      </a>
    </li>
    <li>
      <a href="#" onclick="showPage('settings')" 
         class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/20 transition">
        <i class="fas fa-cog w-5"></i>
        <span>Settings</span>
      </a>
    </li>
  </ul>
</nav>


    <!-- Bottom Profile -->
    <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-red-800/50">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
          <i class="fas fa-user text-white"></i>
        </div>
        <div class="flex-1">
          <p class="font-medium text-sm">Admin User</p>
          <p class="text-xs text-gray-200">Administrator</p>
        </div>
        <a href="{{ route('admin.logout') }}" class="text-gray-200 hover:text-white">
          <i class="fas fa-sign-out-alt"></i>
        </a>
      </div>
    </div>
  </div>

  <!-- Main Content -->
  <div id="main-content" class="ml-64 sidebar-transition">
    <!-- Top Navbar -->
    <div class="bg-gradient-to-r from-red-500 to-orange-500 text-white shadow-md sticky top-0 z-40">
      <div class="flex items-center justify-between p-4">
        <div class="flex items-center gap-3">
          <button onclick="toggleSidebar()" class="text-white hover:text-gray-200 lg:hidden">
            <i class="fas fa-bars text-xl"></i>
          </button>
          <div>
            <h2 class="text-xl font-bold">Dashboard Overview</h2>
            <p class="text-sm opacity-90">Welcome back, Admin!</p>
          </div>
        </div>

        <div class="flex items-center gap-4">
          <button class="relative text-white hover:text-gray-200">
            <i class="fas fa-bell text-xl"></i>
            <span class="absolute -top-1 -right-1 bg-white text-red-600 text-xs w-5 h-5 rounded-full flex items-center justify-center">5</span>
          </button>
          <div class="flex items-center gap-2">
            <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
              <i class="fas fa-user text-white"></i>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Content -->
    <div class="p-6">
      <h1 class="text-2xl font-bold text-gray-800 mb-4">Welcome, {{ Session::get('admin_name') }}</h1>
      <p class="text-gray-600 mb-8">You can manage staff, attendance, and reports from here.</p>

      <!-- Example cards (you can keep your Chart.js section here) -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-orange-500">
          <h3 class="text-gray-600 text-sm font-semibold">Total Staff</h3>
          <p class="text-3xl font-bold text-gray-800 mt-2">156</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-red-500">
          <h3 class="text-gray-600 text-sm font-semibold">Present Today</h3>
          <p class="text-3xl font-bold text-gray-800 mt-2">142</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-yellow-500">
          <h3 class="text-gray-600 text-sm font-semibold">On Leave</h3>
          <p class="text-3xl font-bold text-gray-800 mt-2">8</p>
        </div>
      </div>
    </div>
  </div>

  <script>
    let sidebarOpen = true;
    function toggleSidebar() {
      const sidebar = document.getElementById('sidebar');
      const mainContent = document.getElementById('main-content');
      if (sidebarOpen) {
        sidebar.style.marginLeft = '-16rem';
        mainContent.style.marginLeft = '0';
      } else {
        sidebar.style.marginLeft = '0';
        mainContent.style.marginLeft = '16rem';
      }
      sidebarOpen = !sidebarOpen;
    }

    function showPage(page) {
      alert(`Navigating to ${page} page...`);
    }
  </script>
</body>
</html>
