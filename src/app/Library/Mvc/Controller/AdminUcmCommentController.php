<?php

namespace CLSystems\PhalCMS\Library\Mvc\Controller;

use Phalcon\Mvc\Model\Query\BuilderInterface;
use CLSystems\PhalCMS\Library\Helper\Uri;
use CLSystems\PhalCMS\Library\Helper\Event as EventHelper;
use CLSystems\PhalCMS\Library\Mvc\Model\UcmComment;
use CLSystems\PhalCMS\Library\Helper\Text;
use CLSystems\PhalCMS\Library\Form\FormsManager;

class AdminUcmCommentController extends AdminControllerBase
{
	/** @var UcmComment */
	public $model = 'UcmComment';

	/** @var string */
	public $pickedView = 'UcmComment';

	public function onConstruct()
	{
		parent::onConstruct();
		EventHelper::trigger('beforeUcmComment' . ucfirst($this->dispatcher->getActionName()), [$this], ['Cms']);
		$this->model->referenceContext = $this->dispatcher->getParam('referenceContext');
	}

	protected function prepareIndexQuery(BuilderInterface $query)
	{
		$query->andWhere('item.referenceContext = :context:', ['context' => $this->model->referenceContext]);
	}

	protected function prepareUri(Uri $uri)
	{
		$baseUri = $this->dispatcher->getParam('referenceContext') . '/comment';
		$uri->setVar('uri', $baseUri);
		$uri->setBaseUri($baseUri);
	}

	protected function indexTitle()
	{
		$this->tag->setTitle(Text::_($this->model->referenceContext . '-comment-admin-index-title'));
	}

	protected function editTitle()
	{
		if ($this->model->id)
		{
			$this->tag->setTitle(Text::_($this->model->referenceContext . '-comment-admin-edit-title', ['userName' => $this->model->userName]));
		}
		else
		{
			$this->tag->setTitle(Text::_($this->model->referenceContext . '-comment-admin-add-title'));
		}
	}

	protected function prepareFormsManager(FormsManager $formsManager)
	{
		$generalForm = $formsManager->get('general');
		$generalForm->getField('referenceContext')->setValue($this->model->referenceContext);
		$generalForm->getField('userIp')->setValue($this->request->getClientAddress());
		$generalForm->getField('referenceId')
			->set('context', $this->model->referenceContext)
			->set('requireMessage', $this->model->referenceContext . '-reference-required');
		EventHelper::trigger('prepareUcmCommentFormsManager', [$formsManager], ['Cms']);
	}

	public function indexToolBar($activeState = null, $excludes = ['add', 'copy'])
	{
		parent::indexToolBar($activeState, $excludes);
	}
}
