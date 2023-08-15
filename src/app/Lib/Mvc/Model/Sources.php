<?php

namespace CLSystems\PhalCMS\Lib\Mvc\Model;

use Phalcon\Mvc\Model;

class Sources extends Model
{
    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var string
     */
    public $name;

    /**
     *
     * @var string
     */
    public $createdAt;

    /**
     *
     * @var integer
     */
    public $createdBy;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSource('sources');
    }
}
