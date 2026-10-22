CREATE USER IF NOT EXISTS 'notifiche-circolari-baseggio'@'172.40.0.%' IDENTIFIED BY '';
GRANT ALL PRIVILEGES ON `notifiche-circolari-baseggio`.* TO 'notifiche-circolari-baseggio'@'172.40.0.%';
FLUSH PRIVILEGES;