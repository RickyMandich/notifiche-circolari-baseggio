CREATE USER IF NOT EXISTS 'notifiche-circolari-baseggio'@'172.25.0.%' IDENTIFIED BY '';
GRANT ALL PRIVILEGES ON `notifiche-circolari-baseggio`.* TO 'notifiche-circolari-baseggio'@'172.25.0.%';
FLUSH PRIVILEGES;
