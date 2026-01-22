Set WshShell = CreateObject("WScript.Shell") 
' Script ini akan menjalankan file .bat di atas tanpa membuka jendela (angka 0)
WshShell.Run chr(34) & "C:\laragon\www\SistemMonitoringJaringan-KAI-DAOP-3-CIREBON\run-monitor.bat" & Chr(34), 0
Set WshShell = Nothing