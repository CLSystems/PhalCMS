<?php

namespace CLSystems\PhalCMS\Lib\Form\Field;

use CLSystems\PhalCMS\Lib\Helper\Menu;

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
