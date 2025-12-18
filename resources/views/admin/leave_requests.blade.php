<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Leave Requests</title>
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
        <a href="{{ route('admin.departments') }}" class="block px-4 py-2 rounded-lg hover:bg-white/20 transition">
          <i class="fas fa-building mr-2"></i>Departments
        </a>
        <a href="{{ route('admin.leave.requests') }}" class="block px-4 py-2 rounded-lg bg-white/30 relative group">
          <i class="fas fa-calendar-times mr-2"></i>Leave Requests
          <span id="leaveBadge" class="absolute top-1 right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center hidden font-bold">0</span>
        </a>
        <a href="{{ route('admin.logout') }}" class="block px-4 py-2 rounded-lg hover:bg-white/20 transition">
          <i class="fas fa-sign-out-alt mr-2"></i>Logout
        </a>
      </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto p-8">
      <div class="max-w-7xl">
        <h1 class="text-4xl font-bold mb-2">Leave Requests</h1>
        <p class="text-gray-400 mb-8">Review and manage staff leave applications</p>

        @if(session('success'))
          <div class="bg-green-500/20 border border-green-500 text-green-300 px-4 py-3 rounded-lg mb-6">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
          </div>
        @endif

        <!-- Tabs for filtering -->
        <div class="flex gap-4 mb-6 border-b border-gray-700">
          <a href="?status=pending" class="px-4 py-2 relative group @if(request('status', 'pending') === 'pending') border-b-2 border-orange-500 text-orange-400 @else text-gray-400 @endif">
            <i class="fas fa-hourglass-half mr-2"></i>Pending
            <span id="pendingBadge" class="absolute -top-2 -right-3 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center hidden font-bold">0</span>
          </a>
          <a href="?status=approved" class="px-4 py-2 @if(request('status') === 'approved') border-b-2 border-orange-500 text-orange-400 @else text-gray-400 @endif">
            <i class="fas fa-check-circle mr-2"></i>Approved
          </a>
          <a href="?status=rejected" class="px-4 py-2 @if(request('status') === 'rejected') border-b-2 border-orange-500 text-orange-400 @else text-gray-400 @endif">
            <i class="fas fa-times-circle mr-2"></i>Rejected
          </a>
        </div>

        @if($leaveRequests->count() > 0)
        <!-- Requests Table -->
        <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-x-auto shadow-xl">
          <table class="w-full min-w-max">
            <thead class="bg-gray-700 border-b border-gray-600">
              <tr>
                <th class="px-4 py-4 text-left text-lg font-semibold">Staff Name</th>
                <th class="px-4 py-4 text-left text-lg font-semibold">Leave Type</th>
                <th class="px-4 py-4 text-left text-lg font-semibold">From Date</th>
                <th class="px-4 py-4 text-left text-lg font-semibold">To Date</th>
                <th class="px-4 py-4 text-center text-lg font-semibold">Days</th>
                <th class="px-4 py-4 text-left text-lg font-semibold">Reason</th>
                <th class="px-4 py-4 text-left text-lg font-semibold">Status</th>
                <th class="px-4 py-4 text-center text-lg font-semibold">Action</th>
              </tr>
            </thead>
            <tbody>
              @foreach($leaveRequests as $leave)
              <tr class="border-b border-gray-700 hover:bg-gray-750 transition">
                <td class="px-4 py-4">
                  <div class="flex flex-col">
                    <span class="font-semibold text-base">{{ $leave->staff->staff_name ?? 'N/A' }}</span>
                    <span class="text-sm text-gray-400">{{ $leave->staff->staff_email ?? 'N/A' }}</span>
                  </div>
                </td>
                <td class="px-4 py-4 whitespace-nowrap">
                  <span class="px-2 py-1 bg-blue-500/20 text-blue-300 rounded text-base">{{ $leave->leave_type }}</span>
                </td>
                <td class="px-4 py-4 whitespace-nowrap text-base">{{ $leave->from_date->format('M d, Y') }}</td>
                <td class="px-4 py-4 whitespace-nowrap text-base">{{ $leave->to_date->format('M d, Y') }}</td>
                <td class="px-4 py-4 text-center whitespace-nowrap font-semibold text-base">{{ $leave->from_date->diffInDays($leave->to_date) + 1 }}</td>
                <td class="px-4 py-4 whitespace-nowrap text-gray-300 text-base">
                  @if($leave->reason)
                    <span title="{{ $leave->reason }}">{{ Str::limit($leave->reason, 20) }}</span>
                  @else
                    <span class="text-gray-500">-</span>
                  @endif
                </td>
                <td class="px-4 py-4 whitespace-nowrap">
                  <span class="px-2 py-1 rounded text-base
                    @if($leave->status === 'pending') bg-yellow-500/20 text-yellow-300
                    @elseif($leave->status === 'approved') bg-green-500/20 text-green-300
                    @else bg-red-500/20 text-red-300 @endif">
                    <i class="@if($leave->status === 'pending') fas fa-hourglass-half @elseif($leave->status === 'approved') fas fa-check-circle @else fas fa-times-circle @endif mr-1"></i>{{ ucfirst($leave->status) }}
                  </span>
                </td>
                <td class="px-4 py-4 whitespace-nowrap">
                  @if($leave->status === 'pending')
                  <div class="flex gap-1 justify-center">
                    <form method="POST" action="{{ route('admin.leave.approve', $leave->leave_request_id) }}" class="inline">
                      @csrf
                      <button type="submit" class="px-3 py-2 bg-green-600 hover:bg-green-700 rounded text-base transition" title="Approve">
                        <i class="fas fa-check"></i>
                      </button>
                    </form>
                    <form method="POST" action="{{ route('admin.leave.reject', $leave->leave_request_id) }}" class="inline">
                      @csrf
                      <button type="submit" class="px-3 py-2 bg-red-600 hover:bg-red-700 rounded text-base transition" title="Reject">
                        <i class="fas fa-times"></i>
                      </button>
                    </form>
                  </div>
                  @else
                  <span class="text-gray-500 text-base">-</span>
                  @endif
                </td>
              </tr>

              @if($leave->status === 'rejected' && $leave->admin_notes)
              <tr class="bg-red-500/10 border-b border-gray-700">
                <td colspan="8" class="px-4 py-4">
                  <div class="flex items-start gap-3">
                    <i class="fas fa-clipboard-list text-red-400 mt-1 text-base"></i>
                    <div>
                      <p class="text-red-300 font-semibold text-base">Admin Notes:</p>
                      <p class="text-gray-300 text-base mt-1">{{ $leave->admin_notes }}</p>
                    </div>
                  </div>
                </td>
              </tr>
              @endif

              @if($leave->status === 'approved' && $leave->approved_at)
              <tr class="bg-green-500/10 border-b border-gray-700">
                <td colspan="8" class="px-4 py-3 text-base text-gray-400">
                  <i class="fas fa-check mr-2 text-green-400"></i>Approved on {{ $leave->approved_at->format('M d, Y \a\t H:i') }}
                </td>
              </tr>
              @endif
              @endforeach
            </tbody>
          </table>
        </div>
        @else
        <!-- Empty State -->
        <div class="bg-gray-800 rounded-lg border border-gray-700 p-8 text-center">
          <i class="fas fa-inbox text-4xl text-gray-600 mb-3"></i>
          <p class="text-gray-400">No leave requests found</p>
        </div>
        @endif
      </div>
    </main>
  </div>

</body>

<script>
  // Function to update pending leave count badge
  async function updateLeaveNotificationBadge() {
    try {
      const response = await fetch('{{ route("admin.leave.pending-count") }}');
      const data = await response.json();
      
      const leaveBadge = document.getElementById('leaveBadge');
      const pendingBadge = document.getElementById('pendingBadge');
      
      if (data.count > 0) {
        leaveBadge.textContent = data.count;
        leaveBadge.classList.remove('hidden');
        pendingBadge.textContent = data.count;
        pendingBadge.classList.remove('hidden');
      } else {
        leaveBadge.classList.add('hidden');
        pendingBadge.classList.add('hidden');
      }
    } catch (error) {
      console.error('Failed to update notification badge:', error);
    }
  }
  
  // Load badge on page load only
  updateLeaveNotificationBadge();
</script>

</html>
