<?php

return [
	[
		'name'      => 'route',
		'type'      => 'Text',
		'label'     => 'route',
		'translate' => true,
		'filters'   => ['path'],
	],
	[
		'name'  => 'parentId',
		'type'  => 'CmsUcmItem',
		'label' => 'parent-level',
		'rules' => ['Options'],
	],
//	[
//		'name'      => 'sourceId',
//		'type'      => 'CmsSource',
//		'label'     => 'Source',
//		'translate' => false,
//		//		'filters'   => ['path'],
//	],
//	[
//		'name'      => 'prefUrl',
//		'type'      => 'Text',
//		'label'     => 'External URL',
//		'translate' => false,
//		//		'filters'   => ['path'],
//	],
	[
		'name'      => 'image',
		'type'      => 'CmsImage',
		'multiple'  => true,
		'translate' => true,
		'filters'   => ['fileExists'],
	],
	[
		'name'     => 'hits',
		'type'     => 'Text',
		'label'    => 'Hits',
		'translate' => false,
		'multiple' => false,
//		'rules'    => ['Options'],
	],
	[
		'name'     => 'tags',
		'type'     => 'CmsTag',
		'label'    => 'tags',
		'multiple' => true,
		'rules'    => ['Options'],
	],
];