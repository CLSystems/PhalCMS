<?php

namespace CLSystems\PhalCMS\Tasks\Common;

use CLSystems\PhalCMS\Lib\Mvc\Model\Media;
use CLSystems\PhalCMS\Tasks\MainTask;

/**
 * Class CleanImagesTask
 *
 * @package CLSystems\PhalCMS\Tasks\Common
 */
class CleanImagesTask extends MainTask
{
	/**
	 * Main function for cron
	 */
	public function mainAction()
	{
		$medias = Media::find()->toArray();

		if (false === empty($medias))
		{
			$this->info('Found ' . count($medias) . ' images(s)');
			foreach ($medias as $media)
			{
				$this->info('Processing image ' . $media['file'] . ' (' . $media['id'] . ')');
				$this->cleanImage($media);
			}
		}
	}

	private function cleanImage(array $media)
	{
		$link = __DIR__ . '/../../../public/upload/' . $media['file'];
		if (false === is_readable($link))
		{
			$this->info('Deleting ' . $media['file'] . ' (' . $media['id'] . ')');
			$obj = Media::findFirst([
				'conditions' => 'id = :media_id:',
				'bind' => [
					'media_id' => $media['id']
				],
			]);
			if ($obj)
			{
				$obj->delete();
			}
		}
		else
		{
			$this->info('Image ' . $media['file'] . ' (' . $media['id'] . ') FOUND, skipping...');
		}
	}

}
