<?php

return [
	'name'        => 'Backup',
	'group'       => 'System',
	'title'       => 'backup-plugin-title',
	'description' => 'backup-plugin-desc',
	'version'     => '1.0.0',
	'author'      => 'CLSystems',
	'authorEmail' => 'info@clsystems.nl',
	'authorUrl'   => 'https://github.com/CLSystems',
	'updateUrl'   => null,
	'params'      => [
		[
			'name'  => 'backup',
			'type'  => 'CmsBackup',
			'label' => 'backup-manage',
		],
	],
];
