<?php

namespace CLSystems\PhalCMS\Lib\Form\Field;

use CLSystems\PhalCMS\Lib\Mvc\Model\Tag;

class CmsTag extends Select
{
	private $tags = null;

	public function getOptions()
	{
		if (null === $this->tags)
		{
			$items = Tag::find();
			if (0 < $items->count())
			{
				$this->tags  = [];
				foreach ($items as $item)
				{
					$this->tags[$item->id] = $item->title;
				}
			}
		}

		return $this->tags;
	}
}
