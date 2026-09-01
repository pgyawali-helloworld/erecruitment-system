<?php
/**
 * Configuration File
 * Contains database credentials and system-wide constants.
 */

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'erecruitment_db');

// App Configuration
define('APP_NAME', 'E-Recruitment System');

// URL Root (Dynamic Detection)
// This works whether the project is in WAMP's www root or a subdirectory like /erecruitment-system/
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
$scriptName = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '';
$dir = dirname($scriptName);
$dir = ($dir === '\\' || $dir === '/') ? '' : $dir;

define('URL_ROOT', 'http://localhost/erecruitment-system/public');
define('APP_ROOT', dirname(dirname(__FILE__))); // Points to the /app directory

