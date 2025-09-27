# =============================
# One‑click Next.js runner (Windows)
# - Installs Node LTS (via winget) if absent
# - Installs deps (npm/yarn/pnpm)
# - Builds project
# - Starts server in background (hidden)
# - Opens browser automatically
# Logs: .\.run\next.log and error.log
# =============================


$ErrorActionPreference = 'Stop'


function Write-Info($msg) { Write-Host "[INFO] $msg" -ForegroundColor Cyan }
function Write-Warn($msg) { Write-Host "[WARN] $msg" -ForegroundColor Yellow }
function Write-Err($msg) { Write-Host "[ERROR] $msg" -ForegroundColor Red }


# Move to script directory (project root)
Set-Location -Path $PSScriptRoot


# Create log folder
$runDir = Join-Path $PSScriptRoot '.run'
if (-not (Test-Path $runDir)) { New-Item -ItemType Directory -Path $runDir | Out-Null }
$stdoutLog = Join-Path $runDir 'next.log'
$stderrLog = Join-Path $runDir 'error.log'


# --- 1) Ensure Node.js is installed ---
function Ensure-Node() {
if (Get-Command node -ErrorAction SilentlyContinue) {
Write-Info "Node found: $(node -v)"
return
}
Write-Warn "Node.js not found. Attempting to install Node LTS via winget..."
try {
# Silent install Node LTS
winget install -e --id OpenJS.NodeJS.LTS --silent --accept-package-agreements --accept-source-agreements
# Refresh current session PATH
$env:Path = [System.Environment]::GetEnvironmentVariable('Path','Machine') + ';' + [System.Environment]::GetEnvironmentVariable('Path','User')


if (Get-Command node -ErrorAction SilentlyContinue) {
Write-Info "Installed Node: $(node -v)"
} else {
throw "Node appears not installed after winget run."
}
}
catch {
Write-Err "Failed to install Node automatically. Please install Node LTS manually from https://nodejs.org and run this again. $_"
exit 1
}
}


# --- 2) Pick package manager ---
function Detect-PM() {
if (Test-Path (Join-Path $PSScriptRoot 'pnpm-lock.yaml')) { return 'pnpm' }
if (Test-Path (Join-Path $PSScriptRoot 'yarn.lock')) { return 'yarn' }
return 'npm'
}


# --- 3) Find a free port (3000..3010) ---
function Get-FreePort([int]$start=3000, [int]$end=3010) {
for ($p=$start; $p -le $end; $p++) {
$inUse = Test-NetConnection -ComputerName '127.0.0.1' -Port $p -InformationLevel Quiet
if (-not $inUse) { return $p }
}
throw "No free port between $start and $end."
}


# --- 4) Run a command and stop on error ---
function Run($cmd, $args) {
Write-Info "$cmd $args"
$p = Start-Process -FilePath $cmd -ArgumentList $args -NoNewWindow -PassThru -Wait
if ($p.ExitCode -ne 0) { throw "$cmd failed with exit code $($p.ExitCode)" }
}


# --- Execute ---
Write-Info "Done. If the page doesn't load yet, wait a few seconds while Next.js warms up."