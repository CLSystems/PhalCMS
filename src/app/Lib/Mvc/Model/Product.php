<?php

namespace CLSystems\PhalCMS\Lib\Mvc\Model;

use CLSystems\PhalCMS\Lib\Form\Form;

class Product extends UcmItem
{
	/**
	 * @var string
	 */
	public $context = 'product';

	/**
	 * @var bool
	 */
	public $hasRoute = true;

	/**
	 * @var int
	 */
	public $id;

	/**
	 * @var string
	 */
	public $state;

	/**
	 * @var int
	 */
	public $parentId;

	/**
	 * @var string
	 */
	public $title;

	/**
	 * @var string
	 */
	public $route;

	/**
	 * @var string
	 */
	public $image;

	/**
	 * @var string
	 */
	public $summary;

	/**
	 * @var string
	 */
	public $description;

	/**
	 * @var int
	 */
	public $level;

	/**
	 * @var int
	 */
	public $lft;

	/**
	 * @var int
	 */
	public $rgt;

	/**
	 * @var string
	 */
	public $createdAt;

	/**
	 * @var int
	 */
	public $createdBy;

	/**
	 * @var string
	 */
	public $modifiedAt;

	/**
	 * @var int
	 */
	public $modifiedBy;

	/**
	 * @var string
	 */
	public $checkedAt;

	/**
	 * @var int
	 */
	public $checkedBy;

	/**
	 * @var string
	 */
	public $metaTitle;

	/**
	 * @var string
	 */
	public $metaDesc;

	/**
	 * @var string
	 */
	public $metaKeys;

	/**
	 * @var int
	 */
	public $hits;

	/**
	 * @var int
	 */
	public $ordering;

	/**
	 * @var string
	 */
	public $params;

	/**
	 * @var int
	 */
	public $sourceId;

	/**
	 * @var int
	 */
	public $externalId;

	/**
	 * @var int
	 */
	public $mediaId;

	/**
	 * @var string
	 */
	public $prefUrl;


	public function getParent()
	{
		return $this->getRelated('category');
	}

	public function initialize()
	{
		parent::initialize();
		$this->belongsTo(
			'parentId',
			ProductCategory::class,
			'id',
			[
				'alias'    => 'category',
				'reusable' => true,
				'params'   => [
					'order' => 'ordering ASC',
				],
			]
		);

		$this->hasManyToMany(
			'id',
			UcmItemMap::class,
			'itemId1',
			'itemId2',
			Tag::class,
			'id',
			[
				'alias'    => 'tags',
				'reusable' => true,
				'params'   => [
					'conditions' => UcmItemMap::class . '.context = :context:',
					'bind'       => [
						'context' => 'tag',
					],
				],
			]
		);
	}

	public function getFilterForm()
	{
		$form = parent::getFilterForm();
		$form->getField('parentId')->set('context', 'product-category');

		return $form;
	}

	public function getFormsManager()
	{
		$formsManager = parent::getFormsManager();
		$asideForm = $formsManager->get('aside');
		$asideForm->getField('parentId')->set('context', 'product-category');

		return $formsManager;
	}

	public function getParamsFormsManager()
	{
		$paramsFormManager = parent::getParamsFormsManager();
		$paramsFormManager->set('params', new Form('FormData.params', __DIR__ . '/Form/UcmItem/Comment.php'));

		return $paramsFormManager;
	}
}
