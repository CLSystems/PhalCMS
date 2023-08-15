<?php

namespace CLSystems\PhalCMS\Lib\Mvc\Model;

use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Db\Enum;

class Ad extends ModelBase
{
	/**
	 *
	 * @var int
	 */
	public $id;

	/**
	 *
	 * @var int
	 */
	public $externalId;

	/**
	 *
	 * @var int
	 */
	public $externalMerchantId;

	/**
	 *
	 * @var int
	 */
	public $sourceId;

    /**
     *
     * @var string
     */
    public $prefUrl;

    /**
     *
     * @var string
     */
    public $image;

    /**
     *
     * @var string
     */
    public $createdAt;

    /**
     *
     * @var int
     */
    public $createdBy;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSource('ads');
    }
}
