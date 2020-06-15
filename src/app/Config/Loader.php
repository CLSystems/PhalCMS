<?php

use Phalcon\Loader;

$loader = new Loader;
$loader->registerNamespaces(
	[
		'CLSystems\\PhalCMS\\Plugin' => APP_PATH . '/Plugin',
		'CLSystems\\PhalCMS\\Widget' => APP_PATH . '/Widget',
		'CLSystems\\PhalCMS\\Lib'    => APP_PATH . '/Library',
	]
);

$loader->register();