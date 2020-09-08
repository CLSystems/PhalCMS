<?php

namespace CLSystems\PhalCMS\Plugin\System\Backup;

use CLSystems\PhalCMS\Library\Plugin;
use CLSystems\PhalCMS\Library\Helper\Uri;
use CLSystems\PhalCMS\Library\Helper\IconSvg;
use CLSystems\PhalCMS\Library\Helper\Text;

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
