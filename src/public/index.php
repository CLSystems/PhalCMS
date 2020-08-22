<?php
error_reporting(E_ALL);
ini_set('display_errors', 'true');

use CLSystems\PhalCMS\Library\Factory;

require_once dirname(__DIR__) . '/app/Library/Factory.php';

// Execute application
Factory::getApplication()->execute();
