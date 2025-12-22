<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Staff Profile</title>
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
      <a href="{{ route('staff.profile') }}" class="block px-4 py-2 rounded-lg bg-white/20"><i class="fas fa-user-circle mr-2"></i>Profile</a>
      <a href="{{ route('staff.apply-leave') }}" class="block px-4 py-2 rounded-lg hover:bg-white/20"><i class="fas fa-calendar-times mr-2"></i>Apply Leave</a>
      <a href="{{ route('staff.leave.status') }}" class="block px-4 py-2 rounded-lg hover:bg-white/20"><i class="fas fa-list-check mr-2"></i>Leave Status</a>
      <a href="{{ route('staff.logout') }}" 
        class="block px-4 py-2 rounded-lg hover:bg-white/20 flex items-center">
        <i class="fas fa-sign-out-alt mr-2"></i> Logout
      </a>
    </nav>
  </aside>

  <!-- Main Content -->
  <main class="flex-1 p-10 overflow-y-auto">
    <h1 class="text-3xl font-bold mb-8">My Profile</h1>

    @if(session('success'))
      <div class="mb-6 border border-green-400 bg-green-500/20 text-green-200 px-4 py-3 rounded-lg">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
      </div>
    @endif

    <form method="POST" action="{{ route('staff.profile.update') }}" enctype="multipart/form-data" class="space-y-8">
      @csrf

      <!-- Profile Image Section at Top -->
      <div class="bg-white/10 p-8 rounded-xl shadow-lg border border-white/20">
        <h2 class="text-xl font-semibold mb-4 flex items-center">
          <i class="fas fa-image mr-2"></i>Profile Picture
        </h2>
        
        <div class="flex flex-col items-center gap-6">
          <!-- Current Image Display -->
          <div class="relative">
            @if(!empty($profile->profile_image))
              <img src="{{ asset('storage/' . $profile->profile_image) }}" alt="Profile Image" 
                   class="w-32 h-32 rounded-full object-cover border-4 border-white/30 shadow-lg">
            @else
              <div class="w-32 h-32 rounded-full bg-white/20 border-4 border-white/30 flex items-center justify-center">
                <i class="fas fa-user text-6xl text-white/50"></i>
              </div>
            @endif
          </div>

          <!-- File Upload -->
          <div class="w-full">
            <label class="block text-sm font-medium mb-2">
              <i class="fas fa-cloud-upload-alt mr-2"></i>Upload New Image
            </label>
            <input type="file" name="profile_image" accept="image/*" 
                   class="w-full px-4 py-2 rounded-lg bg-white/20 border border-white/30 text-white file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:bg-red-600 file:text-white hover:file:bg-red-700">
            <p class="text-xs text-gray-300 mt-2">PNG, JPG, GIF (Max 2MB)</p>
          </div>
        </div>
      </div>

      <!-- Personal Information Section -->
      <div class="bg-white/10 p-8 rounded-xl shadow-lg border border-white/20">
        <h2 class="text-xl font-semibold mb-6 flex items-center">
          <i class="fas fa-id-card mr-2"></i>Personal Information
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium mb-2">
              <i class="fas fa-id-badge mr-2"></i>Staff ID
            </label>
            <div class="w-full px-4 py-2 rounded-lg bg-white/10 border border-white/20 text-white font-semibold">
              {{ $staffId ?? 'N/A' }}
            </div>
            <p class="text-xs text-gray-400 mt-2">This field cannot be changed</p>
          </div>

          <div>
            <label class="block text-sm font-medium mb-2">
              <i class="fas fa-user mr-2"></i>Full Name
            </label>
            <input type="text" name="full_name" value="{{ $profile->full_name ?? '' }}" 
                   class="w-full px-4 py-2 rounded-lg bg-white/20 border border-white/30 text-white placeholder-gray-400 focus:outline-none focus:border-white/50">
          </div>

          <div>
            <label class="block text-sm font-medium mb-2">
              <i class="fas fa-envelope mr-2"></i>Email
            </label>
            <input type="email" name="email" value="{{ $profile->email ?? '' }}" 
                   class="w-full px-4 py-2 rounded-lg bg-white/20 border border-white/30 text-white placeholder-gray-400 focus:outline-none focus:border-white/50">
          </div>

          <div>
            <label class="block text-sm font-medium mb-2">
              <i class="fas fa-phone mr-2"></i>Phone Number
            </label>
            <input type="text" name="phone_number" value="{{ $profile->phone_number ?? '' }}" 
                   class="w-full px-4 py-2 rounded-lg bg-white/20 border border-white/30 text-white placeholder-gray-400 focus:outline-none focus:border-white/50">
          </div>

          <div>
            <label class="block text-sm font-medium mb-2">
              <i class="fas fa-building mr-2"></i>Department
            </label>
            <div class="w-full px-4 py-2 rounded-lg bg-white/10 border border-white/20 text-white font-semibold">
              {{ $department?->department_name ?? $profile->department ?? 'N/A' }}
            </div>
            <p class="text-xs text-gray-400 mt-2">Read-only (managed by HR)</p>
          </div>

          <div>
            <label class="block text-sm font-medium mb-2">
              <i class="fas fa-users mr-2"></i>Team
            </label>
            <div class="w-full px-4 py-2 rounded-lg bg-white/10 border border-white/20 text-white font-semibold">
              {{ $team?->team_name ?? 'N/A' }}
            </div>
            <p class="text-xs text-gray-400 mt-2">Read-only (managed by HR)</p>
          </div>

          <div class="md:col-span-2">
            <label class="block text-sm font-medium mb-2">
              <i class="fas fa-map-marker-alt mr-2"></i>Address
            </label>
            <textarea name="address" rows="3" placeholder="Enter your address..."
                      class="w-full px-4 py-2 rounded-lg bg-white/20 border border-white/30 text-white placeholder-gray-400 focus:outline-none focus:border-white/50">{{ $profile->address ?? '' }}</textarea>
          </div>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="flex gap-4 justify-center">
        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-8 py-3 rounded-lg font-semibold flex items-center gap-2 transition">
          <i class="fas fa-save"></i>Save Changes
        </button>
        <a href="{{ route('staff.dashboard') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-8 py-3 rounded-lg font-semibold flex items-center gap-2 transition">
          <i class="fas fa-times"></i>Cancel
        </a>
      </div>
    </form>
  </main>

</body>
</html>
