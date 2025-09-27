@echo off
setlocal EnableExtensions EnableDelayedExpansion

REM =====================================================
REM next_run_fix_v2_no_where.bat
REM - No dependency on where.exe
REM - Refresh PATH from registry + common Node locations
REM - Detect npm/yarn/pnpm by lockfile
REM - Install deps and run `dev`, logging to .run\*.log
REM - Verbose console messages for visibility
REM =====================================================

cd /d "%~dp0"
if not exist ".run" mkdir ".run"
set "SETUP_LOG=.run\setup.log"
set "DEV_LOG=.run\dev.log"
echo ==== START %DATE% %TIME% ==== > "%SETUP_LOG%"
echo [INFO] Working dir: %cd%
echo [INFO] Logs: %SETUP_LOG% and %DEV_LOG%

echo [STEP] Refresh PATH...
for /f "tokens=2*" %%A in ('reg query "HKLM\SYSTEM\CurrentControlSet\Control\Session Manager\Environment" /v Path') do set "MACHINE_PATH=%%B"
for /f "tokens=2*" %%A in ('reg query "HKCU\Environment" /v Path 2^>nul') do set "USER_PATH=%%B"
if defined MACHINE_PATH set "PATH=%MACHINE_PATH%"
if defined USER_PATH   set "PATH=%PATH%;%USER_PATH%"

REM Add common Node install paths
if exist "C:\Windows\System32" set "PATH=C:\Windows\System32;%PATH%"
if exist "C:\Program Files\nodejs\node.exe"               set "PATH=C:\Program Files\nodejs;%PATH%"
if exist "%ProgramData%\chocolatey\bin\node.exe"          set "PATH=%ProgramData%\chocolatey\bin;%PATH%"
if exist "%LocalAppData%\Programs\nodejs\node.exe"        set "PATH=%LocalAppData%\Programs\nodejs;%PATH%"
if exist "%UserProfile%\AppData\Local\Programs\nodejs\node.exe" set "PATH=%UserProfile%\AppData\Local\Programs\nodejs;%PATH%"

echo [STEP] Node/npm versions...
for %%X in (node npm) do (
  call %%X -v 1>>"%SETUP_LOG%" 2>>&1
  if errorlevel 1 (
    echo [WARN] %%X not invokable yet.>> "%SETUP_LOG%"
  )
)
for %%X in (node npm) do (
  call %%X -v
)

REM Ensure project
if not exist "package.json" (
  echo [ERROR] package.json not found in "%cd%".>> "%SETUP_LOG%"
  echo [ERROR] package.json not found. Place this script in your Next.js project folder.
  echo ==== END %DATE% %TIME% ====>> "%SETUP_LOG%"
  exit /b 1
)

REM Detect package manager
set "PM=npm"
if exist "yarn.lock" set "PM=yarn"
if exist "pnpm-lock.yaml" set "PM=pnpm"
echo [STEP] Package manager: %PM%
echo [INFO] PM=%PM%>> "%SETUP_LOG%"

REM Install deps
echo [STEP] Installing dependencies (this may take a while)...
if /i "%PM%"=="yarn" (
  if exist "yarn.lock" (
    call yarn install --frozen-lockfile >> "%SETUP_LOG%" 2>&1
  ) else (
    call yarn install >> "%SETUP_LOG%" 2>&1
  )
) else if /i "%PM%"=="pnpm" (
  if exist "pnpm-lock.yaml" (
    call pnpm install --frozen-lockfile >> "%SETUP_LOG%" 2>&1
  ) else (
    call pnpm install >> "%SETUP_LOG%" 2>&1
  )
) else (
  if exist "package-lock.json" (
    call npm ci >> "%SETUP_LOG%" 2>&1
  ) else (
    call npm install >> "%SETUP_LOG%" 2>&1
  )
)

if errorlevel 1 (
  echo [ERROR] Dependency install failed. See %SETUP_LOG%
  echo [ERROR] Dependency install failed.>> "%SETUP_LOG%"
  echo ==== END %DATE% %TIME% ====>> "%SETUP_LOG%"
  exit /b 1
)

REM Check "dev" script
set "HAS_DEV="
for /f "usebackq delims=" %%L in (`findstr /irc:"\"dev\"\s*:" package.json`) do set "HAS_DEV=1"
if not defined HAS_DEV (
  echo [ERROR] No "dev" script in package.json. Expected something like:>> "%SETUP_LOG%"
  echo [ERROR]   "scripts": { "dev": "next dev" }>> "%SETUP_LOG%"
  echo [ERROR] "dev" script missing. Aborting.
  echo ==== END %DATE% %TIME% ====>> "%SETUP_LOG%"
  exit /b 1
)

REM Free port 3000 if busy (use netstat only if available)
echo [STEP] Freeing port 3000 if occupied...
if exist "%SystemRoot%\System32\netstat.exe" (
  for /f "tokens=5" %%P in ('"%SystemRoot%\System32\netstat.exe" -ano ^| findstr :3000 ^| findstr LISTENING') do set "PID3000=%%P"
  if defined PID3000 (
    echo [INFO] Killing PID %PID3000% on :3000 >> "%SETUP_LOG%"
    taskkill /F /PID %PID3000% >> "%SETUP_LOG%" 2>&1
  )
) else (
  echo [WARN] netstat.exe not available; skipping port free.>> "%SETUP_LOG%"
)

REM Start dev server in a new window and open browser
echo [STEP] Starting dev server in a new window (output -> %DEV_LOG%)...
[cite_start]if exist "%DEV_LOG%" del "%DEV_LOG%" >nul 2>&1 [cite: 8]

if /i "%PM%"=="yarn" (
  start "Yarn Dev Server" cmd /c "yarn run dev > "%DEV_LOG%" 2>&1"
) else if /i "%PM%"=="pnpm" (
  start "PNPM Dev Server" cmd /c "pnpm run dev > "%DEV_LOG%" 2>&1"
) else (
  start "NPM Dev Server" cmd /c "npm run dev > "%DEV_LOG%" 2>&1"
)

echo [STEP] Waiting 5 seconds for the server to initialize...
timeout /t 5 /nobreak >nul

echo [STEP] Opening local file C:\Users\Administrator\Downloads\MANUAL_SETUP\setting.html
start "" "file:///C:/Users/Administrator/Downloads/MANUAL_SETUP/setting.html"

echo [INFO] Dev server started. Check the new window or %DEV_LOG% for output.
echo ==== END %DATE% %TIME% ====>> "%SETUP_LOG%"
echo [DONE] Script finished.
[cite_start]endlocal [cite: 9]
exit /b 0
