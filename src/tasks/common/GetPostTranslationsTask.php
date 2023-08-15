<?php

namespace CLSystems\PhalCMS\Tasks\Common;

use CLSystems\PhalCMS\Lib\Mvc\Model\Post;
use CLSystems\PhalCMS\Tasks\MainTask;
use CLSystems\Php\Filter;

/**
 * Class GetPostTranslationsTask
 *
 * @package CLSystems\PhalCMS\Tasks\Common
 */
class GetPostTranslationsTask extends MainTask
{
	/**
	 * Main function for cron
	 */
	public function mainAction()
	{
		// Merken
		$posts = Post::query()->where('parentId = 117')->orderBy('id DESC')->execute();
		$this->translatePosts($posts);

		// Kortingscodes
		$posts = Post::query()->where('parentId = 118')->orderBy('id DESC')->execute();
		$this->translatePosts($posts);
	}

	private function translatePosts($posts)
	{
		foreach ($posts as $post)
		{
			$this->info('Current: ' . $post->description);
			$data['description'] = (string)$this->getTranslation(strip_tags(str_replace('<br />', "\n", html_entity_decode($post->description))));
			if ($data['description'] !== $post->description)
			{
				$this->info('Saving: ' . $data['description']);
				$data['summary'] = (string)$this->getTranslation(strip_tags(html_entity_decode($post->summary)));
				$post->assign($data)->save();
			}
		}
	}
}
