@echo off
echo Running PHP script at %date% %time% >> "C:\xampp\htdocs\PMO\log.txt"
php "C:\xampp\htdocs\PMO\run_inactive_check.php" >> "C:\xampp\htdocs\PMO\log.txt" 2>&1
echo Script finished at %date% %time% >> "C:\xampp\htdocs\PMO\log.txt"
