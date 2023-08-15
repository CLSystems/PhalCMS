<?php

namespace CLSystems\PhalCMS\Lib\Mvc\Model;

use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Db\Enum;

class ApiCallLog extends ModelBase
{
    /**
     *
     * @var integer
     */
    public $sourceId;

    /**
     *
     * @var string
     */
    public $date;

    /**
     *
     * @var string
     */
    public $uri;

    /**
     *
     * @var string
     */
    public $script;

    /**
     *
     * @var integer
     */
    public $count;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSource('api_call_log');
    }
}
