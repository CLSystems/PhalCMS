<?php

namespace CLSystems\PhalCMS\Library\Helper;

use CLSystems\PhalCMS\Library\Mvc\Model\Config as ConfigModel;
use CLSystems\Php\Registry;
use stdClass;

class Config
{
    protected static $dataContexts = [];

    /**
     * @param string $context
     * @param Registry $configData
     * @return void
     */
    public static function setDataContext($context, Registry $configData)
    {
        static::$dataContexts[$context] = $configData;
    }

    public static function getByContext($context = 'cms.config')
    {
        if (!isset(static::$dataContexts[$context])) {
            if ($entity = static::getEntity($context)) {
                $data = json_decode($entity->data, true) ?: [];
            } else {
                $data = [];
            }

            self::$dataContexts[$context] = new Registry($data);
        }

        return self::$dataContexts[$context];
    }

    /**
     * @param null $index
     * @param null $default
     * @param string $context
     *
     * @return mixed|Registry
     */

    public static function get($index = null, $default = null, $context = 'cms.config')
    {
        $config = self::getByContext($context);

        return null === $index ? $config : $config->get($index, $default);
    }

    /**
     * @param $context
     *
     * @return ConfigModel
     */

    public static function getEntity($context)
    {
        static $entities = [];

        if (!isset($entities[$context])) {
            $eval = strpos($context, '%') === false ? '=' : 'LIKE';
            $entity = ConfigModel::findFirst(
                [
                    'conditions' => 'context ' . $eval . ' :context:',
                    'bind'       => [
                        'context' => $context,
                    ],
                ]
            );

            if (!$entity) {
                $entity = new ConfigModel;
                $entity->context = $context;
            }

            $entities[$context] = $entity;
        }

        return $entities[$context];
    }

    public static function getTemplate()
    {
        static $template = null;

        if (null === $template) {
            $template = new stdClass;
            $template->name = self::get('siteTemplate');
            $configFile = APP_PATH . '/Tmpl/Site/' . $template->name . '/Config.php';
            $configChildFile = APP_PATH . '/Tmpl/Site/' . $template->name . '/Tmpl/Config.php';
            $configData = new Registry;

            if (is_file($configFile)) {
                $configData->merge($configFile);
            }

            if (is_file($configChildFile)) {
                $configData->merge($configChildFile);
            }

            $template->config = $configData;
        }

        return $template;
    }
}
