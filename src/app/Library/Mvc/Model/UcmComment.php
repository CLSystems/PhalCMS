<?php

namespace CLSystems\PhalCMS\Library\Mvc\Model;

use Phalcon\Http\Request;
use CLSystems\PhalCMS\Library\Helper\User as UserHelper;
use CLSystems\PhalCMS\Library\Helper\Uri;
use CLSystems\PhalCMS\Library\Factory;

class UcmComment extends ModelBase
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
	public $referenceContext;

	/**
	 *
	 * @var integer
	 */
	public $referenceId;

	/**
	 *
	 * @var integer
	 */
	public $parentId;

	/**
	 *
	 * @var integer
	 */
	public $userId;

	/**
	 *
	 * @var string
	 */
	public $userIp;

	/**
	 *
	 * @var string
	 */
	public $userAgent;

	/**
	 *
	 * @var string
	 */
	public $userName;

	/**
	 *
	 * @var string
	 */
	public $userEmail;

	/**
	 *
	 * @var integer
	 */
	public $userVote = 0;

	/**
	 *
	 * @var string
	 */
	public $userComment;

	/**
	 *
	 * @var string
	 */
	public $state = 'U';

	/**
	 *
	 * @var string
	 */
	public $createdAt;

	/**
	 *
	 * @var integer
	 */
	public $createdBy = 0;

	/**
	 *
	 * @var string
	 */
	public $modifiedAt = null;

	/**
	 *
	 * @var integer
	 */
	public $modifiedBy = 0;

	/**
	 *
	 * @var string
	 */
	public $checkedAt = null;

	/**
	 *
	 * @var integer
	 */
	public $checkedBy = 0;

	/**
	 * @var string
	 */

	protected $titleField = 'userName';

	/**
	 * Initialize method for model.
	 */

	/**
	 * @var boolean
	 */

	protected $standardMetadata = true;

	public function initialize()
	{
		$this->setSource('ucm_comments');
		$this->belongsTo('parentId', UcmComment::class, 'id',
			[
				'alias' => 'parent',
				'reuse' => true,
			]
		);

		$options = [
			'alias' => 'replies',
			'reuse' => true,
		];

		if (Uri::isClient('site'))
		{
			$options['params']['conditions'] = 'state = \'P\'';
		}

		$this->hasMany('id', UcmComment::class, 'parentId', $options);
	}

	public function getOrderFields()
	{
		return [
			'createdAt',
			'userName',
			'userEmail',
			'userComment',
			'id',
		];
	}

	public function getSearchFields()
	{
		return [
			'userName',
			'userEmail',
			'userComment',
		];
	}

	public function beforeValidation()
	{
		if (Uri::isClient('site'))
		{
			$this->userId = UserHelper::getInstance()->id;
		}

		/** @var Request $request */
		$request         = Factory::getService('request');
		$this->userIp    = $request->getClientAddress();
		$this->userAgent = $request->getUserAgent();
	}

	public function getAuthor()
	{
		if ($this->userId)
		{
			return UserHelper::getInstance($this->userId);
		}

		return false;
	}

	public function getFilterForm()
	{
		$form = parent::getFilterForm();
		$item = $form->getField('referenceId');

		if (!$item->get('context'))
		{
			$item->set('context', Factory::getService('dispatcher')->getParam('referenceContext', 'context'));
		}

		return $form;
	}
}
