@echo off
set SERVICE_NAME=SmartSobsuan-Web
set NSSM_EXE=%~dp0tools\nssm.exe

>nul 2>&1 net session
if not %errorLevel%==0 (
  powershell -NoProfile -ExecutionPolicy Bypass -Command "Start-Process -FilePath '%~f0' -Verb RunAs"
  exit /b
)

"%NSSM_EXE%" stop %SERVICE_NAME%
echo หยุดบริการเรียบร้อย
pause
