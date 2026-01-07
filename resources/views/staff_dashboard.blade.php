<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Staff Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body { overflow-x: hidden; }
  </style>
</head>

<body class="flex h-screen bg-gradient-to-br from-red-800 to-red-950 text-white m-0 p-0">

  <!-- Sidebar -->
  <aside class="w-64 bg-gradient-to-b from-red-900 to-red-950 shadow-lg p-6 space-y-6">
    <div class="flex items-center gap-3">
      <div class="bg-white/20 w-12 h-12 rounded-lg flex items-center justify-center overflow-hidden">
        @if($profile && ! empty($profile->profile_image))
          <img src="{{ asset('storage/' . $profile->profile_image) }}" alt="Profile" class="w-full h-full object-cover">
        @else
          <i class="fas fa-user text-2xl text-white"></i>
        @endif
      </div>
      <div>
        <h2 class="text-lg font-bold">Staff Panel</h2>
        <p class="text-xs text-gray-300">{{ $staffName}}</p>
      </div>
    </div>

    <nav class="mt-8 space-y-4">
      <a href="{{ route('staff.dashboard') }}" class="block px-4 py-2 rounded-lg bg-white/20"><i class="fas fa-home mr-2"></i>{{ __('dashboard.dashboard') }}</a>
      <a href="{{ route('attendance.show') }}" class="block px-4 py-2 rounded-lg hover:bg-white/20"><i class="fas fa-calendar-check mr-2"></i>{{ __('dashboard.attendance') }}</a>
      <a href="{{ route('staff.profile') }}" class="block px-4 py-2 rounded-lg hover:bg-white/20"><i class="fas fa-user-circle mr-2"></i>{{ __('dashboard.profile') }}</a>
      <a href="{{ route('staff.apply-leave') }}" class="block px-4 py-2 rounded-lg hover:bg-white/20"><i class="fas fa-calendar-times mr-2"></i>{{ __('dashboard.apply_leave') }}</a>
      <a href="{{ route('staff.leave.status') }}" class="block px-4 py-2 rounded-lg hover:bg-white/20 relative group">
        <i class="fas fa-list-check mr-2"></i>{{ __('dashboard.leave_status') }}
        <span id="notificationBadge" class="absolute top-1 right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center hidden font-bold text-xs">0</span>
      </a>
      
      <!-- Language Switcher -->
      <div class="border-t border-white/20 pt-4 mt-4">
        <p class="text-xs text-gray-400 mb-2 px-4">Language</p>
        <a href="{{ route('staff.dashboard', ['lang' => 'en']) }}" class="block px-4 py-2 rounded-lg hover:bg-white/20 text-sm @if(app()->getLocale() === 'en') bg-white/20 @endif">
          <i class="fas fa-globe mr-2"></i>English
        </a>
        <a href="{{ route('staff.dashboard', ['lang' => 'ms']) }}" class="block px-4 py-2 rounded-lg hover:bg-white/20 text-sm @if(app()->getLocale() === 'ms') bg-white/20 @endif">
          <i class="fas fa-globe mr-2"></i>Bahasa Melayu
        </a>
      </div>
      
      <a href="{{ route('staff.logout') }}" 
        class="block px-4 py-2 rounded-lg hover:bg-white/20 flex items-center border-t border-white/20 pt-4 mt-4">
        <i class="fas fa-sign-out-alt mr-2"></i>{{ __('dashboard.logout') }}
      </a>

    </nav>
  </aside>

  <!-- Main Content -->
  <main class="flex-1 p-10 overflow-y-auto">
    <h1 class="text-3xl font-bold mb-2">{{ __('dashboard.welcome') }}, {{ $staffName}}!</h1>
    <p class="text-gray-200 text-lg mb-8">{{ __('dashboard.email') }}: {{ $staffEmail }}</p>

    <!-- Today's Attendance Card -->
    <div class="bg-white/10 p-6 rounded-xl shadow-lg border border-white/20 mb-8">
      <h2 class="text-2xl font-semibold mb-4 flex items-center">
        <i class="fas fa-calendar-check mr-2 text-green-400"></i>{{ __('dashboard.todays_attendance') }}
      </h2>
      
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        @if($todayAttendance)
          <!-- Status -->
          <div class="bg-white/5 p-4 rounded-lg border border-white/10">
            <p class="text-gray-300 text-sm mb-2">{{ __('dashboard.status') }}</p>
            <p class="text-2xl font-bold capitalize 
              @if($todayAttendance->status === 'present') text-green-400
              @elseif($todayAttendance->status === 'absent') text-red-400
              @elseif($todayAttendance->status === 'late') text-yellow-400
              @elseif($todayAttendance->status === 'el') text-orange-400
              @elseif($todayAttendance->status === 'on leave') text-blue-400
              @elseif($todayAttendance->status === 'half day') text-purple-400
              @else text-gray-400 @endif">
              {{ ucfirst($todayAttendance->status) }}
            </p>
            @if($todayAttendance->status === 'on leave' && isset($todayAttendance->remarks))
              <p class="text-xs text-blue-300 mt-2">{{ $todayAttendance->remarks }}</p>
            @endif
          </div>

          <!-- Check-in Time - Only show if Present -->
          @if($todayAttendance->status === 'present')
            <div class="bg-white/5 p-4 rounded-lg border border-white/10">
              <p class="text-gray-300 text-sm mb-2">{{ __('dashboard.check_in_time') }}</p>
              <p class="text-2xl font-bold @if($todayAttendance->check_in_time) text-green-400 @else text-gray-400 @endif">
                {{ $todayAttendance->check_in_time ?  substr($todayAttendance->check_in_time, 0, 5) : '--:--' }}
              </p>
            </div>

            <!-- Check-out Time - Only show if Present -->
            <div class="bg-white/5 p-4 rounded-lg border border-white/10">
              <p class="text-gray-300 text-sm mb-2">{{ __('dashboard.check_out_time') }}</p>
              <p class="text-2xl font-bold @if($todayAttendance->check_out_time) text-blue-400 @else text-gray-400 @endif">
                {{ $todayAttendance->check_out_time ? substr($todayAttendance->check_out_time, 0, 5) : '--:--' }}
              </p>
            </div>

            <!-- Duration - Only show if Present -->
            <div class="bg-white/5 p-4 rounded-lg border border-white/10">
              <p class="text-gray-300 text-sm mb-2">{{ __('dashboard.duration') }}</p>
              @if($todayAttendance->check_in_time && $todayAttendance->check_out_time)
                @php
                  $checkIn = \Carbon\Carbon::createFromFormat('H:i:s', $todayAttendance->check_in_time);
                  $checkOut = \Carbon\Carbon::createFromFormat('H:i:s', $todayAttendance->check_out_time);
                  $minutes = abs($checkOut->diffInMinutes($checkIn));
                  $hours = floor($minutes / 60);
                  $remainingMinutes = $minutes % 60;
                @endphp
                <p class="text-2xl font-bold text-purple-400">
                  {{ $hours }}h {{ $remainingMinutes }}m
                </p>
              @else
                <p class="text-2xl font-bold text-gray-400">-</p>
              @endif
            </div>
          @else
            <!-- Show placeholder boxes for non-present status -->
            <div class="bg-white/5 p-4 rounded-lg border border-white/10">
              <p class="text-gray-300 text-sm mb-2">Check-in Time</p>
              <p class="text-2xl font-bold text-gray-500">--:--</p>
            </div>

            <div class="bg-white/5 p-4 rounded-lg border border-white/10">
              <p class="text-gray-300 text-sm mb-2">{{ __('dashboard.check_out_time') }}</p>
              <p class="text-2xl font-bold text-gray-500">--:--</p>
            </div>

            <div class="bg-white/5 p-4 rounded-lg border border-white/10">
              <p class="text-gray-300 text-sm mb-2">{{ __('dashboard.duration') }}</p>
              <p class="text-2xl font-bold text-gray-500">-</p>
            </div>
          @endif
        @else
          <div class="col-span-4 bg-white/5 p-6 rounded-lg border border-white/10 text-center">
            <i class="fas fa-info-circle mr-2 text-blue-400"></i>
            <p class="text-gray-300">{{ __('dashboard.no_record') }}</p>
            <a href="{{ route('attendance.show') }}" class="text-red-400 hover:text-red-300 mt-2 inline-block">
              <i class="fas fa-arrow-right mr-1"></i>{{ __('dashboard.attendance') }}
            </a>
          </div>
        @endif
      </div>

      <!-- Quick Check-in/Check-out Buttons -->
      @if($todayAttendance && $todayAttendance->status === 'present')
        <div class="flex gap-4 mt-6">
          @if(!$todayAttendance->check_in_time)
            <form method="POST" action="{{ route('attendance.checkIn') }}" class="flex-1">
              @csrf
              <button type="submit" class="w-full bg-green-600 hover:bg-green-700 px-6 py-3 rounded-lg font-semibold transition flex items-center justify-center">
                <i class="fas fa-sign-in-alt mr-2"></i>Check In
              </button>
            </form>
          @endif

          @if($todayAttendance->check_in_time && !$todayAttendance->check_out_time)
            <form method="POST" action="{{ route('attendance.checkOut') }}" class="flex-1">
              @csrf
              <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 px-6 py-3 rounded-lg font-semibold transition flex items-center justify-center">
                <i class="fas fa-sign-out-alt mr-2"></i>Check Out
              </button>
            </form>
          @endif
        </div>
      @endif
    </div>
    </div>

    <!-- ========== REDESIGNED SECTION: Pie Chart + Stats Side by Side ========== -->
    <div class="bg-white/10 p-8 rounded-xl shadow-lg border border-white/20 mb-8">
      <div class="flex justify-between items-center mb-6">
        <div>
          <h2 class="text-2xl font-semibold flex items-center">
            <i class="fas fa-chart-pie mr-2 text-purple-400"></i>{{ __('dashboard.monthly_attendance') }}
          </h2>
          <p class="text-sm text-gray-400 mt-2">
            <i class="fas fa-calendar mr-1"></i>
            <span id="currentDate"></span> - 
            <span id="currentTime"></span>
          </p>
          <p class="text-xs text-gray-500 mt-1">
            {{ __('dashboard.last_refreshed') }}: <span id="lastRefresh">{{ __('dashboard.just_now') }}</span>
          </p>
        </div>
        <div class="flex items-center gap-3">
          <!-- Month Selector -->
          <div class="relative">
            <select id="monthSelector" class="bg-purple-600 hover:bg-purple-700 px-4 py-2 rounded-lg text-white text-sm font-semibold appearance-none cursor-pointer transition pr-8" onchange="changeMonth(this.value)">
              @foreach($availableMonths as $monthValue => $monthLabel)
                <option value="{{ $monthValue }}" @if($monthValue === $selectedMonth) selected @endif>
                  {{ $monthLabel }}
                </option>
              @endforeach
            </select>
            <i class="fas fa-chevron-down absolute right-2 top-1/2 transform -translate-y-1/2 text-white pointer-events-none text-sm"></i>
          </div>
          
          <!-- Reset to Current Month Button -->
          @if($selectedMonth !== \Carbon\Carbon::now()->format('Y-m'))
            <button id="resetBtn" class="bg-orange-600 hover:bg-orange-700 px-4 py-2 rounded-lg text-white text-sm font-semibold flex items-center gap-2 transition">
              <i class="fas fa-undo"></i>{{ __('dashboard.current') }}
            </button>
          @endif
          
          <!-- Refresh Button -->
          <button id="refreshBtn" class="bg-purple-600 hover:bg-purple-700 px-4 py-2 rounded-lg text-white text-sm font-semibold flex items-center gap-2 transition">
            <i class="fas fa-sync-alt"></i>{{ __('dashboard.refresh') }}
          </button>
        </div>
      </div>

      <!-- MAIN GRID: Pie Chart (Left) + Stats (Right) -->
      <div class="flex flex-col lg:flex-row gap-6 items-stretch lg:items-center justify-center">
        
        <!-- LEFT SIDE: Pie Chart (50%) -->
        <div class="w-full lg:w-1/2">
         <!-- Pie Chart Container -->
