@echo off
echo สร้างไฟล์ .env.local สำหรับ Google TTS API Key...
echo.

echo NEXT_PUBLIC_GOOGLE_TTS_API_KEY=AIzaSyBQQZs6vlHEnrWYztiSELPkjiQSpmeVSjs > .env.local

echo ✅ สร้างไฟล์ .env.local เรียบร้อยแล้ว!
echo 🔑 Google TTS API Key ถูกตั้งค่าแล้ว
echo.
echo ขั้นตอนถัดไป:
echo 1. รีสตาร์ทเซิร์ฟเวอร์ development (npm run dev)
echo 2. ทดสอบที่ http://localhost:3000/tts-test
echo.
echo หมายเหตุ: ไฟล์ .env.local จะไม่ถูก commit ขึ้น git
echo.
pause
