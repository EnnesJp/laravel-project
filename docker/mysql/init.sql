-- Create the telescope database
CREATE DATABASE IF NOT EXISTS `telescope` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Grant privileges to the laravel user for both databases
GRANT ALL PRIVILEGES ON `telescope`.* TO 'laravel'@'%';

FLUSH PRIVILEGES;