<?php

namespace CLSystems\PhalCMS\Plugin\Cms\SiteMap;

use CLSystems\PhalCMS\Lib\Plugin;
use CLSystems\PhalCMS\Lib\Helper\Uri;
use Phalcon\Mvc\Router;

class SiteMap extends Plugin
{
	public function onConstruct()
	{
	}

	/**
	 * Set the route
	 *
	 * @param Router $router
	 */
	public function onBeforeServiceSetRouter(Router $router)
	{
		$router->addGet(
			Uri::getBaseUriPrefix() . '/sitemap/([0-9]+)',
			[
				'controller' => 'site_map',
				'action'     => 'index',
				'page'       => 1,
			]
		)
		->setName('sitemap');
	}
}