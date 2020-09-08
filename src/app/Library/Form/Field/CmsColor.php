<?php

namespace CLSystems\PhalCMS\Library\Form\Field;

use CLSystems\PhalCMS\Library\Helper\Asset;

class CmsColor extends Text
{
	protected $mode = 'HEX';

	public function toString()
	{
		Asset::addFiles(
			[
				'monolith.min.css',
				'pickr.min.js',
				'color.js'
			]
		);
		$input = parent::toString();
		$color = $this->getValue() ?: '#fff';

		return <<<HTML
<div class="picker-container uk-inline">
    <a class="uk-form-icon uk-form-icon-flip color-picker" href="javascript:void(0)" uk-icon="icon: paint-bucket"
       data-color="{$color}"
       data-mode="{$this->mode}"></a>
	{$input}
</div>
HTML;
	}
}
