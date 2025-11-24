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
      <div class="bg-white/20 w-12 h-12 rounded-lg flex items-center justify-center">
        <i class="fas fa-user text-2xl text-white"></i>
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
      <a href="{{ route('staff.logout') }}" 
        class="block px-4 py-2 rounded-lg hover:bg-white/20 flex items-center">
        <i class="fas fa-sign-out-alt mr-2"></i> Logout
      </a>

    </nav>
  </aside>

  <!-- Main Content -->
  <main class="flex-1 p-10 overflow-y-auto">
    <h1 class="text-3xl font-bold mb-6">Welcome, {{ $staffName}}!</h1>
    <p class="text-gray-200 text-lg">Email: {{$staffEmail}}.</p>

    <!-- Example Dashboard Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mt-8">
      <div class="bg-white/10 p-6 rounded-xl shadow-lg border border-white/20">
        <h3 class="text-lg font-semibold mb-2">Today's Attendance</h3>
        <p class="text-gray-300">Not yet recorded</p>
      </div>

      <div class="bg-white/10 p-6 rounded-xl shadow-lg border border-white/20">
        <h3 class="text-lg font-semibold mb-2">Total Days Present</h3>
        <p class="text-gray-300">15 Days</p>
      </div>

      <div class="bg-white/10 p-6 rounded-xl shadow-lg border border-white/20">
        <h3 class="text-lg font-semibold mb-2">Next Schedule</h3>
        <p class="text-gray-300">No upcoming schedule</p>
      </div>
    </div>
  </main>

</body>
</html>
