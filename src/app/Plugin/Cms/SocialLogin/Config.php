<?php

return [
	'name'        => 'SocialLogin',
	'group'       => 'Cms',
	'version'     => '1.0.0',
	'title'       => 'sl-plugin-title',
	'description' => 'sl-plugin-desc',
	'author'      => 'CLSystems',
	'authorEmail' => 'info@clsystems.nl',
	'authorUrl'   => 'https://github.com/CLSystems',
	'updateUrl'   => null,
	'params'      => [
		[
			'name'          => 'facebookLogin',
			'type'          => 'Check',
			'label'         => 'sl-fb-login',
			'filters'       => ['yesNo'],
			'checkboxValue' => 'Y',
			'value'         => 'N',
		],
		[
			'name'    => 'facebookAppId',
			'type'    => 'Text',
			'label'   => 'sl-fb-app-id',
			'filters' => ['string', 'trim'],
			'showOn'  => 'facebookLogin : is checked',
		],
		[
			'name'    => 'facebookAppSecret',
			'type'    => 'Text',
			'label'   => 'sl-fb-app-secret',
			'filters' => ['string', 'trim'],
			'showOn'  => 'facebookLogin : is checked',
		],
		[
			'name'          => 'googleLogin',
			'type'          => 'Check',
			'label'         => 'sl-gg-login',
			'filters'       => ['yesNo'],
			'checkboxValue' => 'Y',
			'value'         => 'N',
		],
		[
			'name'    => 'googleClientId',
			'type'    => 'Text',
			'label'   => 'sl-gg-client-id',
			'filters' => ['string', 'trim'],
			'showOn'  => 'googleLogin : is checked',
		],
		[
			'name'    => 'googleClientSecret',
			'type'    => 'Text',
			'label'   => 'sl-gg-client-secret',
			'filters' => ['string', 'trim'],
			'showOn'  => 'googleLogin : is checked',
		],
	],
];
