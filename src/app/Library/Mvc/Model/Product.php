<?php

namespace CLSystems\PhalCMS\Library\Mvc\Model;

use CLSystems\PhalCMS\Library\Form\Form;

class Product extends UcmItem
{
	public $context = 'product';
	public $hasRoute = true;

	public function getParent()
	{
		return $this->getRelated('category');
	}

	public function initialize()
	{
		parent::initialize();
		$this->belongsTo('parentId', PostCategory::class, 'id',
			[
				'alias'    => 'category',
				'reusable' => true,
				'params'   => [
					'order' => 'ordering ASC',
				],
			]
		);

		$this->hasManyToMany('id', UcmItemMap::class, 'itemId1', 'itemId2', Tag::class, 'id',
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
		$asideForm    = $formsManager->get('aside');
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
