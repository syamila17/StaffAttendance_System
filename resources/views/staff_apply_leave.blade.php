<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Apply Leave</title>
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
        <p class="text-xs text-gray-300">{{ $staffName ?? 'Staff' }}</p>
      </div>
    </div>

    <nav class="mt-8 space-y-4">
      <a href="{{ route('staff.dashboard') }}" class="block px-4 py-2 rounded-lg hover:bg-white/20"><i class="fas fa-home mr-2"></i>Dashboard</a>
      <a href="{{ route('attendance.show') }}" class="block px-4 py-2 rounded-lg hover:bg-white/20"><i class="fas fa-calendar-check mr-2"></i>Attendance</a>
      <a href="{{ route('staff.profile') }}" class="block px-4 py-2 rounded-lg hover:bg-white/20"><i class="fas fa-user-circle mr-2"></i>Profile</a>
      <a href="{{ route('staff.apply-leave') }}" class="block px-4 py-2 rounded-lg bg-white/20"><i class="fas fa-calendar-times mr-2"></i>Apply Leave</a>
      <a href="{{ route('staff.leave.status') }}" class="block px-4 py-2 rounded-lg hover:bg-white/20"><i class="fas fa-list-check mr-2"></i>Leave Status</a>
      <a href="{{ route('staff.logout') }}" 
        class="block px-4 py-2 rounded-lg hover:bg-white/20 flex items-center">
        <i class="fas fa-sign-out-alt mr-2"></i> Logout
      </a>
    </nav>
  </aside>

  <!-- Main Content -->
  <main class="flex-1 p-10 overflow-y-auto">
    <div class="max-w-2xl">
      <h1 class="text-3xl font-bold mb-2"><i class="fas fa-calendar-times mr-2"></i>Apply for Leave</h1>
      <p class="text-gray-300 mb-8">Submit your leave request for admin approval</p>

      @if(session('success'))
        <div class="bg-green-500/20 border border-green-500 text-green-300 px-4 py-3 rounded-lg mb-6">
          <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
      @endif

      @if($errors->any())
        <div class="bg-red-500/20 border border-red-500 text-red-300 px-4 py-3 rounded-lg mb-6">
          <i class="fas fa-exclamation-circle mr-2"></i>
          <strong>Please fix the following errors:</strong>
          <ul class="mt-2 list-disc list-inside">
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <!-- Leave Request Form -->
      <div class="bg-white/10 p-8 rounded-xl border border-white/20 shadow-lg">
        <form method="POST" action="{{ route('staff.leave.store') }}" class="space-y-6">
          @csrf

          <!-- Leave Type -->
          <div>
            <label class="block text-sm font-semibold mb-3"><i class="fas fa-list mr-2"></i>Leave Type *</label>
            <select name="leave_type" required class="w-full px-4 py-3 bg-white/10 border border-white/30 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-green-400 transition">
              <option value="" style="color: black;">-- Select Leave Type --</option>
              <option value="Annual Leave" style="color: black;">Annual Leave</option>
              <option value="Sick Leave" style="color: black;">Sick Leave</option>
              <option value="Emergency Leave" style="color: black;">Emergency Leave</option>
              <option value="Personal Leave" style="color: black;">Personal Leave</option>
              <option value="Other" style="color: black;">Other</option>
            </select>
            @error('leave_type')
              <p class="text-red-300 text-sm mt-1">{{ $message }}</p>
            @enderror
          </div>

          <!-- From Date -->
          <div>
            <label class="block text-sm font-semibold mb-3"><i class="fas fa-calendar mr-2"></i>From Date *</label>
            <input type="date" name="from_date" value="{{ old('from_date') }}" required 
              class="w-full px-4 py-3 bg-white/10 border border-white/30 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-green-400 transition">
            @error('from_date')
              <p class="text-red-300 text-sm mt-1">{{ $message }}</p>
            @enderror
          </div>

          <!-- To Date -->
          <div>
            <label class="block text-sm font-semibold mb-3"><i class="fas fa-calendar mr-2"></i>To Date *</label>
            <input type="date" name="to_date" value="{{ old('to_date') }}" required 
              class="w-full px-4 py-3 bg-white/10 border border-white/30 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-green-400 transition">
            @error('to_date')
              <p class="text-red-300 text-sm mt-1">{{ $message }}</p>
            @enderror
          </div>

          <!-- Reason -->
          <div>
            <label class="block text-sm font-semibold mb-3"><i class="fas fa-pen-fancy mr-2"></i>Reason (Optional)</label>
            <textarea name="reason" rows="5" placeholder="Provide details about your leave request..." 
              class="w-full px-4 py-3 bg-white/10 border border-white/30 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-green-400 transition resize-none">{{ old('reason') }}</textarea>
            @error('reason')
              <p class="text-red-300 text-sm mt-1">{{ $message }}</p>
            @enderror
          </div>

          <!-- Submit Button -->
          <div class="flex gap-4 pt-4">
            <button type="submit" class="px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 rounded-lg font-semibold transition flex items-center">
              <i class="fas fa-paper-plane mr-2"></i>Submit Request
            </button>
            <a href="{{ route('staff.dashboard') }}" class="px-6 py-3 bg-white/20 hover:bg-white/30 rounded-lg font-semibold transition flex items-center">
              <i class="fas fa-times mr-2"></i>Cancel
            </a>
          </div>
        </form>
      </div>
    </div>
  </main>

</body>
</html>
