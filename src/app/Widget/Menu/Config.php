<?php

return [
	'name'        => 'Menu',
	'title'       => 'widget-menu-title',
	'description' => 'widget-menu-desc',
	'version'     => '1.0.0',
	'author'      => 'CLSystems',
	'authorEmail' => 'info@clsystems.nl',
	'authorUrl'   => 'https://github.com/CLSystems',
	'updateUrl'   => null,
	'params'      => [
		[
			'name'    => 'menuType',
			'type'    => 'CmsMenuType',
			'label'   => 'menu-type-select',
			'value'   => '',
			'options' => [
				'' => 'menu-type-select',
			],
			'rules'   => ['Options'],
		],
	],
];