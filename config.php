<?php
// Load environment variables from .env file if it exists
if (file_exists(__DIR__ . '/.env')) {
    $lines = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
            putenv(sprintf('%s=%s', $name, $value));
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}

define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'gallery_db');
define('DB_USER', getenv('DB_USER') ?: 'your_db_user');
define('DB_PASS', getenv('DB_PASS') ?: 'your_db_password');

define('S3_ENDPOINT', getenv('S3_ENDPOINT') ?: 'https://s3.bazissoft.ru');
define('S3_BUCKET', getenv('S3_BUCKET') ?: 'recruiting');
define('S3_KEY', getenv('S3_KEY') ?: 'test');
define('S3_SECRET', getenv('S3_SECRET') ?: 'test');
define('S3_REGION', getenv('S3_REGION') ?: 'ru-central');

define('MAX_FILE_SIZE', (int)(getenv('MAX_FILE_SIZE') ?: 5 * 1024 * 1024));
define('ALLOWED_MIME_TYPES', array_map('trim', explode(',', getenv('ALLOWED_MIME_TYPES') ?: 'image/jpeg,image/png,image/webp')));
define('LOG_FILE', getenv('LOG_FILE') ?: __DIR__ . '/errors.log');
?>