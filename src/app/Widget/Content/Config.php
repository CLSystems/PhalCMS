<?php

return [
	'name'        => 'Content',
	'title'       => 'widget-content-title',
	'description' => 'widget-content-desc',
	'version'     => '1.0.0',
	'author'      => 'CLSystems',
	'authorEmail' => 'info@clsystems.nl',
	'authorUrl'   => 'https://github.com/CLSystems',
	'updateUrl'   => null,
	'params'      => [
		[
			'name'           => 'content',
			'type'           => 'CmsEditor',
			'label'          => 'content',
			'translate'      => true,
			'filters'        => ['html'],
			'dataAttributes' => [
				'editor-height' => 350,
			],
		],
	],
];
