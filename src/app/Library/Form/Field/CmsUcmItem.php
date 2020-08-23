<?php

namespace CLSystems\PhalCMS\Library\Form\Field;

use Phalcon\Dispatcher;
use CLSystems\PhalCMS\Library\Helper\UcmItem as UcmItemHelper;
use CLSystems\PhalCMS\Library\Helper\Text;
use CLSystems\PhalCMS\Library\Mvc\Model\UcmItem;
use CLSystems\PhalCMS\Library\Mvc\Model\Nested;
use CLSystems\PhalCMS\Library\Factory;

class CmsUcmItem extends Select
{
	protected $context = null;

	protected $showRoot = true;

	protected $rootText = 'no-parent';

	public function getOptions()
	{
		/**
		 * @var Nested     $class
		 * @var Dispatcher $dispatcher
		 */

		$dispatcher = Factory::getService('dispatcher');

		if (empty($this->context))
		{
			$this->context = $dispatcher->getParam('context');
		}

		$options    = parent::getOptions();
		$modelClass = 'CLSystems\\PhalCMS\\Lib\\Mvc\\Model\\' . UcmItemHelper::prepareContext($this->context);

		if (!class_exists($modelClass))
		{
			return $options;
		}

		$class = new $modelClass;

		if ($class instanceof Nested)
		{
			$excludes = [];

			if ('admin_ucm_item' === $dispatcher->getControllerName()
				&& $this->context === $dispatcher->getParam('context')
				&& ($id = $dispatcher->getParam('id', 'int', 0))
			)
			{
				foreach ($class->getTree($id) as $node)
				{
					$excludes[] = $node->id;
				}
			}

			$rootId = $class->getRootId();

			foreach ($class->getTree($rootId) as $node)
			{
				if ($node->id == $rootId)
				{
					if ($this->showRoot)
					{
						$options[$node->id] = Text::_($this->rootText);
					}
				}
				elseif (!in_array($node->id, $excludes))
				{
					$options[$node->id] = str_repeat('-', $node->level - 1) . ' ' . $node->title;
				}
			}
		}
		else
		{
			$entities = UcmItem::find(
				[
					'conditions' => 'state = :state: AND context = :context:',
					'bind'       => [
						'state'   => 'P',
						'context' => $this->context,
					],
				]
			);

			foreach ($entities as $entity)
			{
				$options[$entity->id] = $entity->title;
			}
		}

		return $options;
	}
}
