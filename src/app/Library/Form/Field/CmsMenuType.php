<?php

namespace CLSystems\PhalCMS\Library\Form\Field;

use CLSystems\PhalCMS\Library\Helper\Menu;

class CmsMenuType extends Select
{
	public function getOptions()
	{
		$options = parent::getOptions();

		foreach (Menu::getMenuTypes() as $menuType)
		{
			$options[$menuType->data] = $menuType->data;
		}

		return $options;
	}
}
