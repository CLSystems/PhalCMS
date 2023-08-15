<?php

namespace CLSystems\PhalCMS\Lib\Mvc\Controller;

use Phalcon\Mvc\Controller;
use CLSystems\PhalCMS\Lib\Helper\Asset;
use CLSystems\PhalCMS\Lib\Helper\Config;
use CLSystems\PhalCMS\Lib\Helper\Event;
use CLSystems\PhalCMS\Lib\Helper\Uri;
use CLSystems\PhalCMS\Lib\Helper\Language;
use CLSystems\PhalCMS\Lib\Helper\User;
use stdClass;

class ControllerBase extends Controller
{
    public function onConstruct()
    {
        $siteName = Config::get('siteName');

        if (Uri::isClient('site')) {
            $this->siteBase();
        } else {
            $format = $this->dispatcher->getParam('format');

            if ('raw' === $format) {
                $this->view->setMainView('Raw');
            }

            $this->tag->title($siteName);
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
        $source = new stdClass;
        $source->systemMenus = [];
        Event::trigger('registerSystemMenus', [$source], ['Cms']);
        $this->view->setVar('systemMenus', $source->systemMenus);
    }

    protected function siteBase()
    {
        Asset::addFile('core.js');
        $langCode = Language::getActiveCode();
        $tplLangFile = TPL_SITE_PATH . '/Language/' . $langCode . '.php';

        if (is_file($tplLangFile)
            && ($content = include $tplLangFile)
        ) {
            Language::load($content, $langCode);
        }
    }
}
