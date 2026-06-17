<?php
// ============================================================
// Database Configuration — PDO Connection
// Credentials loaded from environment variables (.env)
// For development, copy .env.example to .env and update
// ============================================================

// Load environment variables from .env file (if exists)
if (!function_exists('loadEnv')) {
    function loadEnv(string $filePath = __DIR__ . '/../.env'): void {
        if (!file_exists($filePath)) {
            return;
        }
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos($line, '=') === false || strpos($line, '#') === 0) {
                continue;
            }
            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value, '\'"');
            if (!empty($key)) {
                $_ENV[$key] = $value;
            }
        }
    }
}

// Load .env file
loadEnv();

// Get environment variable or use default/required value
if (!function_exists('getEnv')) {
    function getEnv(string $key, $default = null) {
        return $_ENV[$key] ?? $_SERVER[$key] ?? $default;
    }
}

define('DB_HOST', getEnv('DB_HOST', 'localhost'));
define('DB_NAME', getEnv('DB_NAME', 'sarak_youth'));
define('DB_USER', getEnv('DB_USER', 'root'));
define('DB_PASS', getEnv('DB_PASS', ''));
define('DB_CHARSET', 'utf8mb4');

function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            http_response_code(500);
            die(json_encode(['error' => 'Database connection failed.']));
        }
    }
    return $pdo;
}