<div class="bg-white/5 rounded-lg border border-white/10 overflow-hidden p-4 h-full" style="min-height: 400px; display: flex; flex-direction: column;">
    <h3 class="text-lg font-bold text-white mb-4">{{ __('dashboard.pie_chart') }}</h3>
    <div id="chartErrorMessage" class="hidden bg-red-500/20 border border-red-500 text-red-300 p-3 rounded mb-4 text-sm"></div>
    <div style="position: relative; height: 300px; width: 100%; flex: 1; display: flex; align-items: center; justify-content: center;">
        <canvas id="attendancePieChart"></canvas>
    </div>
</div>

          
        </div>

        <!-- RIGHT SIDE: Statistics Boxes (50%) - Vertical Stack -->
        <div class="w-full lg:w-1/2 flex flex-col gap-2">
          
          <!-- Total Present Box -->
          <div class="bg-white/10 p-2 rounded-lg shadow-lg border border-white/20 hover:border-green-400/50 transition">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-gray-300 text-xs mb-0">{{ __('dashboard.total_present') }}</p>
                <p class="text-lg font-bold text-green-400">{{ $totalPresent }}</p>
                <p class="text-xs text-green-300">{{ $attendanceStats['present_percentage'] }}% {{ __('dashboard.of_month') }}</p>
              </div>
              <i class="fas fa-check-circle text-green-400/20 text-2xl"></i>
            </div>
          </div>

          <!-- Total Absent Box -->
          <div class="bg-white/10 p-2 rounded-lg shadow-lg border border-white/20 hover:border-red-400/50 transition">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-gray-300 text-xs mb-0">{{ __('dashboard.total_absent') }}</p>
                <p class="text-lg font-bold text-red-400">{{ $totalAbsent }}</p>
                <p class="text-xs text-red-300">{{ $attendanceStats['absent_percentage'] }}% {{ __('dashboard.of_month') }}</p>
              </div>
              <i class="fas fa-times-circle text-red-400/20 text-2xl"></i>
            </div>
          </div>

          <!-- Total Late Box -->
          <div class="bg-white/10 p-2 rounded-lg shadow-lg border border-white/20 hover:border-yellow-400/50 transition">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-gray-300 text-xs mb-0">{{ __('dashboard.total_late') }}</p>
                <p class="text-lg font-bold text-yellow-400">{{ $totalLate }}</p>
                <p class="text-xs text-yellow-300">{{ $attendanceStats['late_percentage'] }}% {{ __('dashboard.of_month') }}</p>
              </div>
              <i class="fas fa-sun text-yellow-400/20 text-2xl"></i>
            </div>
          </div>
        
          <!-- Total Half Day Box -->
          <div class="bg-white/10 p-2 rounded-lg shadow-lg border border-white/20 hover:border-purple-400/50 transition">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-gray-300 text-xs mb-0">{{ __('dashboard.total_half_day') }}</p>
                <p class="text-lg font-bold text-purple-400">{{ $totalHalfDay }}</p>
                <p class="text-xs text-purple-300">{{ $attendanceStats['half_day_percentage'] }}% {{ __('dashboard.of_month') }}</p>
              </div>
              <i class="fas fa-sun text-purple-400/20 text-2xl"></i>
            </div>
          </div>

          <!-- On Leave Box -->
          <div class="bg-white/10 p-2 rounded-lg shadow-lg border border-white/20 hover:border-blue-400/50 transition">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-gray-300 text-xs mb-0">{{ __('dashboard.total_on_leave') }}</p>
                <p class="text-lg font-bold text-blue-400">{{ (int)$totalOnLeave }}</p>
                <p class="text-xs text-blue-300">{{ (int)$attendanceStats['on_leave_percentage'] }}% {{ __('dashboard.of_month') }}</p>
              </div>
              <i class="fas fa-calendar text-blue-400/20 text-2xl"></i>
            </div>
          </div>

        </div>

      </div>
    </div>
    <!-- ========== END REDESIGNED SECTION ========== -->

    <!-- Attendance History -->
    <div class="bg-white/10 p-8 rounded-xl shadow-lg border border-white/20 mt-8">
      <h2 class="text-2xl font-semibold mb-6 flex items-center">
        <i class="fas fa-history mr-2 text-blue-400"></i>{{ __('dashboard.attendance_history') }} {{ __('dashboard.last_30_days') }}
      </h2>

      <div class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="border-b border-white/20">
              <th class="text-left py-3 px-4">{{ __('dashboard.date') }}</th>
              <th class="text-left py-3 px-4">{{ __('dashboard.status') }}</th>
              <th class="text-left py-3 px-4">{{ __('dashboard.check_in') }}</th>
              <th class="text-left py-3 px-4">{{ __('dashboard.check_out') }}</th>
              <th class="text-left py-3 px-4">{{ __('dashboard.duration') }}</th>
              <th class="text-left py-3 px-4">{{ __('dashboard.remarks') }}</th>
            </tr>
          </thead>
          <tbody>
            @forelse($recentAttendance as $record)
              <tr class="border-b border-white/10 hover:bg-white/5">
                <td class="py-3 px-4">{{ $record->attendance_date->format('Y-m-d (l)') }}</td>
                <td class="py-3 px-4">
                  <span class="px-3 py-1 rounded-full text-sm font-medium
                    @if($record->status === 'present') bg-green-500/20 text-green-300
                    @elseif($record->status === 'absent') bg-red-500/20 text-red-300
                    @elseif($record->status === 'late') bg-yellow-500/20 text-yellow-300
                    @elseif($record->status === 'el') bg-orange-500/20 text-orange-300
                    @elseif($record->status === 'on leave') bg-blue-500/20 text-blue-300
                    @elseif($record->status === 'half day') bg-purple-500/20 text-purple-300
                    @else bg-gray-500/20 text-gray-300 @endif">
                    {{ ucfirst($record->status) }}
                  </span>
                </td>
                <td class="py-3 px-4 text-sm">
                  @if($record->status === 'present' && $record->check_in_time)
                    <span class="text-green-300">{{ substr($record->check_in_time, 0, 5) }}</span>
                  @else
                    <span class="text-gray-500">-</span>
                  @endif
                </td>
                <td class="py-3 px-4 text-sm">
                  @if($record->status === 'present' && $record->check_out_time)
                    <span class="text-blue-300">{{ substr($record->check_out_time, 0, 5) }}</span>
                  @else
                    <span class="text-gray-500">-</span>
                  @endif
                </td>
                <td class="py-3 px-4 text-sm">
                  @if($record->status === 'present' && $record->check_in_time && $record->check_out_time)
                    @php
                      $checkIn = \Carbon\Carbon::createFromFormat('H:i:s', $record->check_in_time);
                      $checkOut = \Carbon\Carbon::createFromFormat('H:i:s', $record->check_out_time);
                      $minutes = abs($checkOut->diffInMinutes($checkIn));
                      $hours = floor($minutes / 60);
                      $remainingMinutes = $minutes % 60;
                    @endphp
                    <span class="text-purple-300">{{ $hours }}h {{ $remainingMinutes }}m</span>
                  @else
                    <span class="text-gray-500">-</span>
                  @endif
                </td>
                <td class="py-3 px-4 text-sm text-gray-400">
                  {{ $record->remarks ?  $record->remarks : '-' }}
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="py-6 text-center text-gray-400">
                  <i class="fas fa-inbox mr-2"></i>{{ __('dashboard.no_records') }}
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

  </main>

