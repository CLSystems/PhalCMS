<?php

namespace CLSystems\PhalCMS\Lib;

use Phalcon\Html\Escaper;
use Phalcon\Html\TagFactory;
use Phalcon\Support\Debug;
use Phalcon\Mvc\Url;
use Phalcon\Config\Adapter\Ini;
use Phalcon\Di\FactoryDefault;
use Phalcon\Session\Manager;
use Phalcon\Session\Adapter\Stream;
use Phalcon\Session\Bag;
use Phalcon\Db\Adapter\Pdo\Mysql;
use CLSystems\PhalCMS\Lib\Helper\Asset;
use CLSystems\PhalCMS\Lib\Helper\Config;
use CLSystems\PhalCMS\Lib\Helper\Event;
use CLSystems\PhalCMS\Lib\Helper\Language;
use CLSystems\PhalCMS\Lib\Mvc\View\ViewBase;
use CLSystems\Php\Registry;
use Exception;

if (!function_exists('debugVar'))
{
    function debugVar($var)
    {
        (new Debug)
            ->debugVar($var)
            ->listen(true, true)
            ->halt();
    }
}

class Factory
{
    /** @var Registry $config */
    protected static $config;

    /** @var CmsApplication $application */
    protected static $application;

    protected static function loadConfig()
    {
        if (!is_file(BASE_PATH . '/src/config.ini')) {
            if (is_file(BASE_PATH . '/public/install.php')) {
                $protocol = 'http';

                if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
                    $protocol .= 's';
                }

                header('location: ' . $protocol . '://' . $_SERVER['HTTP_HOST'] . '/install.php');
            } else {
                die('The config INI file not found at ' . BASE_PATH . '/src/config.ini');
            }
        }

        require_once BASE_PATH . '/src/app/Config/Define.php';
        require_once BASE_PATH . '/src/app/Config/Loader.php';
        require_once BASE_PATH . '/vendor/autoload.php';

        return new Ini(BASE_PATH . '/src/config.ini', INI_SCANNER_NORMAL);
    }

    public static function getApplication()
    {
        if (!isset(self::$application)) {
            $config = self::loadConfig();
            $dbPrefix = $config->path('DB.PREFIX');

            try {
                $db = new Mysql(
                    [
                        'host'     => $config->path('DB.HOST'),
                        'username' => $config->path('DB.USER'),
                        'password' => $config->path('DB.PASS'),
                        'dbname'   => $config->path('DB.NAME'),
                        'charset'  => 'utf8mb4',
                    ]
                );

            } catch (Exception $e) {
                die($e->getMessage());
            }

            $registry = new Registry(
                [
                    'siteTemplate' => $config->path('APP.TEMPLATE') ?? 'PhalCMS',
                    'core'         => [
                        'plugins' => [
							'CLSystems\\PhalCMS\\Plugin\\System\\Cms\\Cms',
                        ],
                        'widgets' => [
                            'CLSystems\\PhalCMS\\Widget\\Code\\Code',
                            'CLSystems\\PhalCMS\\Widget\\Content\\Content',
                            'CLSystems\\PhalCMS\\Widget\\FlashNews\\FlashNews',
                            'CLSystems\\PhalCMS\\Widget\\LanguageSwitcher\\LanguageSwitcher',
                            'CLSystems\\PhalCMS\\Widget\\Login\\Login',
                            'CLSystems\\PhalCMS\\Widget\\Menu\\Menu',
                        ],
                    ],
                ]
            );

            if ($extraConfig = $db->fetchColumn('SELECT data FROM ' . $dbPrefix . 'config_data WHERE context = \'cms.config\'')) {
                $registry->merge($extraConfig);
            }

            define('ADMIN_URI_PREFIX', $registry->get('adminPrefix', 'admin'));
            define('DEVELOPMENT_MODE', $registry->get('development', 'Y') === 'Y');
            Config::setDataContext('cms.config', $registry);

            if (true === DEVELOPMENT_MODE) {
                ini_set('display_errors', true);
                error_reporting(E_ALL);

                if (!defined('TEST_PHPUNIT_MODE')) {
                    (new Debug())->listen(true, false);
                }
            }

            // Create DI Factory Default
            $di = new FactoryDefault();

            // Set URL service before to use debug
            $di->setShared('url', new Url);
            $di->setShared('config', $config);
            $escaper = new Escaper();
            $tagFactory = new TagFactory($escaper);
            $di->setShared('assets', new Asset($tagFactory));
            $di->getShared('modelsManager')->setModelPrefix($dbPrefix);
            $di->setShared('db', $db);
            $di->getShared('flashSession')
                ->setAutoescape(false)
                ->setCssClasses(
                    [
                        'error'   => 'uk-alert uk-alert-danger',
                        'success' => 'uk-alert uk-alert-success',
                        'notice'  => 'uk-alert uk-alert-warning',
                        'warning' => 'uk-alert uk-alert-warning',
                    ]
                );

            $di->setShared('session', function () {
                $session = new Manager;
                $session->setAdapter(new Stream);
                $session->start();

                return $session;
            });

            $di->setShared('sessionBag', function  ()  {
                $sessionBag = new Manager;
                return new Bag($sessionBag, 'controller.persistent');
            });

            $di->setShared('router', function () {
                $router = include CONFIG_PATH . '/Router.php';
                Event::trigger('onBeforeServiceSetRouter', [$router], ['System', 'Cms']);

                return $router;
            });

            $di->getShared('crypt')
                ->setCipher('aes-256-ctr')
                ->setKey($config->path('SECRET.CRYPT_KEY'));
            $di->getShared('dispatcher')
                ->setEventsManager($di->getShared('eventsManager'));
            $di->setShared('view', ViewBase::getInstance($di));

            // Initialise config data
            self::$config = new Registry($config->toArray());

            // Initialise application
            self::$application = new CmsApplication($di);

            // Initialise languages
            Language::initialise();
        }

        return self::$application;
    }

    public static function getConfig()
    {
        return self::$config;
    }

    public static function getService($name, $parameters = null)
    {
        $di = self::getApplication()->getDI();

        switch ($name) {
            case 'url':
            case 'view':
            case 'db':
            case 'modelsMetadata':
            case 'modelsManager':
            case 'session':
            case 'flashSession':
            case 'cookies':
            case 'security':
            case 'dispatcher':
            case 'router':
            case 'filter':
            case 'crypt':
            case 'request':
            case 'response':
                return $di->getShared($name, $parameters);
        }

        return $di->get($name, $parameters);
    }
}
