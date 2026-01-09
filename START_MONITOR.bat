@echo off
TITLE Launcher Monitoring Jaringan

echo ===================================================
echo   STARTING NETWORK MONITOR SYSTEM
echo ===================================================
echo.

:: 1. Jalankan PHP Artisan Serve (Server Web)
:: Menggunakan /min agar jendelanya terbuka dalam keadaan minimize (tidak mengganggu)
echo [1/3] Menyalakan Web Server...
start "WEB SERVER (JANGAN DITUTUP)" /min cmd /k "php artisan serve"

:: 2. Jalankan Perintah Device Check (Ping Robot)
echo [2/3] Menyalakan Robot Ping...
start "ROBOT PING (JANGAN DITUTUP)" /min cmd /k "php artisan device:check"

:: 3. Tunggu 3 detik (memberi waktu agar server siap)
echo [3/3] Membuka Browser...
timeout /t 3 /nobreak >nul

:: 4. Buka Browser Chrome/Edge otomatis ke halaman preview
start http://127.0.0.1:8000/preview

echo.
echo ===================================================
echo   BERHASIL! 
echo   Sistem berjalan di latar belakang.
echo   Jangan tutup jendela terminal yang muncul.
echo ===================================================
pause