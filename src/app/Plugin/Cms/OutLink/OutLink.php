<?php

namespace CLSystems\PhalCMS\Plugin\Cms\OutLink;

use CLSystems\PhalCMS\Lib\Helper\Asset;
use CLSystems\PhalCMS\Lib\Plugin;
use Phalcon\Mvc\Router;

/**
 * Class OutLink
 *
 * @package CLSystems\PhalCMS\Plugin\Cms\OutLink
 */
class OutLink extends Plugin
{
	public function onConstruct()
	{
		Asset::addFiles(
			[
				__DIR__ . '/Asset/Css/outlink.css',
				__DIR__ . '/Asset/Js/outlink.js',
			]
		);
	}

	/**
	 * Set the route
	 *
	 * @param Router $router
	 */
	public function onBeforeServiceSetRouter(Router $router)
	{
		$router->add(
			'/outlink/:params',
			[
				'controller' => 'out_link',
				'action'     => 'index',
				'params'     => 1,
			]
		);
	}
}