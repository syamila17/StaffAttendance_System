<!-- Sidebar -->
<aside class="w-64 bg-gradient-to-b from-orange-600 to-orange-700 p-6 shadow-lg">
  <div class="flex items-center gap-3 mb-8">
    <div class="bg-white/20 w-12 h-12 rounded-lg flex items-center justify-center">
      <i class="fas fa-user-shield text-2xl"></i>
    </div>
    <div>
      <h2 class="text-lg font-bold">Admin Panel</h2>
      <p class="text-xs text-gray-200">{{ session('admin_name') ?? 'Admin' }}</p>
    </div>
  </div>

  @php
    $pendingLeaves = \App\Models\LeaveRequest::where('status', 'pending')->count();
  @endphp

  <nav class="space-y-3">
    <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 rounded-lg hover:bg-white/20 transition relative group">
      <i class="fas fa-home mr-2"></i>{{ trans('admin.dashboard') }}
    </a>
    
    <a href="{{ route('admin.staff.index') }}" class="block px-4 py-2 rounded-lg hover:bg-white/20 transition">
      <i class="fas fa-users mr-2"></i>{{ trans('admin.staff_management') }}
    </a>
    
    <a href="{{ route('admin.attendance') }}" class="block px-4 py-2 rounded-lg hover:bg-white/20 transition">
      <i class="fas fa-calendar-check mr-2"></i>{{ trans('admin.attendance') }}
    </a>
    
    <a href="{{ route('admin.attendance.report') }}" class="block px-4 py-2 rounded-lg hover:bg-white/20 transition">
      <i class="fas fa-chart-bar mr-2"></i>{{ trans('admin.reports') }}
    </a>
    
    <a href="{{ route('admin.departments') }}" class="block px-4 py-2 rounded-lg hover:bg-white/20 transition">
      <i class="fas fa-building mr-2"></i>{{ trans('admin.departments') }}
    </a>
    
    <a href="{{ route('admin.leave.requests') }}" class="block px-4 py-2 rounded-lg hover:bg-white/20 transition relative group">
      <i class="fas fa-calendar-times mr-2"></i>{{ trans('admin.leave_requests') }}
      @if($pendingLeaves > 0)
        <span class="absolute top-1 right-2 bg-red-500 text-white text-xs rounded-full w-6 h-6 flex items-center justify-center font-bold">{{ $pendingLeaves }}</span>
      @endif
    </a>
    
    <a href="{{ route('admin.logout') }}" class="block px-4 py-2 rounded-lg hover:bg-white/20 transition">
      <i class="fas fa-sign-out-alt mr-2"></i>{{ trans('admin.logout') }}
    </a>
  </nav>
</aside>
