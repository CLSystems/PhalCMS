<?php

namespace CLSystems\PhalCMS\Lib\Form\Field;

use CLSystems\PhalCMS\Lib\Mvc\Model\Sources;

class CmsSource extends Select
{
	private $sources = null;

	public function getOptions()
	{
		if (null === $this->sources)
		{
			$this->sources = parent::getOptions();
			$items = Sources::find();
			if (0 < $items->count())
			{
				foreach ($items as $item)
				{
					$this->sources[$item->id] = $item->name;
				}
			}
		}

		return $this->sources;
	}
}
