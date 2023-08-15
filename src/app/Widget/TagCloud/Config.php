<?php

return [
	'name'        => 'TagCloud',
	'title'       => 'widget-tagcloud-title',
	'description' => 'widget-tagcloud-desc',
	'version'     => '1.0.6',
	'author'      => 'CLSystems',
	'authorEmail' => 'info@clsystems.nl',
	'authorUrl'   => 'https://github.com/CLSystems',
	'updateUrl'   => null,
	'params'      => [
		[
			'name'     => 'tagsNum',
			'type'     => 'Number',
			'label'    => 'limit-tags-number',
			'multiple' => true,
			'filters'  => ['uint'],
			'min'      => 1,
			'value'    => 10,
		],
	],
];
