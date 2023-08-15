<?php

namespace CLSystems\PhalCMS\Lib\Helper;

use Phalcon\Assets\Manager as AssetsManager;
use CLSystems\PhalCMS\Lib\Factory;

class Asset extends AssetsManager
{
	protected static $code = [];
	protected static $assets = [
		'js'  => [],
		'css' => [],
	];

	public static function getFiles()
	{
		return self::$assets;
	}

	public static function core()
	{
		static $core = false;

		if (!$core)
		{
			$core = true;
			self::addFiles(
				[
					'core.js',
				]
			);
		}
	}

	public static function chosenCore()
	{
		static $chosen = false;

		if (!$chosen)
		{
			$chosen = true;
			self::addFiles(
				[
					'chosen/chosen.min.css',
					'chosen/chosen.jquery.min.js',
				]
			);
		}
	}

	public static function chosen($selector = '.select-chosen', $options = [])
	{
		self::chosenCore();
		$options = json_encode(array_merge([
			'rtl'                      => Text::_('direction', null, 'Locale') === 'rtl' ? true : false,
			'disable_search_threshold' => 10,
			'width'                    => '100%',
			'allow_single_deselect'    => true,
			'allow_custom_value'       => true,
		], $options));
		Factory::getService('assets')
			->addInlineJs(<<<JAVASCRIPT
$('{$selector}:not(.not-chosen)').addClass('has-chosen').chosen({$options}); 
JAVASCRIPT
			);
	}

	public static function select2Core()
	{
		static $chosen = false;

		if (!$chosen)
		{
			$chosen = true;
			self::addFiles(
				[
					'select2/css/select2.min.css',
					'select2/js/select2.full.js',
				]
			);
		}
	}

	public static function select2($selector = '.select-select2', $options = [])
	{
		self::select2Core();
		$options = json_encode(array_merge([
			'dir'                      => Text::_('direction', null, 'Locale') === 'rtl' ? true : false,
			'minimumInputLength'       => 3,
			'multiple'                 => true,
			'width'                    => '100%',
			'ajax'                     => [
				'url' => '//',
				'dataType' => 'json',
				'delay' => 250,
				'data' => function ($params) {
					return [
						'q' => $params['term'], // search term
						'page' => $params['page']
					];
				},
				'processResults' => function ($data, $params) {
					// parse the results into the format expected by Select2
					// since we are using custom formatting functions we do not need to
					// alter the remote JSON data, except to indicate that infinite
					// scrolling can be used
					$params['page'] = $params['page'] || 1;

					return [
						'results' => $data['items'],
						'pagination' => [
							'more' => ($params['page'] * 30) < $data['total_count']
						]
					];
				},
				'cache' => true,
			],
			'placeholder' => 'Search for a tag',
		], $options));
		Factory::getService('assets')
			->addInlineJs(<<<JAVASCRIPT
$('{$selector}:not(.not-chosen)').addClass('has-chosen').select2({$options}); 
JAVASCRIPT
			);
	}

	public static function tabState()
	{
		static $tabState = false;

		if (!$tabState)
		{
			$tabState = true;
			self::addFile('tab-state.js');
		}
	}

	public static function reactJsCore()
	{
		static $reactJs = false;

		if (!$reactJs)
		{
			$reactJs = true;
			$mode    = DEVELOPMENT_MODE ? 'development' : 'production.min';
			$assets  = Factory::getService('assets')
				->addJs('https://unpkg.com/react@16/umd/react.' . $mode . '.js', false, false, ['crossorigin' => ''])
				->addJs('https://unpkg.com/react-dom@16/umd/react-dom.' . $mode . '.js', false, false, ['crossorigin' => '']);

			if ($mode === 'development')
			{
				$assets->addJs('https://unpkg.com/babel-standalone@6/babel.min.js', false, false, null);
			}
		}
	}

	public static function tagEditorCore()
	{
		static $tagEditor = false;

		if (!$tagEditor)
		{
			$tagEditor = true;
			self::addFiles(
				[
					'tag-editor/jquery.tag-editor.min.css',
					'tag-editor/jquery.tag-editor.min.js',
				]
			);
		}
	}

	public static function tagEditor($selector = '.tag-area', $options = [])
	{
		self::tagEditorCore();
		$options = json_encode($options);
		Factory::getService('assets')->addInlineJs(<<<JAVASCRIPT
$('{$selector}').addClass('tag-area').tagEditor({$options}); 
JAVASCRIPT
		);
	}

	public static function jui()
	{
		static $jui = false;

		if (!$jui)
		{
			$jui = true;
			self::addFiles(
				[
					'js/jquery-ui/jquery-ui.js',
					'js/jquery-ui/jquery-ui.css',
				]
			);
		}
	}

	public static function calendar()
	{
		static $calendar = false;

		if (!$calendar)
		{
			self::jui();
			$calendar = true;
			$langCode = Language::getActiveCode();

			if (is_file(BASE_PATH . '/public/assets/js/jquery-ui/i18n/datepicker-' . $langCode . '.js'))
			{
				self::addFile('jquery-ui/i18n/datepicker-' . $langCode . '.js');
			}

			self::addFiles(
				[
					'calendar.css',
					'calendar.js',
				]
			);
		}
	}

	public static function addFiles(array $baseFiles, $basePath = PUBLIC_PATH . '/assets')
	{
		foreach ($baseFiles as $baseFile)
		{
			self::addFile($baseFile, $basePath);
		}
	}

	public static function addFile($baseFile, $basePath = PUBLIC_PATH . '/assets')
	{
		static $addedFiles = [];
		$key = $basePath . ':' . $baseFile;

		if (array_key_exists($key, $addedFiles))
		{
			return true;
		}

		$addedFiles[$key] = true;

		if (preg_match('/\.js$/', $baseFile))
		{
			$t = 'js';
		}
		else
		{
			$t = 'css';
		}

		$file = null;

		if (preg_match('/^https?:/', $baseFile))
		{
			$assets = Factory::getService('assets');

			return call_user_func_array([$assets, 'add' . ucfirst($t)], [$baseFile, false]);
		}

		if (is_file($baseFile))
		{
			$file = $baseFile;
		}
		elseif (is_file($basePath . '/' . $baseFile))
		{
			$file = $basePath . '/' . $baseFile;
		}
		elseif (is_file($basePath . '/' . $t . '/' . $baseFile))
		{
			$file = $basePath . '/' . $t . '/' . $baseFile;
		}

		if (!$file || in_array($file, self::$assets[$t]))
		{
			return false;
		}

		self::$assets[$t][] = $file;
	}

	public static function addCode($code)
	{
		self::$code[] = $code;
	}

	public static function getCode()
	{
		return implode(PHP_EOL, self::$code);
	}

	public static function inlineCss($css)
	{
		Factory::getService('assets')
			->addInlineCss($css);
	}

	public static function inlineJs($js)
	{
		Factory::getService('assets')
			->addInlineJs($js);
	}
}