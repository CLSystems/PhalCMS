<?php

return [
	'name'        => 'SocialShare',
	'title'       => 'widget-socialshare-title',
	'description' => 'widget-socialshare-desc',
	'version'     => '1.0.6',
	'author'      => 'CLSystems',
	'authorEmail' => 'info@clsystems.nl',
	'authorUrl'   => 'https://github.com/CLSystems',
	'updateUrl'   => null,
	'params'      => [
		[
			'name'     => 'networksNum',
			'type'     => 'Number',
			'label'    => 'limit-networks-number',
			'multiple' => true,
			'filters'  => ['uint'],
			'min'      => 1,
			'value'    => 10,
		],
	],
];
