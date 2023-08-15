<?php

namespace CLSystems\PhalCMS\Lib\Helper;

use CLSystems\PhalCMS\Lib\Mvc\Model\Config as ConfigModel;
use CLSystems\Php\Registry;
use stdClass;

/**
 * Class Config
 *
 * @package CLSystems\PhalCMS\Lib\Helper
 */
class Config
{
	protected static $dataContexts = [];

    /**
     * @param string                  $context
     * @param \CLSystems\Php\Registry $configData
     */
	public static function setDataContext(string $context, Registry $configData): void
    {
		static::$dataContexts[$context] = $configData;
	}

    /**
     * @param string $context
     *
     * @return \CLSystems\Php\Registry
     */
	public static function getByContext(string $context = 'cms.config'): Registry
    {
		if (!isset(static::$dataContexts[$context]))
		{
			if ($entity = static::getEntity($context))
			{
				$data = json_decode($entity->data, true) ?: [];
			}
			else
			{
				$data = [];
			}

			self::$dataContexts[$context] = new Registry($data);
		}

		return self::$dataContexts[$context];
	}

	/**
	 * @param   null $index
	 * @param   null $default
	 * @param string $context
	 *
	 * @return mixed|Registry
	 */
	public static function get($index = null, $default = null, string $context = 'cms.config'): mixed
    {
		$config = self::getByContext($context);

		return null === $index ? $config : $config->get($index, $default);
	}

	/**
	 * @param $context
	 *
	 * @return ConfigModel
	 */
	public static function getEntity($context): ConfigModel
    {
		static $entities = [];

		if (!isset($entities[$context]))
		{
			$eval   = !str_contains($context, '%') ? '=' : 'LIKE';
			$entity = ConfigModel::findFirst(
				[
					'conditions' => 'context ' . $eval . ' :context:',
					'bind'       => [
						'context' => $context,
					],
				]
			);

			if (!$entity)
			{
				$entity          = new ConfigModel;
				$entity->context = $context;
			}

			$entities[$context] = $entity;
		}

		return $entities[$context];
	}

    /**
     * @return \stdClass|null
     */
	public static function getTemplate(): ?stdClass
    {
		static $template = null;

		if (null === $template)
		{
			$template        = new stdClass;
			$template->name  = self::get('siteTemplate');
			$configFile      = APP_PATH . '/Tmpl/Site/' . $template->name . '/Config.php';
			$configChildFile = APP_PATH . '/Tmpl/Site/' . $template->name . '/Tmpl/Config.php';
			$configData      = new Registry;

			if (is_file($configFile))
			{
				$configData->merge($configFile);
			}

			if (is_file($configChildFile))
			{
				$configData->merge($configChildFile);
			}

			$template->config = $configData;
		}

		return $template;
	}
}
