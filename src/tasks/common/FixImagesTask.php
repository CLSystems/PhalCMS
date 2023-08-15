<?php

namespace CLSystems\PhalCMS\Tasks\Common;

use CLSystems\PhalCMS\Lib\Mvc\Model\Post;
use CLSystems\PhalCMS\Tasks\MainTask;

/**
 * Class FixImagesTask
 *
 * @package CLSystems\PhalCMS\Tasks\Common
 */
class FixImagesTask extends MainTask
{
	/**
	 * Main function for cron
	 */
	public function mainAction()
	{
		$posts = Post::find([
			'conditions' => 'context = :context: AND parentId = :parent_id: AND sourceId = :source_id: AND externalId = externalMerchantId',
			'bind' => [
				'context'   => 'post',
				'parent_id' => 117,
				'source_id' => 13,
			]
		])->toArray();

		if (false === empty($posts))
		{
			$this->info('Found ' . count($posts) . ' post(s)');
			foreach ($posts as $post)
			{
				$this->info('Processing post ' . $post['title'] . ' (' . $post['id'] . ')');
				$this->fixImage($post);
			}
		}
	}

	private function fixImage(array $post) : void
	{
		$currentImg = (string)$post['image'];
		if ('image_not_found.png' === $currentImg)
		{
			return;
		}

		if (false !== stristr($currentImg, "\\\\\\/"))
		{
			$postObj = Post::findFirst([
				'conditions' => 'id = :post_id:',
				'bind' => [
					'post_id' => $post['id']
				],
			]);
			if ($postObj)
			{
				$newImage = str_replace("\\\\\\/", "\/", $currentImg);
				$this->info('New image: ' . $newImage);
				$post['image'] = $newImage;
				$postObj->assign($post)->save();
				return;
			}
		}

		if (false !== stristr($currentImg, "\\\\/"))
		{
			$postObj = Post::findFirst([
				'conditions' => 'id = :post_id:',
				'bind' => [
					'post_id' => $post['id']
				],
			]);
			if ($postObj)
			{
				$newImage = str_replace("\\\\/", "\/", $currentImg);
				$this->info('New image: ' . $newImage);
				$post['image'] = $newImage;
				$postObj->assign($post)->save();
				return;
			}
		}

		if (false !== stristr($currentImg, "\\/"))
		{
			$postObj = Post::findFirst([
				'conditions' => 'id = :post_id:',
				'bind' => [
					'post_id' => $post['id']
				],
			]);
			if ($postObj)
			{
				$newImage = str_replace("\\/", "\/", $currentImg);
				$this->info('New image: ' . $newImage);
				$post['image'] = $newImage;
				$postObj->assign($post)->save();
				return;
			}
		}

		$slashCount = explode('\/', $currentImg);
		if (2 === count($slashCount))
		{
			$postObj = Post::findFirst([
				'conditions' => 'id = :post_id:',
				'bind' => [
					'post_id' => $post['id']
				],
			]);
			if ($postObj)
			{
				$prefix = $slashCount[0];
				$suffix = $slashCount[1];
				$newImage = $prefix . '\/' . $suffix[0] . '\/' . $suffix;
				$this->info('New image: ' . $newImage);
				$post['image'] = $newImage;
				$postObj->assign($post)->save();
				return;
			}
		}

		$slashCount = explode('/', $currentImg);
		if (3 === count($slashCount))
		{
			$postObj = Post::findFirst([
				'conditions' => 'id = :post_id:',
				'bind' => [
					'post_id' => $post['id']
				],
			]);
			if ($postObj)
			{
				$newImage = addcslashes($currentImg, '/');
				$this->info('New image: ' . $newImage);
				$post['image'] = $newImage;
				$postObj->assign($post)->save();
				return;
			}
		}
	}

}
