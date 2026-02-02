@echo off
title KAI Network Monitor

:: 1. Masuk ke folder project
cd /d "C:\laragon\www\SistemMonitoringJaringan-KAI-DAOP-3-CIREBON"

:loop
echo Memulai Monitoring Jaringan...

:: 2. Jalankan monitoring (Menggunakan versi 8.3.28 sesuai screenshot kamu)
"C:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe" artisan device:check

echo Proses berhenti atau crash!
echo Memulai ulang dalam 5 detik...
timeout /t 5 >nul
goto loop