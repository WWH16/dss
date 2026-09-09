<?php

// Create required writable directories in Vercel's /tmp environment
$writableDirs = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
    '/tmp/bootstrap/cache',
];

foreach ($writableDirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// Map Laravel cache and storage paths to /tmp for Vercel serverless execution
$_ENV['APP_STORAGE'] = '/tmp/storage';
$_ENV['APP_SERVICES_CACHE'] = '/tmp/bootstrap/cache/services.php';
$_ENV['APP_PACKAGES_CACHE'] = '/tmp/bootstrap/cache/packages.php';
$_ENV['APP_CONFIG_CACHE'] = '/tmp/bootstrap/cache/config.php';
$_ENV['APP_ROUTES_CACHE'] = '/tmp/bootstrap/cache/routes.php';
$_ENV['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';

putenv('APP_STORAGE=/tmp/storage');
putenv('APP_SERVICES_CACHE=/tmp/bootstrap/cache/services.php');
putenv('APP_PACKAGES_CACHE=/tmp/bootstrap/cache/packages.php');
putenv('APP_CONFIG_CACHE=/tmp/bootstrap/cache/config.php');
putenv('APP_ROUTES_CACHE=/tmp/bootstrap/cache/routes.php');
putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');

// Default to stderr logging on Vercel to avoid file system permission issues and stream logs to dashboard
if (empty($_ENV['LOG_CHANNEL']) && !getenv('LOG_CHANNEL')) {
    $_ENV['LOG_CHANNEL'] = 'stderr';
    putenv('LOG_CHANNEL=stderr');
}

// Default to database sessions on Vercel to ensure reliable session persistence without 4KB cookie drops
if (empty($_ENV['SESSION_DRIVER']) && !getenv('SESSION_DRIVER')) {
    $_ENV['SESSION_DRIVER'] = 'database';
    putenv('SESSION_DRIVER=database');
}

// Default to smtp mailer if credentials exist and mailer is not set
if (empty($_ENV['MAIL_MAILER']) && !getenv('MAIL_MAILER') && (getenv('MAIL_USERNAME') || !empty($_ENV['MAIL_USERNAME']))) {
    $_ENV['MAIL_MAILER'] = 'smtp';
    putenv('MAIL_MAILER=smtp');
}

// Unset MAIL_SCHEME if empty or string "null"
if (getenv('MAIL_SCHEME') === 'null' || getenv('MAIL_SCHEME') === '') {
    putenv('MAIL_SCHEME');
    unset($_ENV['MAIL_SCHEME']);
}

// Forward execution to Laravel entry point
require __DIR__ . '/../public/index.php';
