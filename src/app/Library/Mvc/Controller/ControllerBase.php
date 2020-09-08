<?php

namespace CLSystems\PhalCMS\Library\Mvc\Controller;

use Phalcon\Mvc\Controller;
use CLSystems\PhalCMS\Library\Helper\Asset;
use CLSystems\PhalCMS\Library\Helper\Config;
use CLSystems\PhalCMS\Library\Helper\Event;
use CLSystems\PhalCMS\Library\Helper\Uri;
use CLSystems\PhalCMS\Library\Helper\Language;
use CLSystems\PhalCMS\Library\Helper\User;
use stdClass;

class ControllerBase extends Controller
{
	public function onConstruct()
	{
		$siteName = Config::get('siteName');

		if (Uri::isClient('site'))
		{
			$this->siteBase();
		}
		else
		{
			$format = $this->dispatcher->getParam('format');

			if ('raw' === $format)
			{
				$this->view->setMainView('Raw');
			}

			$this->tag->setTitle($siteName);
		}

		$this->view->setVars(
			[
				'siteName'  => $siteName,
				'cmsConfig' => Config::get(),
				'user'      => User::getInstance(),
			]
		);
	}

	protected function adminBase()
	{
		Asset::addFiles(
			[
				'admin.css',
				'core.js',
				'admin.js',
				'tab-state.js',
			]
		);
		Asset::chosen('.uk-select');
		$source              = new stdClass;
		$source->systemMenus = [];
		Event::trigger('registerSystemMenus', [$source], ['Cms']);
		$this->view->setVar('systemMenus', $source->systemMenus);
	}

	protected function siteBase()
	{
		Asset::addFile('core.js');
		$langCode    = Language::getActiveCode();
		$tplLangFile = TPL_SITE_PATH . '/Language/' . $langCode . '.php';

		if (is_file($tplLangFile)
			&& ($content = include $tplLangFile)
		)
		{
			Language::load($content, $langCode);
		}
	}
}
