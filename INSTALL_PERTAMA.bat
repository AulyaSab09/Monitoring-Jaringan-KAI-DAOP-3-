@echo off
TITLE Instalasi Awal Project Laravel

echo ===================================================
echo   SEDANG MENYIAPKAN APLIKASI UNTUK PERTAMA KALI
echo   Pastikan komputer sudah terinstall PHP & Composer
echo ===================================================
echo.

 1. Install Library Laravel (Memunculkan folder vendor)
echo [14] Sedang download library (Composer Install)...
echo Mohon tunggu, butuh kuota internet...
call composer install

 2. Membuat file .env (Settingan Environment)
echo.
echo [24] Menyiapkan file setting (.env)...
copy .env.example .env

 3. Generate Kunci Aplikasi
echo.
echo [34] Membuat kunci rahasia aplikasi...
call php artisan keygenerate

 4. Setup Database
echo.
echo [44] Menyiapkan Database...
touch databasedatabase.sqlite
call php artisan migrate --force

echo.
echo ===================================================
echo   INSTALASI SELESAI!
echo   Sekarang Anda bisa klik file START_MONITOR.bat
echo ===================================================
pause