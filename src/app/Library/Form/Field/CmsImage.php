<?php

namespace CLSystems\PhalCMS\Library\Form\Field;

use CLSystems\PhalCMS\Library\Helper\Asset;
use CLSystems\PhalCMS\Library\Factory;
use CLSystems\Php\Registry;

class CmsImage extends Select
{
	protected function toInput()
	{
		return parent::toString();
	}

	public function setValue($value)
	{
		$registry = new Registry($value);

		if ($this->multiple)
		{
			$this->value = $registry->toArray();
		}
		else
		{
			$this->value = $registry->get('0');
		}
	}

	public function getOptions()
	{
		$options = [];

		if (!empty($this->value))
		{
			if ($this->multiple)
			{
				foreach ($this->value as $val)
				{
					$options[$val] = $val;
				}
			}
			else
			{
				$options[$this->value] = $this->value;
			}
		}

		return $options;
	}

	public function toString()
	{
		Asset::addFile('media-modal.js');
		$this->class = rtrim('not-chosen uk-hidden ' . $this->class);

		return Factory::getService('view')
			->getPartial(
				'Form/Field/Image',
				[
					'field' => $this,
					'input' => $this->toInput(),
				]
			);
	}
}
