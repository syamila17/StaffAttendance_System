<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Staff Profile</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-red-800 to-red-950 text-white min-h-screen p-10">

  <div class="max-w-3xl mx-auto bg-white/10 p-8 rounded-xl shadow-lg border border-white/20">
    <h1 class="text-2xl font-bold mb-6 text-center">My Profile</h1>

    @if(session('success'))
      <div class="border border-green-400 bg-green-50 text-green-700 px-4 py-3 rounded mb-4 text-center">
        {{ session('success') }}
      </div>
    @endif

    <form method="POST" action="{{ route('staff.profile.update') }}" enctype="multipart/form-data">
      @csrf
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block text-sm mb-1">Full Name</label>
          <input type="text" name="full_name" value="{{ $profile->full_name ?? '' }}" class="w-full p-2 rounded bg-white/20 border border-white/30 text-white">
        </div>

        <div>
          <label class="block text-sm mb-1">Email</label>
          <input type="email" name="email" value="{{ $profile->email ?? '' }}" class="w-full p-2 rounded bg-white/20 border border-white/30 text-white">
        </div>

        <div>
          <label class="block text-sm mb-1">Phone Number</label>
          <input type="text" name="phone_number" value="{{ $profile->phone_number ?? '' }}" class="w-full p-2 rounded bg-white/20 border border-white/30 text-white">
        </div>

        <div>
          <label class="block text-sm mb-1">Department</label>
          <input type="text" name="department" value="{{ $profile->department ?? '' }}" class="w-full p-2 rounded bg-white/20 border border-white/30 text-white">
        </div>

        <div class="col-span-2">
          <label class="block text-sm mb-1">Address</label>
          <textarea name="address" rows="3" class="w-full p-2 rounded bg-white/20 border border-white/30 text-white">{{ $profile->address ?? '' }}</textarea>
        </div>

        <div class="col-span-2">
          <label class="block text-sm mb-1">Profile Image</label>
          <input type="file" name="profile_image" class="w-full text-white">
          @if(!empty($profile->profile_image))
            <img src="{{ asset('storage/' . $profile->profile_image) }}" alt="Profile Image" class="w-24 h-24 rounded-full mt-2">
          @endif
        </div>
      </div>

      <div class="text-center mt-6">
        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-semibold">
          Update Profile
        </button>
      </div>
    </form>
  </div>

</body>
</html>
