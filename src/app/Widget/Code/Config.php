<?php

return [
	'name'        => 'Code',
	'title'       => 'widget-code-title',
	'description' => 'widget-code-desc',
	'version'     => '1.0.0',
	'author'      => 'CLSystems',
	'authorEmail' => 'info@clsystems.nl',
	'authorUrl'   => 'https://github.com/CLSystems',
	'updateUrl'   => null,
	'params'      => [
		[
			'name'      => 'content',
			'type'      => 'CmsEditorCode',
			'label'     => 'widget-code-title',
			'translate' => true,
			'filters'   => ['html'],
		],
	],
];