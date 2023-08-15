<?php

namespace CLSystems\PhalCMS\Lib\Mvc\Controller;

use CLSystems\PhalCMS\Lib\Mvc\Model\Formbuilder;

class AdminFormbuilderController extends AdminControllerBase
{
	/** @var Formbuilder */
	public $model = 'Formbuilder';

	/** @var string */
	public $pickedView = 'Formbuilder';

	/** @var null */
	public $stateField = null;

	public function indexToolBar($activeState = null, $excludes = ['copy'])
	{
		parent::indexToolBar($activeState, $excludes);
	}
}