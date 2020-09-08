<?php

namespace CLSystems\PhalCMS\Library\Form\Field;

use CLSystems\PhalCMS\Library\Helper\Asset;
use CLSystems\PhalCMS\Library\Helper\Text;
use CLSystems\PhalCMS\Library\Mvc\Model\UcmItem;
use CLSystems\PhalCMS\Library\Factory;

class CmsModalUcmItem extends CmsImage
{
	protected $context;
	protected $multiple = false;
	protected $modalFull = false;

	public function toString()
	{
		$value    = $this->getValue();
		$id       = $this->getId();
		$multiple = $this->multiple ? 'true' : 'false';

		if ($this->multiple)
		{
			$selectText = Text::_($this->context . '-items-select');
		}
		else
		{
			$selectText = Text::_($this->context . '-item-select');
		}

		if (!is_array($value))
		{
			$value = $value ? [(int) $value] : [];
		}

		$value = array_map('intval', $value);

		if (empty($value))
		{
			$items = [];
		}
		else
		{
			$items = UcmItem::find('id IN (' . implode(',', $value) . ')')->toArray();
		}

		$dispatcher = Factory::getService('dispatcher');
		$request    = Factory::getService('request');

		if ($dispatcher->getControllerName() === 'admin_menu'
			&& $request->has('type')
			&& $request->has('id')
		)
		{
			// This came from the menu page
			$this->modalFull = true;
		}

		Asset::addFile('ucm-item-modal.js');
		Asset::inlineJs('cmsCore.initUcmElementModal(\'' . $id . '\');');
		$this->class = rtrim('not-chosen uk-hidden ' . $this->class);

		return Factory::getService('view')
			->getPartial('Form/Field/ModalUcmItem',
				[
					'id'         => $id,
					'context'    => $this->context,
					'name'       => $this->getName(),
					'value'      => $value,
					'modalClass' => $this->modalFull ? 'uk-modal-full' : 'uk-modal-container',
					'modalClose' => $this->modalFull ? 'uk-modal-close-full' : 'uk-modal-close-default',
					'items'      => $items,
					'selectText' => $selectText,
					'multiple'   => $multiple,
					'input'      => parent::toInput(),
				]
			);
	}
}
