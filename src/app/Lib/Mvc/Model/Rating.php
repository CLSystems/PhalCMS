<?php

namespace CLSystems\PhalCMS\Lib\Mvc\Model;

use Phalcon\Mvc\Model;

class Rating extends Model
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
	public $sourceId;

	/**
	 *
	 * @var int
	 */
	public $externalMerchantId;

	/**
	 *
	 * @var string
	 */
	public $merchantName;

	/**
	 *
	 * @var int
	 */
	public $ratingValue;

	/**
	 *
	 * @var int
	 */
	public $ratingCount;

	/**
	 *
	 * @var string
	 */
	public $externalRatingUrl;

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
		$this->setSource('ratings');
	}
}
