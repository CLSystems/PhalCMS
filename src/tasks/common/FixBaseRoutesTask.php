<?php

namespace CLSystems\PhalCMS\Tasks\Common;

use CLSystems\PhalCMS\Lib\Mvc\Model\Post;
use CLSystems\PhalCMS\Lib\Mvc\Model\UcmItem;
use CLSystems\PhalCMS\Tasks\MainTask;
use CLSystems\Php\Filter;

/**
 * Class FixRoutesTask
 *
 * @package CLSystems\PhalCMS\Tasks\Common
 */
class FixBaseRoutesTask extends MainTask
{
	/**
	 * Main function for cron
	 */
	public function mainAction()
	{
		/* @var $posts Post */
		$posts = Post::find([
			'conditions' => 'context = :context: AND parentId = :parent_id: AND externalId = externalMerchantId AND route LIKE :route_str:',
//			'conditions' => 'id = :post_id: AND context = :context: AND parentId = :parent_id: AND externalId = externalMerchantId',
			'bind' => [
//				'post_id' => 87043,
				'context'   => 'post',
				'parent_id' => 117, // Merken
				'route_str' => 'merken/rad-%'
//				'source_id' => 1,
			],
			'order' => 'id asc',
//			'limit' => 5,
		])->toArray();

		if (false === empty($posts))
		{
			$this->info('Found ' . count($posts) . ' post(s)');
			foreach ($posts as $post)
			{
				$this->info('Processing post ' . $post['id'] . ' - ' . $post['title']);
				$this->fixRoute($post);
			}
		}
	}

	private function fixRoute(array $post) : void
	{
		$baseRoute = $this->cleanRoute($post['route']);
		if (strlen($baseRoute) < 1 || $baseRoute === 'merken/rad')
		{
			// $name = $this->cleanProgramName($post['title']);
			$slug = Filter::toSlug($post['title']);
			$baseRoute = Filter::toPath('merken/' . $slug);
		}
		$this->info('baseRoute -> ' . $baseRoute);
		/* @var $similarPosts Post */
		$similarPosts = Post::find([
			'conditions' => 'context = :context: AND parentId = :parent_id: AND route LIKE :route_str: AND externalId = externalMerchantId',
//			'conditions' => 'id = :post_id: AND context = :context: AND parentId = :parent_id: AND externalId = externalMerchantId',
			'bind' => [
//				'post_id' => $post['id'],
				'context'   => 'post',
				'parent_id' => 117, // Merken
				'route_str' => $baseRoute . '-%',
			],
			'order' => 'id asc',
		])->toArray();
		$this->info('Found ' . count($similarPosts) . ' similar post(s)');
		$count = 0;
		$db = $this->container->getShared('db');
		if (true === empty($similarPosts))
		{
			$db->begin();
			/* @var $postObj Post */
			$postObj = Post::findFirst([
				'conditions' => 'id = :post_id:',
				'bind' => [
					'post_id' => $post['id']
				],
			]);

			$this->info('Updating post ' . $post['id'] . ' - ' . $postObj->route . ' -> ' . $baseRoute);
			$postObj->route = $baseRoute;
			$postObj->update();
			$db->commit();
		}
		else
		{
			foreach ($similarPosts as $similarPost)
			{
				$db->begin();
				/* @var $postObj Post */
				$postObj = Post::findFirst([
					'conditions' => 'id = :post_id:',
					'bind' => [
						'post_id' => $similarPost['id']
					],
				]);
				if ($count === 0)
				{
					$newRoute = $baseRoute; // . '-kortingen-aanbiedingen';
				}
				else
				{
					$newRoute = $baseRoute . '-' . ($count + 1);
				}

				$this->info('Updating post ' . $similarPost['id'] . ' - ' . $postObj->route . ' -> ' . $newRoute);
				$postObj->route = $newRoute;
				$postObj->update();
				$db->commit();
				++$count;
			}
		}
	}

	private function cleanRoute(string $route): string
	{
		$parts = explode('-', $route);
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
		return implode('-', $parts);
	}
}
