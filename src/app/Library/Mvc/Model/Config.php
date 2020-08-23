<?php

namespace CLSystems\PhalCMS\Library\Mvc\Model;

use CLSystems\PhalCMS\Library\Form\Form;
use CLSystems\PhalCMS\Library\Form\FormsManager;
use CLSystems\PhalCMS\Library\Helper\Config as ConfigHelper;

class Config extends ModelBase
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
	public $context;

	/**
	 *
	 * @var string
	 */
	public $data;

	/**
	 *
	 * @var integer
	 */
	public $ordering;

	/**
	 * Initialize method for model.
	 */
	public function initialize()
	{
		$this->setSource('config_data');
	}

	public function beforeSave()
	{
		if (is_array($this->data)
			|| is_object($this->data)
		)
		{
			$this->data = json_encode($this->data);
		}

		return parent::beforeSave();
	}

	public function getFormsManager($bindData = true)
	{
		// Create forms
		$siteForm    = new Form('FormData', __DIR__ . '/Form/Config/Site.php');
		$localeForm  = new Form('FormData', __DIR__ . '/Form/Config/Locale.php');
		$userForm    = new Form('FormData', __DIR__ . '/Form/Config/User.php');
		$commentForm = new Form('FormData', __DIR__ . '/Form/Config/Comment.php');
		$systemForm  = new Form('FormData', __DIR__ . '/Form/Config/System.php');

		// Append forms to forms manager
		$formsManager = new FormsManager;
		$formsManager->set('site', $siteForm);
		$formsManager->set('locale', $localeForm);
		$formsManager->set('user', $userForm);
		$formsManager->set('comment', $commentForm);
		$formsManager->set('system', $systemForm);

		if ($bindData)
		{
			$formsManager->bind(ConfigHelper::get()->toArray());
		}

		return $formsManager;
	}
}
