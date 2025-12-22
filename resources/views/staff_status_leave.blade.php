<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Leave Status</title>
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
      <a href="{{ route('staff.apply-leave') }}" class="block px-4 py-2 rounded-lg hover:bg-white/20"><i class="fas fa-calendar-times mr-2"></i>Apply Leave</a>
      <a href="{{ route('staff.leave.status') }}" class="block px-4 py-2 rounded-lg bg-white/20"><i class="fas fa-list-check mr-2"></i>Leave Status</a>
      <a href="{{ route('staff.logout') }}" 
        class="block px-4 py-2 rounded-lg hover:bg-white/20 flex items-center">
        <i class="fas fa-sign-out-alt mr-2"></i> Logout
      </a>
    </nav>
  </aside>

  <!-- Main Content -->
  <main class="flex-1 p-10 overflow-y-auto">
    <div class="max-w-6xl mx-auto">
      <div class="flex items-center justify-between mb-8">
        <div>
          <h1 class="text-3xl font-bold"><i class="fas fa-list-check mr-2"></i>Leave Status & History</h1>
          <p class="text-gray-300 mt-1">Track your leave requests and off-day balance</p>
        </div>
      </div>

      @if(session('success'))
        <div class="bg-green-500/20 border border-green-500 text-green-300 px-4 py-3 rounded-lg mb-6">
          <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
      @endif

      <!-- Status Summary Cards -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <!-- Pending Requests -->
        <div class="bg-white/10 p-6 rounded-lg border border-white/20 hover:border-yellow-400/50 transition">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-300 text-sm">Pending Requests</p>
              <p class="text-3xl font-bold text-yellow-300 mt-2">{{ $pendingCount }}</p>
            </div>
            <i class="fas fa-hourglass-half text-4xl text-yellow-400/30"></i>
          </div>
        </div>

        <!-- Approved Requests -->
        <div class="bg-white/10 p-6 rounded-lg border border-white/20 hover:border-green-400/50 transition">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-300 text-sm">Approved Requests</p>
              <p class="text-3xl font-bold text-green-300 mt-2">{{ $approvedCount }}</p>
            </div>
            <i class="fas fa-check-circle text-4xl text-green-400/30"></i>
          </div>
        </div>

        <!-- Rejected Requests -->
        <div class="bg-white/10 p-6 rounded-lg border border-white/20 hover:border-red-400/50 transition">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-300 text-sm">Rejected Requests</p>
              <p class="text-3xl font-bold text-red-300 mt-2">{{ $rejectedCount }}</p>
            </div>
            <i class="fas fa-times-circle text-4xl text-red-400/30"></i>
          </div>
        </div>

        <!-- Total Off Days This Month -->
        <div class="bg-white/10 p-6 rounded-lg border border-white/20 hover:border-blue-400/50 transition">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-300 text-sm">Off Days ({{ $currentMonth }})</p>
              <p class="text-3xl font-bold text-blue-300 mt-2">{{ $totalOffDaysMonth }} days</p>
            </div>
            <i class="fas fa-calendar text-4xl text-blue-400/30"></i>
          </div>
        </div>
      </div>

      <!-- Leave Balance Summary -->
      <div class="bg-white/10 p-8 rounded-xl border border-white/20 shadow-lg mb-8">
        <h2 class="text-2xl font-bold mb-6"><i class="fas fa-chart-pie mr-2"></i>Annual Leave Balance</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <!-- Total Annual Leave -->
          <div class="bg-white/5 p-6 rounded-lg border border-white/10">
            <p class="text-gray-300 text-sm mb-2">Total Annual Leave</p>
            <div class="flex items-end gap-3">
              <p class="text-4xl font-bold">{{ $totalAnnualLeave }}</p>
              <p class="text-gray-400 text-lg">days</p>
            </div>
          </div>

          <!-- Used Leave -->
          <div class="bg-white/5 p-6 rounded-lg border border-white/10">
            <p class="text-gray-300 text-sm mb-2">Used Leave ({{ date('Y') }})</p>
            <div class="flex items-end gap-3">
              <p class="text-4xl font-bold text-orange-300">{{ $usedLeave }}</p>
              <p class="text-gray-400 text-lg">days</p>
            </div>
          </div>

          <!-- Remaining Balance -->
          <div class="bg-white/5 p-6 rounded-lg border border-white/10">
            <p class="text-gray-300 text-sm mb-2">Remaining Balance</p>
            <div class="flex items-end gap-3">
              <p class="text-4xl font-bold text-green-300">{{ $remainingBalance }}</p>
              <p class="text-gray-400 text-lg">days</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Status Filter Tabs -->
      <div class="flex gap-4 mb-6 border-b border-white/20">
        <a href="?filter=all" class="px-4 py-3 font-semibold transition 
          @if(request('filter', 'all') === 'all') border-b-2 border-green-400 text-green-300 @else text-gray-400 hover:text-white @endif">
          <i class="fas fa-list mr-2"></i>All Requests ({{ $totalRequests }})
        </a>
        <a href="?filter=pending" class="px-4 py-3 font-semibold transition 
          @if(request('filter') === 'pending') border-b-2 border-yellow-400 text-yellow-300 @else text-gray-400 hover:text-white @endif">
          <i class="fas fa-hourglass-half mr-2"></i>Pending ({{ $pendingCount }})
        </a>
        <a href="?filter=approved" class="px-4 py-3 font-semibold transition 
          @if(request('filter') === 'approved') border-b-2 border-green-400 text-green-300 @else text-gray-400 hover:text-white @endif">
          <i class="fas fa-check-circle mr-2"></i>Approved ({{ $approvedCount }})
        </a>
        <a href="?filter=rejected" class="px-4 py-3 font-semibold transition 
          @if(request('filter') === 'rejected') border-b-2 border-red-400 text-red-300 @else text-gray-400 hover:text-white @endif">
          <i class="fas fa-times-circle mr-2"></i>Rejected ({{ $rejectedCount }})
        </a>
      </div>

      <!-- Leave History Table -->
      @if($filteredLeaves->count() > 0)
        <div class="bg-white/10 rounded-lg border border-white/20 overflow-hidden shadow-lg">
          <table class="w-full">
            <thead class="bg-white/20 border-b border-white/20">
              <tr>
                <th class="px-6 py-4 text-left">Leave Type</th>
                <th class="px-6 py-4 text-left">From Date</th>
                <th class="px-6 py-4 text-left">To Date</th>
                <th class="px-6 py-4 text-center">Days</th>
                <th class="px-6 py-4 text-left">Reason</th>
                <th class="px-6 py-4 text-left">Proof</th>
                <th class="px-6 py-4 text-left">Status</th>
                <th class="px-6 py-4 text-left">Applied On</th>
              </tr>
            </thead>
            <tbody>
              @foreach($filteredLeaves as $leave)
              <tr class="border-b border-white/10 hover:bg-white/5 transition">
                <td class="px-6 py-4">
                  <span class="px-3 py-1 bg-blue-500/20 text-blue-300 rounded-full text-sm">{{ $leave->leave_type }}</span>
                </td>
                <td class="px-6 py-4">{{ $leave->from_date->format('M d, Y') }}</td>
                <td class="px-6 py-4">{{ $leave->to_date->format('M d, Y') }}</td>
                <td class="px-6 py-4 text-center font-semibold">{{ $leave->from_date->diffInDays($leave->to_date) + 1 }} days</td>
                <td class="px-6 py-4 text-gray-300">
                  @if($leave->reason)
                    <span title="{{ $leave->reason }}">{{ Str::limit($leave->reason, 30) }}</span>
                  @else
                    <span class="text-gray-500">-</span>
                  @endif
                </td>
                <td class="px-6 py-4">
                  @if($leave->isProofRequired())
                    @if($leave->hasProofFile())
                      <button onclick="openProofModal({{ $leave->leave_request_id }}, '{{ $leave->proof_file }}')" class="px-3 py-1 bg-green-500/20 text-green-300 rounded-full text-sm inline-flex items-center gap-2 hover:bg-green-500/30 transition">
                        <i class="fas fa-eye"></i>View
                      </button>
                    @else
                      <span class="px-3 py-1 bg-red-500/20 text-red-300 rounded-full text-sm flex items-center gap-2">
                        <i class="fas fa-exclamation-circle"></i>Required
                      </span>
                    @endif
                  @elseif($leave->isProofOptional())
                    @if($leave->hasProofFile())
                      <button onclick="openProofModal({{ $leave->leave_request_id }}, '{{ $leave->proof_file }}')" class="px-3 py-1 bg-blue-500/20 text-blue-300 rounded-full text-sm inline-flex items-center gap-2 hover:bg-blue-500/30 transition">
                        <i class="fas fa-file"></i>View
                      </button>
                    @else
                      <span class="text-gray-500 text-sm">-</span>
                    @endif
                  @else
                    <span class="text-gray-500 text-sm">-</span>
                  @endif
                </td>
                <td class="px-6 py-4">
                  @if($leave->status === 'pending')
                    <span class="px-3 py-1 bg-yellow-500/20 text-yellow-300 rounded-full text-sm">
                      <i class="fas fa-hourglass-half mr-1"></i>Pending
                    </span>
                  @elseif($leave->status === 'approved')
                    <span class="px-3 py-1 bg-green-500/20 text-green-300 rounded-full text-sm">
                      <i class="fas fa-check-circle mr-1"></i>Approved
                    </span>
                  @else
                    <span class="px-3 py-1 bg-red-500/20 text-red-300 rounded-full text-sm">
                      <i class="fas fa-times-circle mr-1"></i>Rejected
                    </span>
                  @endif
                </td>
                <td class="px-6 py-4 text-sm text-gray-400">{{ $leave->created_at->format('M d, Y') }}</td>
              </tr>

              @if($leave->status === 'rejected' && $leave->admin_notes)
              <tr class="bg-red-500/10 border-b border-white/10">
                <td colspan="7" class="px-6 py-4">
                  <div class="flex items-start gap-3">
                    <i class="fas fa-clipboard-list text-red-400 mt-1"></i>
                    <div>
                      <p class="text-red-300 font-semibold text-sm">Admin Notes:</p>
                      <p class="text-gray-300 text-sm mt-1">{{ $leave->admin_notes }}</p>
                    </div>
                  </div>
                </td>
              </tr>
              @endif

              @if($leave->status === 'approved' && $leave->approved_at)
              <tr class="bg-green-500/10 border-b border-white/10">
                <td colspan="7" class="px-6 py-3 text-sm text-gray-400">
                  <i class="fas fa-check mr-2 text-green-400"></i>Approved on {{ $leave->approved_at->format('M d, Y \a\t H:i') }}
                </td>
              </tr>
              @endif
              @endforeach
            </tbody>
          </table>
        </div>
      @else
        <div class="bg-white/10 rounded-lg border border-white/20 p-12 text-center">
          <i class="fas fa-inbox text-5xl text-gray-500 mb-4"></i>
          <p class="text-gray-300 text-lg">No leave requests found</p>
          <p class="text-gray-400 text-sm mt-2">
            @if(request('filter') !== 'all')
              No {{ request('filter') }} leave requests
            @else
              Start by <a href="{{ route('staff.apply-leave') }}" class="text-green-400 hover:underline">applying for leave</a>
            @endif
          </p>
        </div>
      @endif
    </div>
  </main>

  <!-- Proof File Viewer Modal -->
  <div id="proofModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-red-950 rounded-lg shadow-2xl w-full max-w-4xl max-h-[90vh] flex flex-col border border-red-800">
      <!-- Modal Header -->
      <div class="flex items-center justify-between p-6 border-b border-red-800">
        <div class="flex items-center gap-3">
          <i class="fas fa-file text-green-400 text-2xl"></i>
          <div>
            <h2 class="text-xl font-bold text-white">Proof Document</h2>
            <p class="text-sm text-gray-400" id="proofFileName"></p>
          </div>
        </div>
        <button onclick="closeProofModal()" class="text-gray-400 hover:text-white text-2xl transition">
          <i class="fas fa-times"></i>
        </button>
      </div>

      <!-- Modal Content -->
      <div id="proofViewer" class="flex-1 overflow-auto bg-red-900/30 flex items-center justify-center">
        <!-- Content will be loaded here -->
      </div>

      <!-- Modal Footer -->
      <div class="flex items-center justify-between p-6 border-t border-red-800">
        <p class="text-sm text-gray-400">
          <i class="fas fa-info-circle mr-2"></i>
          <span id="previewInfo">Previewing document. Use download button to save.</span>
        </p>
        <div class="flex gap-3">
          <button onclick="closeProofModal()" class="px-4 py-2 bg-white/20 hover:bg-white/30 rounded-lg text-white transition">
            <i class="fas fa-times mr-2"></i>Close
          </button>
          <button id="downloadProofBtn" class="px-4 py-2 bg-green-600 hover:bg-green-700 rounded-lg text-white transition flex items-center gap-2">
            <i class="fas fa-download"></i>Download
          </button>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Proof file viewer modal
    function openProofModal(leaveId, fileName) {
      const modal = document.getElementById('proofModal');
      const iframe = document.getElementById('proofViewer');
      const downloadBtn = document.getElementById('downloadProofBtn');
      const proofFileName = document.getElementById('proofFileName');
      
      const proofUrl = '{{ url("/staff/leave") }}/' + leaveId + '/download-proof';
      
      // Set the file name
      proofFileName.textContent = fileName;
      
      // Try to display in iframe for preview
      const ext = fileName.split('.').pop().toLowerCase();
      if (ext === 'pdf') {
        iframe.src = proofUrl;
        iframe.style.display = 'block';
      } else if (['jpg', 'jpeg', 'png'].includes(ext)) {
        iframe.innerHTML = '<img src="' + proofUrl + '" style="max-width: 100%; max-height: 100%; object-fit: contain;">';
        iframe.style.display = 'block';
      } else {
        iframe.innerHTML = '<div class="flex flex-col items-center justify-center h-full text-gray-400"><i class="fas fa-file text-5xl mb-4"></i><p>Preview not available for this file type.</p><p class="text-sm mt-2">Please download to view.</p></div>';
        iframe.style.display = 'block';
      }
      
      // Set download button
      downloadBtn.onclick = function() {
        window.location.href = proofUrl;
      };
      
      modal.classList.remove('hidden');
      modal.classList.add('flex');
    }

    function closeProofModal() {
      const modal = document.getElementById('proofModal');
      const iframe = document.getElementById('proofViewer');
      iframe.src = '';
      iframe.innerHTML = '';
      modal.classList.add('hidden');
      modal.classList.remove('flex');
    }

    // Close modal when clicking outside
    document.getElementById('proofModal')?.addEventListener('click', function(e) {
      if (e.target === this) {
        closeProofModal();
      }
    });
  </script>

</body>
</html>
