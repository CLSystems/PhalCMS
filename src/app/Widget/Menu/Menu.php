<?php

namespace CLSystems\PhalCMS\Widget\Menu;

use CLSystems\PhalCMS\Library\Helper\Menu as MenuHelper;
use CLSystems\PhalCMS\Library\Widget;

class Menu extends Widget
{
	public function getContent()
	{
		if ($items = MenuHelper::getMenuItems($this->widget->get('params.menuType')))
		{
			$renderer = $this->getRenderer();

			return $renderer->getPartial('Content/Navbar', ['items' => $items, 'renderer' => $renderer]);
		}

		return null;
	}
}
