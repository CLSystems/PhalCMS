<?php

namespace CLSystems\PhalCMS\Lib\Mvc\Controller;

ini_set('memory_limit', '512M');

use Phalcon\Mvc\Controller;
use Phalcon\Mvc\Dispatcher;
use CLSystems\PhalCMS\Lib\Helper\Event;
use CLSystems\PhalCMS\Plugin\Cms\SiteMap\SiteMap;
use CLSystems\PhalCMS\Lib\Mvc\Model\Post;
use CLSystems\PhalCMS\Lib\Mvc\Model\PostCategory;

/**
 * Class SiteMapController
 *
 * @package CLSystems\PhalCMS\Lib\Mvc\Controller
 */
class SiteMapController extends Controller
{
	/** @var SiteMap */
	protected $pluginHandler;

	/**
	 * @param Dispatcher $dispatcher
	 * @return false
	 */
	public function beforeExecuteRoute(Dispatcher $dispatcher) : bool
	{
		$plugins  = Event::getPlugins(true);
		$plgClass = SiteMap::class;

		if (!isset($plugins['Cms'][$plgClass]))
		{
			$dispatcher->forward(
				[
					'controller' => 'error',
					'action'     => 'show',
				]
			);

			return false;
		}

		$this->pluginHandler = Event::getHandler($plgClass, $plugins['Cms'][$plgClass]);

		return true;
	}

	public function indexAction()
	{
		$page = $this->dispatcher->getParam('page') ?? 1;
		$posts = Post::query()
			->where("context = 'post' AND state = 'P'")
			->orderBy('id DESC')
			->limit(25000, ($page - 1) * 25000)
			->execute();
		$categories = PostCategory::query()
			->where("context = 'post-category' AND state = 'P'")
			->andWhere("level > 0")
			->orderBy('id DESC')
			->execute();

		$this->view
			->disable()
			->setVar('posts', $posts)
			->setVar('categories', $categories)
			->setVar('domain', DOMAIN . '/')
			->partial('SiteMap/Index');
//		$this->view->start()->render("SiteMap", "index")->finish();
	}
}