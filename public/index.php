<?php
declare(strict_types=1);

ini_set('date.timezone', 'Europe/Amsterdam');
setlocale(LC_ALL, 'nl_NL.UTF-8', 'nl_NL@euro', 'nl_NL', 'dutch');

use CLSystems\PhalCMS\Lib\Factory;

define('BASE_PATH', dirname(__DIR__));

if (false === is_readable(BASE_PATH . '/src/config.ini'))
{
	header('Location:install.php');
}

require_once BASE_PATH . '/src/app/Lib/Factory.php';

// Execute application
Factory::getApplication()->execute();