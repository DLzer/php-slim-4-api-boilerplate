<?php

// Error reporting
error_reporting(1);
ini_set('display_errors', '1');

// Timezone
date_default_timezone_set('America/New_York');

// Load our environment variables
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__, 1));
$dotenv->load();

// Settings
$settings = [];

// Environment
$settings['environment'] = $_ENV['APP_ENV'] ?? 'local';

// Path settings
$settings['root'] = dirname(__DIR__, 1);
$settings['temp'] = $settings['root'] . '/tmp';
$settings['public'] = $settings['root'] . '/public';
$settings['templates'] = $settings['root'] . '/resoures/views';

// Error Handling Middleware settings
$settings['error_handler_middleware'] = [

    // Should be set to false in production
    'display_error_details' => true,

    // Parameter is passed to the default ErrorHandler
    // View in rendered output by enabling the "displayErrorDetails" setting.
    // For the console and unit tests we also disable it
    'log_errors' => true,

    // Display error details in error log
    'log_error_details' => true,
];

// Logger
$settings['logger'] = [
    'name' => 'app',
    'path' => $settings['root'] . '/logs',
    'filename' => 'app.log',
    'level' => \Monolog\Logger::DEBUG,
    'file_permission' => 0775,
];

// SMTP Settings
$settings['smtp'] = [
    'type' => $_ENV['MAIL_DRIVER'],
    'host' => $_ENV['MAIL_HOST'],
    'port' => $_ENV['MAIL_PORT'],
    'username' => $_ENV['MAIL_USERNAME'],
    'password' => $_ENV['MAIL_PASSWORD'],
    'envryption' => $_ENV['MAIL_ENCRYPTION'],
];

// Database settings
$settings['db'] = [
    'driver'    => \Cake\Database\Driver\Mysql::class,
    'host'      => $_ENV['DB_HOST'],
    'database'  => $_ENV['DB_DATABASE'],
    'username'  => $_ENV['DB_USERNAME'],
    'password'  => $_ENV['DB_PASSWORD'],
    'charset'   => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
    'options' => [
        // Turn off persistent connections
        PDO::ATTR_PERSISTENT => false,
        // Enable exceptions
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        // Emulate prepared statements
        PDO::ATTR_EMULATE_PREPARES => true,
        // // Set default fetch mode to array
        // PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        // Set character set
        // PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci'
    ],
];

// Phinx settings
$settings['phinx'] = [
    'migrations' =>  'database/migrations',
    'seeds' => 'database/seeds',
    'schema' => 'database/schema',
    'environments' => [
        'local' => [
            'adapter' => 'mysql',
            'host' => (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') ? 'localhost' : '127.0.0.1', // Windows - Localhost -- Linux 127.0.0.1
            'port' => 3306,
            'name' => 'local',
            'socket' => NULL,
            'user' => 'local',
            'pass' => 'local',
            'charset' => 'utf8',
        ],
        'development' => [
            'adapter' => 'mysql',
            'host' => '127.0.0.1',
            'port' => 3306,
            'name' => 'amf_dev',
            'socket' => NULL,
            'user' => 'password',
            'pass' => 'root',
            'charset' => 'utf8',
        ],
        'production' => [
            'adapter' => 'mysql',
            'host' => '127.0.0.1',
            'port' => 3306,
            'name' => 'amf_prod',
            'socket' => NULL,
            'user' => 'password',
            'pass' => 'root',
            'charset' => 'utf8',
        ],
    ],
    'default_environment' => 'local',
    'default_migration_table' => 'phinxlog'
];

// Web Token settings
$settings['jwt'] = [
        'secret' => $_ENV['JWT_SECRET'],
        'secure' => false,
        "header" => "Authorization",
        "regexp" => "/Token\s+(.*)$/i",
        'passthrough' => ['OPTIONS']
];
return $settings;