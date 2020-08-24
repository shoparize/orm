<?php
require_once(__DIR__ . "/vendor/autoload.php");

$environment = array_merge($_SERVER, $_ENV);

$phinxConf =
[
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/test/Migrations',
        'seeds' => '%%PHINX_CONFIG_DIR%%/test/Seeds'
    ],
    'environments' => [
        'default_environment' => 'testing',
        'default_migration_table' => 'Migrations',

        'testing' => [
            'adapter' => 'mysql',
            'host' => $environment['MYSQL_HOST'],
            'name' => $environment['MYSQL_DATABASE'],
            'user' => $environment['MYSQL_USER'],
            'pass' => $environment['MYSQL_PASSWORD'],
            'port' => '3306',
            'charset' => 'utf8',
        ]
    ],
    'version_order' => 'creation'
];

\Kint::dump($phinxConf);
return $phinxConf;