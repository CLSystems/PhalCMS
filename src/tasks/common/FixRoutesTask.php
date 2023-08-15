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
class FixRoutesTask extends MainTask
{
	/**
	 * Main function for cron
	 */
	public function mainAction()
	{
		$posts = Post::find([
			'conditions' => 'context = :context: AND parentId = :parent_id: AND externalId = externalMerchantId',
//			'conditions' => 'context = :context: AND parentId = :parent_id: AND externalId = externalMerchantId AND modifiedAt < :modified:',
//			'conditions' => 'id = :post_id: AND context = :context: AND parentId = :parent_id: AND externalId = externalMerchantId',
			'bind' => [
//				'post_id' => 222116,
				'context'   => 'post',
				'parent_id' => 117, // Merken
//				'modified' => date('Y-m-d H:i:s', strtotime('yesterday')),
//				'source_id' => 1,
			],
			'order' => 'id ASC',
			'limit' => 100,
		])->toArray();

		if (false === empty($posts))
		{
			$this->info('Found ' . count($posts) . ' post(s)');
			foreach ($posts as $post)
			{
				$this->info('Processing post ' . $post['id'] . ' - ' . $post['title']);
				$this->fixRoute($post);
				usleep(500000);
			}
		}
	}

	private function fixRoute(array $post) : void
	{
//		$baseRoute = $this->cleanRoute($post['route']);
//		if (strlen($baseRoute) < 1)
		{
			$name = $this->cleanProgramName($post['title']);
			$slug = Filter::toSlug($name);
			$baseRoute = Filter::toPath('merken/' . $slug);
		}
		$this->info('baseRoute -> ' . $baseRoute);
		$similarPosts = Post::find([
			'conditions' => 'context = :context: AND parentId = :parent_id: AND route LIKE :route_str: AND externalId = externalMerchantId',
			'bind' => [
				'context'   => 'post',
				'parent_id' => 117, // Merken
				'route_str' => $baseRoute . '%',
//				'source_id' => 1,
			],
			'order' => 'id ASC',
		])->toArray();
		$this->info('Found ' . count($similarPosts) . ' similar post(s)');
		$count = 0;
		$db = $this->container->getShared('db');
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
				$newRoute = $baseRoute. '-' . ($count + 1); // . '-kortingen-aanbiedingen-' . ($count + 1);
			}

			if (true === $this->hasSameTitle($post, $similarPost))
			{
				$this->info('Updating post ' . $postObj->id . ' - ' . $postObj->route . ' -> ' . $newRoute);
				$postObj->route = $newRoute;
			}
			else
			{
				$name = $this->cleanProgramName($similarPost['title']);
				$slug = Filter::toSlug($name);
				$baseRoute = Filter::toPath('merken/' . $slug);

				if ($count === 0)
				{
					$newRoute = $baseRoute; // . '-kortingen-aanbiedingen';
				}
				else
				{
					$newRoute = $baseRoute. '-' . ($count + 1); // . '-kortingen-aanbiedingen-' . ($count + 1);
				}

				$this->info('Updating post ' . $postObj->id . ' - ' . $postObj->route . ' -> ' . $newRoute);
				$postObj->route = $baseRoute;
			}
			$postObj->modifiedAt = date('Y-m-d H:i:s');
			$postObj->update();
			$db->commit();
			++$count;
			usleep(500000);
		}
	}

	/**
	 * @param array $post
	 * @param array $similarPost
	 * @return bool
	 */
	private function hasSameTitle(array $post, array $similarPost) : bool
	{
		if (strtolower($this->cleanProgramName($post['title'])) === strtolower($this->cleanProgramName($similarPost['title'])))
		{
			return true;
		}
		return false;
	}

	private function cleanRoute(string $route) : string
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
