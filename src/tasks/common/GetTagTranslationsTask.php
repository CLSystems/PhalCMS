<?php

namespace CLSystems\PhalCMS\Tasks\Common;

use CLSystems\PhalCMS\Lib\Mvc\Model\Tag;
use CLSystems\PhalCMS\Tasks\MainTask;
use CLSystems\Php\Filter;

/**
 * Class GetTagTranslationsTask
 *
 * @package CLSystems\PhalCMS\Tasks\Common
 */
class GetTagTranslationsTask extends MainTask
{
    /**
     * Main function for cron
     */
    public function mainAction()
    {
    	$tags = Tag::find();
    	foreach ($tags as $tag)
		{
			$this->info('Current: ' . $tag->title);
			$data['title'] = (string)$this->getTranslation($tag->title);
			if($data['title'] !== $tag->title)
			{
				$this->info('Saving: ' . $data['title']);
				$data['slug'] = 'tag-' . Filter::toSlug($data['title']);
				$tag->assign($data)->save();
			}
		}
    }

}
