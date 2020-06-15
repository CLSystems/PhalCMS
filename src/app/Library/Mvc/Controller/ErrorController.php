<?php

namespace CLSystems\PhalCMS\Lib\Mvc\Controller;

use CLSystems\PhalCMS\Lib\Helper\State;
use CLSystems\PhalCMS\Lib\Helper\Text;
use Exception;

class ErrorController extends ControllerBase
{
	public function showAction()
	{
		$this->view->setMainView('Error/Index');
		$this->view->pick('Error/Message');

		// Default error code is 404
		$vars = [
			'code'      => $this->dispatcher->getParam('code', ['int'], 404),
			'title'     => $this->dispatcher->getParam('title', ['trim', 'string'], Text::_('404-title')),
			'message'   => $this->dispatcher->getParam('message', ['trim', 'string'], Text::_('404-message')),
			'exception' => State::getMark('exception'),
		];

		if ($vars['exception'] instanceof Exception)
		{
			$vars['code'] = $vars['exception']->getCode();
		}

		$this->view->setVars($vars);
	}
}
