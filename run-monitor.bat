@echo off
title KAI Network Monitor

:: 1. Masuk ke folder project Anda
cd /d "C:\laragon\www\SistemMonitoringJaringan-KAI-DAOP-3-CIREBON"

:loop
echo Memulai Monitoring Jaringan...

:: 2. Jalankan perintah monitoring menggunakan PHP Laragon yang spesifik
"C:\laragon\bin\php\php-8.3.26-Win32-vs16-x64\php.exe" artisan device:check

echo Proses berhenti atau crash! Memulai ulang dalam 5 detik...
timeout /t 5 >nul
goto loop