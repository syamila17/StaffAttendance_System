# Configure Windows Hosts File for Staff Attendance System
# Run as Administrator

$hostsFile = "C:\Windows\System32\drivers\etc\hosts"

# Check if running as Administrator
if (-NOT ([Security.Principal.WindowsPrincipal] [Security.Principal.WindowsIdentity]::GetCurrent()).IsInRole([Security.Principal.WindowsRole]::Administrator)) {
    Write-Host "This script must be run as Administrator!" -ForegroundColor Red
    Exit
}

# Entries to add
$entries = @(
    "127.0.0.1       sistemkehadiranUtm.staff",
    "127.0.0.1       sistemkehadiranUtm.admin"
)

# Read current hosts file
$hostsContent = Get-Content $hostsFile

# Add entries if they don't exist
$updated = $false
foreach ($entry in $entries) {
    if ($hostsContent -notcontains $entry) {
        Add-Content -Path $hostsFile -Value $entry
        Write-Host "Added: $entry" -ForegroundColor Green
        $updated = $true
    } else {
        Write-Host "Already exists: $entry" -ForegroundColor Yellow
    }
}

if ($updated) {
    Write-Host "`nConfiguration Complete!" -ForegroundColor Green
    Write-Host "`nYou can now access the system at:" -ForegroundColor Cyan
    Write-Host "  Staff:  http://sistemkehadiranUtm.staff:8000" -ForegroundColor White
    Write-Host "  Admin:  http://sistemkehadiranUtm.admin:8000" -ForegroundColor White
} else {
    Write-Host "`nAll entries already configured!" -ForegroundColor Green
}

Write-Host "`nPress Enter to exit..." -ForegroundColor Gray
Read-Host
