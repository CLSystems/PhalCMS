<?php

namespace CLSystems\PhalCMS\Library\Mvc\Model;

use CLSystems\PhalCMS\Library\Helper\Uri;
use CLSystems\PhalCMS\Library\Form\FormsManager;

class ProductCategory extends Nested
{
	public $context = 'product-category';
	public $itemContext = 'product';
	public $hasRoute = true;

	public function initialize()
	{
		parent::initialize();
		$referenceModel = get_class($this);
		$params         = [
			'conditions' => '',
			'bind'       => [],
			'order'      => 'ordering ASC',
		];

		if (Uri::isClient('site'))
		{
			$params['conditions']    = 'state = :state:';
			$params['bind']['state'] = 'P';
		}
		else
		{
			$params['conditions']    = 'state <> :state:';
			$params['bind']['state'] = 'T';
		}

		$this->belongsTo(['context', 'parentId'], $referenceModel, ['context', 'id'],
			[
				'alias'    => 'parent',
				'reusable' => true,
				'params'   => $params,
			]
		);

		$this->hasMany(['context', 'id'], $referenceModel, ['context', 'parentId'],
			[
				'alias'    => 'children',
				'reusable' => true,
				'params'   => $params,
			]
		);
	}

	public function prepareFormsManager(FormsManager $formsManager)
	{
		$asideForm = $formsManager->get('aside');
		$asideForm->remove('tags');
		$asideForm->getField('parentId')->set('context', 'product-category');
	}
}
