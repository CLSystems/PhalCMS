<?php

use Phalcon\Autoload\Loader;

$loader = new Loader;
$loader->setNamespaces(
	[
		'CLSystems\\PhalCMS\\Plugin' => APP_PATH . '/Plugin',
		'CLSystems\\PhalCMS\\Widget' => APP_PATH . '/Widget',
		'CLSystems\\PhalCMS\\Lib'    => APP_PATH . '/Lib',
	]
);

$loader->register();