</body>

<script>
  // Fetch and display leave status update badge
  async function loadNotificationBadge() {
    try {
      const response = await fetch('{{ route("staff.leave.notifications") }}');
      const data = await response.json();
      
      const badge = document.getElementById('notificationBadge');
      
      if (data.count > 0) {
        badge.textContent = data.count;
        badge.classList.remove('hidden');
      } else {
        badge.classList.add('hidden');
      }
    } catch (error) {
      console.error('Failed to load notification badge:', error);
    }
  }
  
  // Load badge on page load and refresh every 30 seconds
  loadNotificationBadge();
  setInterval(loadNotificationBadge, 30000);

  // ========== GRAFANA PIE CHART AUTO-REFRESH ==========
  
  /**
   * Refresh Grafana pie chart iframe
   */
  function refreshGrafanaChart() {
    const iframe = document.getElementById('grafanaPieChart');
    if (iframe) {
      // Add timestamp to force refresh
      const currentSrc = iframe.src;
      if (currentSrc.includes('_t=')) {
        iframe.src = currentSrc.replace(/_t=\d+/, `_t=${Date.now()}`);
      } else {
        iframe.src = currentSrc + (currentSrc.includes('?') ? '&' : '?') + `_t=${Date.now()}`;
      }
    }
  }

  /**
   * Initialize chart on page load
   */
  document.addEventListener('DOMContentLoaded', () => {
    // Initial load is handled by iframe src
    // Optional: Add manual refresh button if needed
  });

  /**
   * Auto-refresh Grafana chart every 30 seconds
   * Note: Grafana iframe has built-in 30s refresh, this is optional backup
   */
  // Uncomment if you want to force refresh every 30 seconds
  // setInterval(() => {
  //   refreshGrafanaChart();
  // }, 30000);

  // ========== MONTH SELECTOR ==========
  function changeMonth(monthValue) {
    // Redirect to dashboard with selected month
    const url = new URL(window.location);
    url.searchParams.set('month', monthValue);
    window.location.href = url.toString();
  }

  // Reset to current month button
  const resetBtn = document.getElementById('resetBtn');
  if (resetBtn) {
    resetBtn.addEventListener('click', function() {
      const today = new Date();
      const currentMonth = today.getFullYear() + '-' + String(today.getMonth() + 1).padStart(2, '0');
      changeMonth(currentMonth);
    });
  }

  // ========== REFRESH BUTTON ==========
  let lastRefreshTime = new Date();

  // Translation strings
  const translations = {
    justNow: "{{ __('dashboard.just_now') }}",
    minuteAgo: "{{ __('dashboard.minute_ago') }}",
    minutesAgo: "{{ __('dashboard.minutes_ago') }}",
    hourAgo: "{{ __('dashboard.hour_ago') }}",
    hoursAgo: "{{ __('dashboard.hours_ago') }}"
  };

  // Update current date and time
  function updateDateTime() {
    const now = new Date();
    const dateOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    const timeOptions = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true };
    
    // Get locale from document lang attribute or default to 'en-US'
    const locale = document.documentElement.lang || 'en-US';
    document.getElementById('currentDate').textContent = now.toLocaleDateString(locale, dateOptions);
    document.getElementById('currentTime').textContent = now.toLocaleTimeString(locale, timeOptions);
  }

  // Update last refresh time display
  function updateLastRefreshTime() {
    const now = new Date();
    const diff = Math.floor((now - lastRefreshTime) / 1000);
    
    let refreshText = '';
    if (diff < 60) {
      refreshText = translations.justNow;
    } else if (diff < 3600) {
      const minutes = Math.floor(diff / 60);
      refreshText = minutes === 1 ? `1 ${translations.minuteAgo}` : `${minutes} ${translations.minutesAgo}`;
    } else {
      const hours = Math.floor(diff / 3600);
      refreshText = hours === 1 ? `1 ${translations.hourAgo}` : `${hours} ${translations.hoursAgo}`;
    }
    
    document.getElementById('lastRefresh').textContent = refreshText;
  }

  // Refresh button click handler
  document.getElementById('refreshBtn').addEventListener('click', function() {
    const btn = this;
    const icon = btn.querySelector('i');
    
    // Disable button and show loading state
    btn.disabled = true;
    icon.classList.add('animate-spin');
    
    // Refresh the Grafana chart
    refreshGrafanaChart();
    
    // Update last refresh time
    lastRefreshTime = new Date();
    updateLastRefreshTime();
    
    // Re-enable button after 1 second
    setTimeout(() => {
      btn.disabled = false;
      icon.classList.remove('animate-spin');
    }, 1000);
  });

  // Initialize date/time on page load
  updateDateTime();
  updateLastRefreshTime();

  // Update date/time every second
  setInterval(() => {
    updateDateTime();
    updateLastRefreshTime();
  }, 1000);
