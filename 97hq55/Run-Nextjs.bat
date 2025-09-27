@echo off
setlocal

where node >nul 2>nul
if %errorlevel% neq 0 (
    echo [WARN] ไม่พบ Node.js

    rem --- ลองใช้ winget ถ้ามี ---
    where winget >nul 2>nul
    if %errorlevel% eq 0 (
        echo [INFO] กำลังติดตั้ง Node LTS ผ่าน winget...
        winget install -e --id OpenJS.NodeJS.LTS --silent --accept-package-agreements --accept-source-agreements
    ) else (
        rem --- ถ้าไม่มี winget ลองใช้ choco ---
        where choco >nul 2>nul
        if %errorlevel% eq 0 (
            echo [INFO] กำลังติดตั้ง Node LTS ผ่าน Chocolatey...
            choco install nodejs-lts -y
        ) else (
            echo [ERROR] ไม่มี winget หรือ chocolatey
            echo กรุณาติดตั้ง Node.js เองจาก https://nodejs.org แล้วลองใหม่
            pause
            exit /b 1
        )
    )
)

echo [INFO] Node version:
node -v

echo [INFO] กำลัง build โปรเจกต์...
call npm run build

echo [INFO] กำลังรัน npm start แบบ background...
start "" /min cmd /c "npm start > .run\next.log 2>&1"

start http://localhost:3000
echo [INFO] เสร็จสิ้น
endlocal
