<?php

namespace CLSystems\PhalCMS\Lib\Mvc\Controller;

use CLSystems\PhalCMS\Lib\Helper\State;
use CLSystems\PhalCMS\Lib\Helper\Text;
use CLSystems\PhalCMS\Lib\Mvc\Model\UcmItem;
use Exception;

class ErrorController extends ControllerBase
{
	public function showAction()
	{
		$path = $this->dispatcher->getParam('path');

        echo "<br>PATH<br>";
        var_dump($path);
		$parts = explode('-', $path);

        echo "<br>PARTS<br>";
        var_dump($parts);

		foreach ($parts as $key => $part)
		{
			if (false !== stristr($part, 'kortingen'))
			{
				unset($parts[$key]);
			}
			if (false !== stristr($part, 'kortingscodes'))
			{
				unset($parts[$key]);
			}
			if (false !== stristr($part, 'aanbiedingen'))
			{
				unset($parts[$key]);
			}
			if (true === is_numeric($part))
			{
				unset($parts[$key]);
			}
		}

        echo "<br>ADJUSTED PARTS<br>";
        var_dump($parts);
		$newRoute = implode('-', $parts);
        echo "<br>NEWROUTE<br>";
        var_dump($newRoute);
		if (false === empty($newRoute))
		{
            $post = UcmItem::findFirst([
                'conditions' => "route LIKE '" . $newRoute . "%' AND state = 'P'",
            ]);
            if (false === empty($post)) {
                echo 'HTTP/1.1 301 Moved Permanently
Location: ' . $newRoute;
                $this->response->redirect($post->route, false, 301);
            }
            else
            {
                $this->view->pick('Error/NotFound');

                // Default error code is 404
                $vars = [
                    'title'     => $this->dispatcher->getParam('title', ['trim', 'string'], Text::_('404-title')),
                    'message'   => $this->dispatcher->getParam('message', ['trim', 'string'], Text::_('404-message')),
                ];

                $this->view->setVars($vars);
            }
		}
		else
		{
            $code = $this->dispatcher->getParam('code', ['int'], 404);
            if (404 === $code)
            {
                // Try to find some alternative(s) by redirect to search
                $searchString = implode(' ', $parts);
                $this->response->redirect('/search?q=' . $searchString, false, 301);
            }
            else
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
	}
}
