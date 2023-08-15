<?php

namespace CLSystems\PhalCMS\Lib\Mvc\Controller;

use CLSystems\PhalCMS\Lib\Mvc\Model\Tag;

class AdminTagController extends AdminControllerBase
{
	/** @var Tag */
	public $model = 'Tag';

	/** @var string */
	public $pickedView = 'Tag';

	/** @var null */
	public $stateField = null;
}
