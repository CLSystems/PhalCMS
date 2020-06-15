<?php

namespace CLSystems\PhalCMS\Plugin\System\Backup;

use CLSystems\PhalCMS\Lib\Plugin;
use CLSystems\PhalCMS\Lib\Helper\Uri;
use CLSystems\PhalCMS\Lib\Helper\IconSvg;
use CLSystems\PhalCMS\Lib\Helper\Text;

class Backup extends Plugin
{
	public function onAfterSystemMenus()
	{
		$url  = Uri::route('backup/index');
		$icon = IconSvg::render('database');
		$text = Text::_('backup-system');

		return <<<HTML
 <li>
	<a href="{$url}">
		{$icon}
		{$text}
	</a>
</li>
HTML;

	}
}