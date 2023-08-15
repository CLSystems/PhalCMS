<?php

namespace CLSystems\PhalCMS\Lib\Mvc\Controller;

use Phalcon\Mvc\Controller;
use Phalcon\Mvc\Dispatcher;
use CLSystems\PhalCMS\Lib\Helper\Event;
use CLSystems\PhalCMS\Plugin\Cms\OutLink\OutLink;
use CLSystems\PhalCMS\Lib\Mvc\Model\Post;

/**
 * Class OutLinkController
 *
 * @package CLSystems\PhalCMS\Lib\Mvc\Controller
 */
class OutLinkController extends Controller
{
	/** @var OutLink */
	protected $pluginHandler;

	/**
	 * @param Dispatcher $dispatcher
	 * @return false
	 */
	public function beforeExecuteRoute(Dispatcher $dispatcher) : bool
	{
		$plugins  = Event::getPlugins(true);
		$plgClass = OutLink::class;

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

	/**
	 * @param int $id
	 */
	public function indexAction(int $id)
	{
		$ucmItem = Post::findFirst([
			'conditions' => "id = " . $id . " AND context = 'post'",
		]);

		if (!$ucmItem)
		{
			$this->response->redirect('error/show');
			$this->view->disable();
			return false;
		}

		$this->response->redirect($ucmItem->getLink());
		$this->view->disable();
		return true;
	}
}