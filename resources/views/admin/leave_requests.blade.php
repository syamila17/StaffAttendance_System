<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ trans('admin.leave_requests') }}</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-900 text-white">

  <!-- Sidebar -->
  <div class="flex h-screen">
    @include('admin.layouts.sidebar')

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto p-8">
      <div class="max-w-7xl">
        <h1 class="text-4xl font-bold mb-2">{{ trans('admin.leave_requests') }}</h1>
        <p class="text-gray-400 mb-8">{{ trans('admin.review_leave') }}</p>

        @if(session('success'))
          <div class="bg-green-500/20 border border-green-500 text-green-300 px-4 py-3 rounded-lg mb-6">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
          </div>
        @endif

        <!-- Tabs for filtering -->
        <div class="flex gap-4 mb-6 border-b border-gray-700">
          <a href="?status=pending" class="px-4 py-2 relative group @if(request('status', 'pending') === 'pending') border-b-2 border-orange-500 text-orange-400 @else text-gray-400 @endif">
            <i class="fas fa-hourglass-half mr-2"></i>{{ trans('admin.pending') }}
            <span id="pendingBadge" class="absolute -top-2 -right-3 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center hidden font-bold">0</span>
          </a>
          <a href="?status=approved" class="px-4 py-2 @if(request('status') === 'approved') border-b-2 border-orange-500 text-orange-400 @else text-gray-400 @endif">
            <i class="fas fa-check-circle mr-2"></i>{{ trans('admin.approved') }}
          </a>
          <a href="?status=rejected" class="px-4 py-2 @if(request('status') === 'rejected') border-b-2 border-orange-500 text-orange-400 @else text-gray-400 @endif">
            <i class="fas fa-times-circle mr-2"></i>{{ trans('admin.rejected') }}
          </a>
        </div>

        @if($leaveRequests->count() > 0)
        <!-- Requests Table -->
        <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-x-auto shadow-xl">
          <table class="w-full min-w-max">
            <thead class="bg-gray-700 border-b border-gray-600">
              <tr>
                <th class="px-4 py-4 text-left text-lg font-semibold">{{ trans('admin.name') }}</th>
                <th class="px-4 py-4 text-left text-lg font-semibold">{{ trans('admin.leave_type') }}</th>
                <th class="px-4 py-4 text-left text-lg font-semibold">{{ trans('admin.from_date') }}</th>
                <th class="px-4 py-4 text-left text-lg font-semibold">{{ trans('admin.to_date') }}</th>
                <th class="px-4 py-4 text-center text-lg font-semibold">{{ trans('admin.days') }}</th>
                <th class="px-4 py-4 text-left text-lg font-semibold">{{ trans('admin.reason') }}</th>
                <th class="px-4 py-4 text-left text-lg font-semibold">{{ trans('admin.proof') }}</th>
                <th class="px-4 py-4 text-left text-lg font-semibold">{{ trans('admin.status') }}</th>
                <th class="px-4 py-4 text-center text-lg font-semibold">{{ trans('admin.actions') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach($leaveRequests as $leave)
              <tr class="border-b border-gray-700 hover:bg-gray-750 transition">
                <td class="px-4 py-4">
                  <div class="flex flex-col">
                    <span class="font-semibold text-base">{{ $leave->staff->staff_name ?? 'N/A' }}</span>
                    <span class="text-sm text-gray-400">{{ $leave->staff->staff_id ?? 'N/A' }}</span>
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
                <td class="px-4 py-4 whitespace-nowrap text-base">
                  @if($leave->isProofRequired())
                    @if($leave->hasProofFile())
                      <button onclick="openProofModal({{ $leave->leave_request_id }}, '{{ $leave->proof_file }}')" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 rounded text-blue-200 inline-flex items-center gap-2 transition" title="View Proof">
                        <i class="fas fa-eye"></i>
                        <span>View</span>
                      </button>
                    @else
                      <span class="px-3 py-1 bg-red-500/20 text-red-300 rounded text-base flex items-center gap-2">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>Missing</span>
                      </span>
                    @endif
                  @elseif($leave->isProofOptional())
                    @if($leave->hasProofFile())
                      <button onclick="openProofModal({{ $leave->leave_request_id }}, '{{ $leave->proof_file }}')" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 rounded text-blue-200 inline-flex items-center gap-2 transition" title="View Proof">
                        <i class="fas fa-eye"></i>
                        <span>View</span>
                      </button>
                    @else
                      <span class="text-gray-500">Not provided</span>
                    @endif
                  @else
                    <span class="text-gray-500">N/A</span>
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

  <!-- Proof File Viewer Modal -->
  <div id="proofModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-gray-800 rounded-lg shadow-2xl w-full max-w-4xl max-h-[90vh] flex flex-col border border-gray-700">
      <!-- Modal Header -->
      <div class="flex items-center justify-between p-6 border-b border-gray-700">
        <div class="flex items-center gap-3">
          <i class="fas fa-file text-blue-400 text-2xl"></i>
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
      <div id="proofViewer" class="flex-1 overflow-auto bg-gray-900 flex items-center justify-center">
        <!-- Content will be loaded here -->
      </div>

      <!-- Modal Footer -->
      <div class="flex items-center justify-between p-6 border-t border-gray-700">
        <p class="text-sm text-gray-400">
          <i class="fas fa-info-circle mr-2"></i>
          <span id="previewInfo">Previewing document. Use download button to save.</span>
        </p>
        <div class="flex gap-3">
          <button onclick="closeProofModal()" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 rounded-lg text-white transition">
            <i class="fas fa-times mr-2"></i>Close
          </button>
          <button id="downloadProofBtn" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 rounded-lg text-white transition flex items-center gap-2">
            <i class="fas fa-download"></i>Download
          </button>
        </div>
      </div>
    </div>
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

</html>
