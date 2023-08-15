<?php

namespace CLSystems\PhalCMS\Tasks\Common;

use CLSystems\PhalCMS\Lib\Mvc\Model\Post;
use CLSystems\PhalCMS\Lib\Mvc\Model\Translation;
use CLSystems\PhalCMS\Lib\Mvc\Model\UcmFieldValue;
use CLSystems\PhalCMS\Lib\Mvc\Model\UcmItem;
use CLSystems\PhalCMS\Lib\Mvc\Model\UcmItemMap;
use CLSystems\PhalCMS\Tasks\MainTask;

/**
 * Class CleanPostsTask
 *
 * @package CLSystems\PhalCMS\Tasks\Common
 */
class CleanPostsTask extends MainTask
{
	/**
	 * Main function for cron
	 */
	public function mainAction()
	{
		$posts = Post::find([
			'conditions' => 'context = :context: AND id < :post_id:  AND parentId = :parent_id: AND sourceId = :source_id: AND externalId = externalMerchantId',
			'bind' => [
				'context'   => 'post',
				'post_id'   => 89000,
				'parent_id' => 117,
				'source_id' => 2,
			]
		])->toArray();

		if (false === empty($posts))
		{
			$this->info('Found ' . count($posts) . ' post(s)');
			foreach ($posts as $post)
			{
				$this->info('Processing post ' . $post['title'] . ' (' . $post['id'] . ')');
				$this->cleanupPost($post);
			}
		}
	}

	private function cleanupPost(array $post)
	{
		$posts = Post::find([
			'conditions' => 'context = :context: AND id != :post_id: AND sourceId = :source_id: AND externalId = :external_id:',
			'bind' => [
				'context' => 'post',
				'post_id' => $post['id'],
				'source_id' => 2,
				'external_id' => $post['externalId'],
			]
		]);
		foreach ($posts as $doublePost)
		{
			$this->info('Deleting post ' . $doublePost->id . ' (' . $doublePost->title . ')');
			$this->container
				->getShared('modelsManager')
				->executeQuery('DELETE FROM ' . UcmItem::class . ' WHERE id = ' . $doublePost->id);

			// Delete related tags
			$this->container
				->getShared('modelsManager')
				->executeQuery('DELETE FROM ' . UcmItemMap::class . ' WHERE itemId1 = ' . $doublePost->id);

			// Delete related field values
			$this->container
				->getShared('modelsManager')
				->executeQuery('DELETE FROM ' . UcmFieldValue::class . ' WHERE itemId = ' . $doublePost->id);

			// Delete related translations
			$this->container
				->getShared('modelsManager')
				->executeQuery("DELETE FROM " . Translation::class . " WHERE translationId LIKE '%." . UcmItem::class . ".id:" . $doublePost->id . ".%'");
		}
	}
}