</script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

<script>
    let attendanceChart = null;
    let autoRefreshInterval = null;

    // Initialize chart
    function initializeChart() {
        const ctx = document.getElementById('attendancePieChart');
        
        if (attendanceChart) {
            attendanceChart.destroy();
        }
        
        Chart.register(ChartDataLabels);
        
        attendanceChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Present', 'Absent', 'Late', 'Leave', 'Half Day'],
                datasets: [{
                    data: [0, 0, 0, 0, 0],
                    backgroundColor: [
                        'rgba(34, 197, 94, 0.7)',
                        'rgba(239, 68, 68, 0.7)',
                        'rgba(234, 179, 8, 0.7)',
                        'rgba(59, 130, 246, 0.7)',
                        'rgba(168, 85, 247, 0.7)'
                    ],
                    borderColor: [
                        'rgba(34, 197, 94, 1)',
                        'rgba(239, 68, 68, 1)',
                        'rgba(234, 179, 8, 1)',
                        'rgba(59, 130, 246, 1)',
                        'rgba(168, 85, 247, 1)'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        align: 'center',
                        labels: {
                            color: 'rgba(255, 255, 255, 0.8)',
                            font: { size: 13 },
                            padding: 12,
                            boxWidth: 12,
                            boxHeight: 12
                        }
                    },
                    datalabels: {
                        display: false
                    },
                    tooltip: {
                        enabled: true,
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 10,
                        titleFont: { size: 13, weight: 'bold' },
                        bodyFont: { size: 12 },
                        callbacks: {
                            label: function(context) {
                                const label = context.chart.data.labels[context.dataIndex];
                                const value = context.raw;
                                return label + ': ' + value;
                            }
                        }
                    }
                }
            }
        });
    }

    // Fetch chart data
    async function updateChartData() {
        try {
            const errorMsg = document.getElementById('chartErrorMessage');
            if (errorMsg) {
                errorMsg.classList.add('hidden');
            }
            
            const month = new URLSearchParams(window.location.search).get('month') || 
                         new Date().toISOString().slice(0, 7);
            
            const response = await fetch(`/staff/pie-chart-data?month=${month}`);
            const result = await response.json();
            
            if (result.success && attendanceChart) {
                // Update chart data and labels
                attendanceChart.data.labels = result.data.labels;
                attendanceChart.data.datasets[0].data = result.data.datasets[0].data;
                attendanceChart.data.datasets[0].backgroundColor = result.data.datasets[0].backgroundColor;
                attendanceChart.data.datasets[0].borderColor = result.data.datasets[0].borderColor;
                attendanceChart.update('none');
            } else if (!result.success) {
                throw new Error(result.message);
            }
        } catch (error) {
            console.error('Chart update error:', error);
            const errorMsg = document.getElementById('chartErrorMessage');
            if (errorMsg) {
                errorMsg.textContent = 'Error loading chart: ' + error.message;
                errorMsg.classList.remove('hidden');
            }
        }
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        initializeChart();
        updateChartData();
        
        // Auto-refresh every 10 seconds
        autoRefreshInterval = setInterval(updateChartData, 10000);
    });

    // Cleanup on page unload
    window.addEventListener('beforeunload', function() {
        if (autoRefreshInterval) {
            clearInterval(autoRefreshInterval);
        }
        if (attendanceChart) {
            attendanceChart.destroy();
        }
    });
</script>
</body>
</html>