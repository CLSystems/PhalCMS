<?php

namespace CLSystems\PhalCMS\Library\Mvc\Controller;

use CLSystems\PhalCMS\Library\Mvc\Model\Tag;

class AdminTagController extends AdminControllerBase
{
	/** @var Tag */
	public $model = 'Tag';

	/** @var string */
	public $pickedView = 'Tag';

	/** @var null */
	public $stateField = null;
}
