<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Attendance Reports</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>
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
        <a href="{{ route('admin.attendance.report') }}" class="block px-4 py-2 rounded-lg bg-white/30">
          <i class="fas fa-chart-bar mr-2"></i>Reports
        </a>
        <a href="{{ route('admin.departments') }}" class="block px-4 py-2 rounded-lg hover:bg-white/20 transition">
          <i class="fas fa-building mr-2"></i>Departments
        </a>
        <a href="{{ route('admin.leave.requests') }}" class="block px-4 py-2 rounded-lg hover:bg-white/20 transition">
          <i class="fas fa-calendar-times mr-2"></i>Leave Requests
        </a>
        <a href="{{ route('admin.logout') }}" class="block px-4 py-2 rounded-lg hover:bg-white/20 transition">
          <i class="fas fa-sign-out-alt mr-2"></i>Logout
        </a>
      </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto p-8">
      <div class="max-w-6xl">
        <h1 class="text-4xl font-bold mb-2">Attendance Reports</h1>
        <p class="text-gray-400 mb-8">View attendance statistics and trends</p>

        <!-- Filters & Export -->
        <div class="flex justify-between items-start mb-8 gap-4">
          <div class="flex-1">
            <div class="bg-gray-800 rounded-lg border border-gray-700 p-6">
              <h2 class="text-lg font-semibold mb-4">Filters</h2>
              <form method="GET" action="{{ route('admin.attendance.report') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
              <label class="block text-sm mb-2 text-white">Start Date</label>
              <input type="date" name="start_date" value="{{ $startDate }}" 
                class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded text-white">
            </div>
            <div>
              <label class="block text-sm mb-2 text-white">End Date</label>
              <input type="date" name="end_date" value="{{ $endDate }}" 
                class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded text-white">
            </div>
            <div>
              <label class="block text-sm mb-2 text-white">Staff Member</label>
              <select name="staff_id" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded text-white">
                <option value="">All Staff</option>
                @foreach($staff as $person)
                  <option value="{{ $person->staff_id }}" style="color: black;">{{ $person->staff_name }}</option>
                @endforeach
              </select>
            </div>
            <div class="flex items-end">
              <button type="submit" class="w-full px-4 py-2 bg-orange-600 hover:bg-orange-700 rounded transition text-white">
                <i class="fas fa-search mr-2"></i>Filter
              </button>
            </div>
          </form>
            </div>
          </div>
          <div class="flex flex-col gap-2 h-fit">
            <button onclick="generatePDF()" class="px-6 py-3 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 rounded-lg transition text-white font-semibold shadow-lg text-sm" title="Export as PDF">
              <i class="fas fa-file-pdf mr-2"></i>Export PDF
            </button>
            <button onclick="printReport()" class="px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 rounded-lg transition text-white font-semibold shadow-lg text-sm" title="Print Report">
              <i class="fas fa-print mr-2"></i>Print
            </button>
          </div>
        </div>

        <!-- Summary Stats -->
        <div class="grid grid-cols-5 gap-4 mb-8">
          <div class="bg-gray-800 p-4 rounded-lg border border-gray-700">
            <p class="text-gray-400 text-sm">Total Records</p>
            <p class="text-3xl font-bold text-white">{{ $summary['total_records'] }}</p>
          </div>
          <div class="bg-green-900/30 p-4 rounded-lg border border-green-700">
            <p class="text-green-300 text-sm">Present</p>
            <p class="text-3xl font-bold text-green-300">{{ $summary['present'] }}</p>
          </div>
          <div class="bg-red-900/30 p-4 rounded-lg border border-red-700">
            <p class="text-red-300 text-sm">Absent</p>
            <p class="text-3xl font-bold text-red-300">{{ $summary['absent'] }}</p>
          </div>
          <div class="bg-yellow-900/30 p-4 rounded-lg border border-yellow-700">
            <p class="text-yellow-300 text-sm">Late</p>
            <p class="text-3xl font-bold text-yellow-300">{{ $summary['late'] }}</p>
          </div>
          <div class="bg-blue-900/30 p-4 rounded-lg border border-blue-700">
            <p class="text-blue-300 text-sm">Leave</p>
            <p class="text-3xl font-bold text-blue-300">{{ $summary['leave'] }}</p>
          </div>
        </div>

        <!-- Attendance Table -->
        <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-x-auto shadow-xl" id="reportTable">
          <table class="w-full min-w-max">
            <thead class="bg-gray-700 border-b border-gray-600">
              <tr>
                <th class="px-4 py-4 text-left text-lg font-semibold">Date</th>
                <th class="px-4 py-4 text-left text-lg font-semibold">Staff Name</th>
                <th class="px-4 py-4 text-left text-lg font-semibold">Status</th>
                <th class="px-4 py-4 text-left text-lg font-semibold">Check-in</th>
                <th class="px-4 py-4 text-left text-lg font-semibold">Check-out</th>
                <th class="px-4 py-4 text-left text-lg font-semibold">Duration</th>
              </tr>
            </thead>
            <tbody>
              @forelse($attendanceRecords as $record)
                <tr class="border-b border-gray-700 hover:bg-gray-750 transition">
                  <td class="px-4 py-4 whitespace-nowrap text-base">{{ $record->attendance_date->format('Y-m-d') }}</td>
                  <td class="px-4 py-4 whitespace-nowrap text-base">{{ $record->staff->staff_name }}</td>
                  <td class="px-4 py-4 whitespace-nowrap">
                    <span class="px-2 py-1 rounded text-xs capitalize
                      @if($record->status === 'present') bg-green-500/20 text-green-300
                      @elseif($record->status === 'late') bg-yellow-500/20 text-yellow-300
                      @elseif($record->status === 'leave') bg-blue-500/20 text-blue-300
                      @else bg-red-500/20 text-red-300 @endif">
                      {{ $record->status }}
                    </span>
                  </td>
                  <td class="px-4 py-4 whitespace-nowrap text-base">{{ $record->check_in_time ?? '-' }}</td>
                  <td class="px-4 py-4 whitespace-nowrap text-base">{{ $record->check_out_time ?? '-' }}</td>
                  <td class="px-4 py-4 whitespace-nowrap text-base">
                    @if($record->check_in_time && $record->check_out_time)
                      @php
                        $checkIn = \Carbon\Carbon::createFromFormat('H:i:s', $record->check_in_time);
                        $checkOut = \Carbon\Carbon::createFromFormat('H:i:s', $record->check_out_time);
                        $totalMinutes = abs($checkOut->diffInMinutes($checkIn));
                        $hours = floor($totalMinutes / 60);
                        $minutes = $totalMinutes % 60;
                      @endphp
                      {{ $hours }}h {{ $minutes }}m
                    @else
                      -
                    @endif
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="px-4 py-8 text-center text-gray-400 text-base">
                    No records found for the selected period
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </main>
  </div>

  <!-- PDF Export Script -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
  <script>
    function generatePDF() {
      // Create a formatted document for PDF
      const doc = new jspdf.jsPDF({
        orientation: 'landscape',
        unit: 'mm',
        format: 'a4'
      });

      const pageHeight = doc.internal.pageSize.getHeight();
      const pageWidth = doc.internal.pageSize.getWidth();
      let yPosition = 15;

      // Title
      doc.setFontSize(18);
      doc.setFont(undefined, 'bold');
      doc.text('Attendance Report', pageWidth / 2, yPosition, { align: 'center' });
      yPosition += 10;

      // Report Details
      doc.setFontSize(10);
      doc.setFont(undefined, 'normal');
      const startDate = new URLSearchParams(window.location.search).get('start_date') || 'All';
      const endDate = new URLSearchParams(window.location.search).get('end_date') || 'All';
      doc.text(`Generated: ${new Date().toLocaleDateString()}`, pageWidth / 2, yPosition, { align: 'center' });
      yPosition += 5;
      doc.text(`Period: ${startDate} to ${endDate}`, pageWidth / 2, yPosition, { align: 'center' });
      yPosition += 10;

      // Summary Stats
      doc.setFont(undefined, 'bold');
      doc.setFontSize(9);
      doc.text('Summary:', 15, yPosition);
      yPosition += 5;

      const summaryText = [
        `Total Records: {{ $summary['total_records'] }} | Present: {{ $summary['present'] }} | Absent: {{ $summary['absent'] }} | Late: {{ $summary['late'] }} | Leave: {{ $summary['leave'] }}`
      ];

      doc.setFont(undefined, 'normal');
      doc.setFontSize(8);
      summaryText.forEach(text => {
        doc.text(text, 15, yPosition);
        yPosition += 4;
      });
      yPosition += 3;

      // Table
      const rows = [];
      const tableData = document.querySelectorAll('#reportTable tbody tr');
      
      tableData.forEach(row => {
        const cells = row.querySelectorAll('td');
        if (cells.length > 0) {
          rows.push([
            cells[0].textContent.trim(),
            cells[1].textContent.trim(),
            cells[2].textContent.trim(),
            cells[3].textContent.trim(),
            cells[4].textContent.trim(),
            cells[5].textContent.trim()
          ]);
        }
      });

      doc.autoTable({
        startY: yPosition,
        head: [['Date', 'Staff Name', 'Status', 'Check-in', 'Check-out', 'Duration']],
        body: rows,
        theme: 'grid',
        headStyles: {
          fillColor: [51, 65, 85],
          textColor: [255, 255, 255],
          fontStyle: 'bold',
          fontSize: 9,
          cellPadding: 3
        },
        bodyStyles: {
          fontSize: 8,
          cellPadding: 2
        },
        alternateRowStyles: {
          fillColor: [242, 242, 242]
        },
        margin: { top: 10, right: 10, bottom: 10, left: 10 }
      });

      // Footer
      const pageCount = doc.internal.getNumberOfPages();
      for (let i = 1; i <= pageCount; i++) {
        doc.setPage(i);
        doc.setFontSize(8);
        doc.text(`Page ${i} of ${pageCount}`, pageWidth - 15, pageHeight - 10, { align: 'right' });
      }

      doc.save('attendance-report.pdf');
    }

    function printReport() {
      // Create a formatted print window
      const printWindow = window.open('', '', 'height=600,width=800');
      const tableData = document.getElementById('reportTable').innerHTML;
      
      const html = `
        <!DOCTYPE html>
        <html>
        <head>
          <title>Attendance Report</title>
          <style>
            body {
              font-family: Arial, sans-serif;
              padding: 20px;
              background: white;
            }
            .header {
              text-align: center;
              margin-bottom: 20px;
            }
            .header h1 {
              margin: 0 0 10px 0;
              font-size: 24px;
              color: #1a1a1a;
            }
            .report-info {
              text-align: center;
              margin-bottom: 20px;
              font-size: 12px;
              color: #666;
            }
            .summary {
              background: #f5f5f5;
              padding: 10px;
              margin-bottom: 20px;
              border-radius: 4px;
              font-size: 12px;
            }
            table {
              width: 100%;
              border-collapse: collapse;
              margin-top: 10px;
            }
            thead {
              background: #374151;
              color: white;
            }
            th {
              padding: 10px;
              text-align: left;
              font-weight: bold;
              border: 1px solid #ddd;
              font-size: 12px;
            }
            td {
              padding: 8px;
              border: 1px solid #ddd;
              font-size: 11px;
            }
            tbody tr:nth-child(even) {
              background: #f9f9f9;
            }
            tbody tr:hover {
              background: #f0f0f0;
            }
            .footer {
              margin-top: 20px;
              text-align: center;
              font-size: 10px;
              color: #999;
              border-top: 1px solid #ddd;
              padding-top: 10px;
            }
            @media print {
              body {
                margin: 0;
                padding: 10px;
              }
              .no-print {
                display: none;
              }
            }
          </style>
        </head>
        <body>
          <div class="header">
            <h1>Attendance Report</h1>
          </div>
          
          <div class="report-info">
            <p>Generated: ${new Date().toLocaleDateString()} at ${new Date().toLocaleTimeString()}</p>
          </div>

          <div class="summary">
            <strong>Summary:</strong><br>
            Total Records: {{ $summary['total_records'] }} | 
            Present: {{ $summary['present'] }} | 
            Absent: {{ $summary['absent'] }} | 
            Late: {{ $summary['late'] }} | 
            Leave: {{ $summary['leave'] }}
          </div>

          ${tableData}

          <div class="footer">
            <p>This report was generated on ${new Date().toLocaleDateString()}. For more information, please contact your administrator.</p>
          </div>
        </body>
        </html>
      `;
      
      printWindow.document.write(html);
      printWindow.document.close();
      printWindow.focus();
      setTimeout(() => {
        printWindow.print();
      }, 250);
    }
  </script>

  <!-- Print Styles -->
  <style media="print">
    body {
      margin: 0;
      padding: 10px;
    }
    aside {
      display: none;
    }
    main {
      padding: 10px;
    }
    .flex.justify-between {
      display: none;
    }
    .grid.grid-cols-5 {
      display: none;
    }
    #reportTable {
      border: none;
      overflow: visible;
    }
  </style>

</body>
</html>
