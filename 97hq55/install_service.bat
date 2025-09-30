@echo off
setlocal EnableDelayedExpansion

REM ====== ตั้งค่าพื้นฐาน (ไม่จำเป็นต้องเปลี่ยน) ======
set SERVICE_NAME=SmartSobsuan-Web
set PROJECT_DIR=%~dp0
set LOG_DIR=%PROJECT_DIR%_logs
set NSSM_EXE=%PROJECT_DIR%tools\nssm.exe

REM ====== ขอสิทธิ์ผู้ดูแลระบบ (Admin) ถ้ายังไม่ได้ ======
>nul 2>&1 net session
if not %errorLevel%==0 (
  echo ขอสิทธิ์ผู้ดูแลระบบเพื่อดำเนินการ...
  powershell -NoProfile -ExecutionPolicy Bypass -Command "Start-Process -FilePath '%~f0' -Verb RunAs"
  exit /b
)

REM ====== ตรวจ nssm.exe ======
if not exist "%NSSM_EXE%" (
  echo ไม่พบ %NSSM_EXE%
  echo กรุณาดาวน์โหลด nssm.exe (64-bit) แล้ววางไว้ที่: tools\nssm.exe
  echo จากนั้นดับเบิลคลิกไฟล์นี้อีกครั้ง
  pause
  exit /b 1
)

REM ====== ตรวจ Node.js/NPM ======
where node >nul 2>&1
if not %errorLevel%==0 (
  echo ไม่พบ Node.js บนเครื่องนี้ กรุณาติดตั้ง Node.js ก่อน แล้วรันไฟล์นี้ใหม่
  start "" "https://nodejs.org/"
  pause
  exit /b 1
)
where npm >nul 2>&1
if not %errorLevel%==0 (
  echo ไม่พบ NPM บนเครื่องนี้ กรุณาติดตั้ง Node.js (พร้อม npm) แล้วรันไฟล์นี้ใหม่
  start "" "https://nodejs.org/"
  pause
  exit /b 1
)

REM ====== ติดตั้ง dependency (อัตโนมัติ) ======
cd /d "%PROJECT_DIR%"
if exist "%PROJECT_DIR%package-lock.json" (
  call npm ci
) else (
  call npm install
)

REM ====== Build โปรเจกต์ (โหมดโปรดักชัน) ======
call npm run build || (
  echo Build ล้มเหลว กรุณาตรวจสอบโค้ด/สคริปต์ใน package.json
  pause
  exit /b 1
)

REM ====== เตรียมโฟลเดอร์ Log ======
if not exist "%LOG_DIR%" mkdir "%LOG_DIR%"

REM ====== ติดตั้ง Service ด้วย NSSM ======
"%NSSM_EXE%" stop %SERVICE_NAME% >nul 2>&1
"%NSSM_EXE%" remove %SERVICE_NAME% confirm >nul 2>&1

REM ให้ Service รันคำสั่ง start ของ Next.js ในโฟลเดอร์โปรเจกต์
"%NSSM_EXE%" install %SERVICE_NAME% "C:\Windows\System32\cmd.exe" /c "npm run start"
"%NSSM_EXE%" set %SERVICE_NAME% AppDirectory "%PROJECT_DIR%"
"%NSSM_EXE%" set %SERVICE_NAME% AppEnvironmentExtra "NODE_ENV=production"
"%NSSM_EXE%" set %SERVICE_NAME% AppStdout "%LOG_DIR%\out.log"
"%NSSM_EXE%" set %SERVICE_NAME% AppStderr "%LOG_DIR%\err.log"
"%NSSM_EXE%" set %SERVICE_NAME% Start SERVICE_AUTO_START

REM เริ่มบริการ
"%NSSM_EXE%" start %SERVICE_NAME%

echo.
echo ==========================================================
echo  ติดตั้งและเริ่มบริการเรียบร้อยแล้ว: %SERVICE_NAME%
echo  เปิดเว็บเบราว์เซอร์ไปที่ http://localhost:3000/
echo  Log: %LOG_DIR%\out.log และ %LOG_DIR%\err.log
echo ==========================================================
echo.

start "" "%PROJECT_DIR%Open-App.html"
pause
