<?php

use Phalcon\Cli\Console;
use Phalcon\Cli\Dispatcher;
use Phalcon\Config\Adapter\Ini;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Di\FactoryDefault\Cli as CliDI;
use Phalcon\Exception as PhalconException;
use Phalcon\Loader;

require_once __DIR__ . '/src/app/Library/Factory.php';

$loader = new Loader();
$loader->registerNamespaces(
    [
        'CLSystems\\PhalCMS' => 'src/',
    ]
);
$loader->register();

require_once __DIR__ . '/vendor/autoload.php';

$container = new CliDI();
$dispatcher = new Dispatcher();

$dispatcher->setDefaultNamespace('CLSystems\PhalCMS\Tasks');
$container->setShared('dispatcher', $dispatcher);

$config = new Ini(BASE_PATH . '/config.ini', INI_SCANNER_NORMAL);
$dbPrefix = $config->path('DB.PREFIX');

try {
    $db = new Mysql(
        [
            'host'     => $config->path('DB.HOST'),
            'username' => $config->path('DB.USER'),
            'password' => $config->path('DB.PASS'),
            'dbname'   => $config->path('DB.NAME'),
            'charset'  => 'utf8mb4',
        ]
    );

}
catch (Exception $exception) {
    die($exception->getCode() . ' - ' . $exception->getMessage());
}

$container->getShared('modelsManager')->setModelPrefix($dbPrefix);
$container->setShared('db', $db);

$console = new Console($container);
$container->setShared('console', $console);

$arguments = [];
foreach ($argv as $k => $arg) {
	if ($k === 1) {
		$arguments['task'] = $arg;
	}
	else if ($k === 2) {
		$arguments['action'] = $arg;
	}
	else if ($k >= 3) {
		$arguments['params'][] = $arg;
	}
}

try {
	$console->handle($arguments);
}
catch (PhalconException $e) {
	fwrite(STDERR, $e->getMessage() . PHP_EOL . ' - ' . $e->getTraceAsString());
	exit(1);
}
catch (Throwable $throwable) {
	fwrite(STDERR, $throwable->getMessage() . PHP_EOL . ' - ' . $throwable->getTraceAsString());
	exit(1);
}
catch (Exception $exception) {
	fwrite(STDERR, $exception->getMessage() . PHP_EOL . ' - ' . $exception->getTraceAsString());
	exit(1);
}
