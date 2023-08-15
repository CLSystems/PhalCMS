<?php

namespace CLSystems\PhalCMS\Lib\Mvc\Controller;

use CLSystems\PhalCMS\Lib\Helper\Text;
use Phalcon\Paginator\Adapter\QueryBuilder as Paginator;
use CLSystems\PhalCMS\Lib\Mvc\Model\UcmItem as Item;
use CLSystems\PhalCMS\Lib\Mvc\Model\UcmItemMap;
use CLSystems\PhalCMS\Lib\Mvc\Model\Translation;
use CLSystems\PhalCMS\Lib\Mvc\Model\Tag;
use CLSystems\PhalCMS\Lib\Helper\Config;
use CLSystems\PhalCMS\Lib\Helper\Event;
use CLSystems\PhalCMS\Lib\Helper\Language;
use stdClass;

class TagController extends ControllerBase
{
    /**
     * @return void
     */
    public function overviewAction()
    {
        $queryBuilder = $this->modelsManager
            ->createBuilder()
            ->from(['tag' => Tag::class])
            ->orderBy('slug ASC');

        $paginator = new Paginator(
            [
                'builder' => $queryBuilder,
                'limit'   => Config::get('listLimit', 20),
                'page'    => $this->request->get('page', ['uint'], 0),
            ]
        );

        $this->view->setVar('paginator', $paginator);

        // Metadata
        $metadata = new stdClass;
        $metadata->metaTitle = 'Tags overview';
        $metadata->metaDesc = 'Tags overview';
        $metadata->metaKeys = 'Tags overview';
        $metadata->contentRights = Config::get('siteContentRights');
        $metadata->metaRobots = Config::get('siteRobots');

        $vars = [
            'metadata' => $metadata,
        ];

        $this->view->setVars($vars);
        $this->view->pick('Tag/Overview');
    }
}