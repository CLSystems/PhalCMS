<?php

namespace CLSystems\PhalCMS\Library\Form\Field;

use CLSystems\PhalCMS\Library\Mvc\Model\Tag;

class CmsTag extends Select
{
	public function getOptions()
	{
		static $tags = null;

		if (null === $tags)
		{
			$tags  = [];
			$items = Tag::find();

			if ($items->count())
			{
				foreach ($items as $item)
				{
					$tags[$item->id] = $item->title;
				}
			}
		}

		return $tags;
	}
}
