<?php

namespace CLSystems\PhalCMS\Library\Helper;

use CLSystems\PhalCMS\Library\Factory;

class Toolbar
{
	protected static $toolbars = [];

	public static function add($name, $action, $icon = null)
	{
		self::$toolbars[$name] = [
			'route' => $action,
			'icon'   => $icon ?: $name,
			'text'   => Text::_($name),
		];
	}

	public static function addCustom($name, $html)
	{
		self::$toolbars[$name] = $html;
	}

	public static function render()
	{
		return Factory::getService('view')->getPartial('Toolbar/Toolbar', ['toolbars' => self::$toolbars]);
	}
}
