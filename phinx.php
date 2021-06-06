<?php

declare(strict_types=1);

use Selective\Config\Configuration;

$app            = require_once __DIR__ . '/config/bootstrap.php';
$container      = $app->getContainer();
$db             = $container->get(Configuration::class)->getArray('db');

return
[
    'paths' => [
        'migrations' => 'database/migrations',
        'seeds' => 'database/seeds'
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => 'default',
        'default' => [
            'adapter' => $db['driver'],
            'host'    => $db['host'] ?? null,
            'port'    => $db['port'] ?? null,
            'socket'  => $db['socket'] ?? null,
            'name'    => $db['database'],
            'user'    => $db['username'],
            'pass'    => $db['password'],
            'charset' => 'utf8',
        ],
    ],
    'version_order' => 'creation'
];
