-- Fix Admin-Benutzer Passwort
-- Setzt das Passwort für den Admin-Benutzer auf: ee97mnee

UPDATE users 
SET password_hash = '$2y$10$wPQYPMN3WpVoliNeEhKCselzfTbLHD5tX9EbH/THNlG5BLUdiQrs6'
WHERE username = 'admin';
