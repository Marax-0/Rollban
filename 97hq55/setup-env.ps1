# PowerShell script สำหรับสร้างไฟล์ .env.local
# รันสคริปต์นี้เพื่อตั้งค่า Google TTS API Key

$envContent = @"
# Database Configuration for MSSQL
DB_USER=your_username
DB_PASSWORD=your_password
DB_SERVER=your_server_name_or_ip
DB_NAME=your_database_name

# Optional: Database Port (if not using default 1433)
DB_PORT=1433

# Optional: Connection Timeout (in milliseconds)
DB_TIMEOUT=30000

# Optional: Connection Pool Settings
DB_POOL_MIN=0
DB_POOL_MAX=10

# Environment
NODE_ENV=development

# Google Text-to-Speech API Configuration
NEXT_PUBLIC_GOOGLE_TTS_API_KEY=AIzaSyBQQZs6vlHEnrWYztiSELPkjiQSpmeVSjs
"@

# สร้างไฟล์ .env.local
$envContent | Out-File -FilePath ".env.local" -Encoding UTF8

Write-Host "✅ สร้างไฟล์ .env.local เรียบร้อยแล้ว!" -ForegroundColor Green
Write-Host "🔑 Google TTS API Key ถูกตั้งค่าแล้ว" -ForegroundColor Yellow
Write-Host ""
Write-Host "ขั้นตอนถัดไป:" -ForegroundColor Cyan
Write-Host "1. รีสตาร์ทเซิร์ฟเวอร์ development (npm run dev)" -ForegroundColor White
Write-Host "2. ทดสอบที่ http://localhost:3000/tts-test" -ForegroundColor White
Write-Host ""
Write-Host "หมายเหตุ: ไฟล์ .env.local จะไม่ถูก commit ขึ้น git" -ForegroundColor Gray
