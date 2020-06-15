<?php
error_reporting(E_ALL);
ini_set('display_errors', 'true');

use CLSystems\PhalCMS\Lib\Factory;

define('BASE_PATH', dirname(__DIR__));

require_once BASE_PATH . '/app/Library/Factory.php';

// Execute application
Factory::getApplication()->execute();