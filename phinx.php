<?php

declare(strict_types=1);

use Selective\Config\Configuration;

$app              = require_once __DIR__ . '/config/bootstrap.php';
$container        = $app->getContainer();
$db = $container->get(Configuration::class)->getArray('phinx');
$env = $container->get(Configuration::class)->getArray('environment');

$env = ( $env != '' ) ? $env : 'local';

return
[
    'paths' => [
        'migrations' => $db['migrations'],
        'seeds' => $db['seeds'],
    ],
    'schema_file' => $db['schema'],
    'default_migration_prefix' => '',
    'mark_generated_migration' => true,
    'environments' => [
        'default_migration_table' => $db['default_migration_table'],
        'default_environment' => $env[0],
        'production' => $db['environments']['production'],
        'development' => $db['environments']['development'],
        'local' => $db['environments']['local'],
    ],
    'version_order' => 'creation'
];
