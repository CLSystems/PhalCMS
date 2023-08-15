<?php

namespace CLSystems\PhalCMS\Lib\Mvc\Controller;

use PDO;
use Phalcon\Support\Version;
use Phalcon\Db\Adapter\Pdo\Mysql;
use CLSystems\PhalCMS\Lib\Helper\Event;
use CLSystems\PhalCMS\Lib\Helper\Widget;

class AdminIndexController extends ControllerBase
{
	public function onConstruct()
	{
		parent::onConstruct();
		$this->adminBase();
		$this->view->pick('CPanel/Index');
	}

	public function indexAction()
	{
		/** @var Mysql $db */
		$db           = $this->getDI()->get('db');
		$pdo          = $db->getInternalHandler();
		$prefix       = $this->modelsManager->getModelPrefix();
		$widgetsCount = 0;
		$pluginsCount = 0;

		foreach (Widget::getWidgetItems() as $pos => $widgets)
		{
			$widgetsCount += count($widgets);
		}

		foreach (Event::getPlugins(true) as $group => $plugins)
		{
			$pluginsCount += count($plugins);
		}

		$vars = [
			'widgetsCount'    => $widgetsCount,
			'pluginsCount'    => $pluginsCount,
			'phalconVersion'  => (new Version)->get(),
			'phpVersion'      => PHP_VERSION,
			'databaseVersion' => $pdo->getAttribute($pdo::ATTR_SERVER_VERSION),
			'postsCount'      => 0,
			'categoriesCount' => 0,
			'usersCount'      => 0,
			'mediaCount'      => 0,
			'extensions'      => [
				'Curl'      => extension_loaded('curl'),
				'GetText'   => extension_loaded('gettext'),
				'Gd'        => extension_loaded('gd'),
				'Json'      => extension_loaded('json'),
				'Mbstring'  => extension_loaded('mbstring'),
				'FileInfo'  => extension_loaded('fileinfo'),
				'OpenSSL'   => extension_loaded('openssl'),
				'PDO Mysql' => false,
			],
		];

		if (class_exists('PDO')
			&& in_array('mysql', PDO::getAvailableDrivers())
		)
		{
			$vars['extensions']['PDO Mysql'] = true;
		}

		// Posts categories count
		$result                  = $db->fetchOne('SELECT COUNT(id) AS num FROM ' . $prefix . 'ucm_items WHERE context = \'post-category\' AND state = \'P\' AND lft > 0');
		$vars['categoriesCount'] = (int) $result['num'];

		// Posts count
		$result             = $db->fetchOne('SELECT COUNT(id) AS num FROM ' . $prefix . 'ucm_items WHERE context = \'post\' AND state = \'P\'');
		$vars['postsCount'] = (int) $result['num'];

		// Users count
		$result             = $db->fetchOne('SELECT COUNT(id) AS num FROM ' . $prefix . 'users');
		$vars['usersCount'] = (int) $result['num'];

		// Media count
		$result             = $db->fetchOne('SELECT COUNT(id) AS num FROM ' . $prefix . 'media');
		$vars['mediaCount'] = (int) $result['num'];

		$this->view->setVars($vars);
	}
}