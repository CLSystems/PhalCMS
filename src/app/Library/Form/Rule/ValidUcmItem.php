<?php

namespace CLSystems\PhalCMS\Library\Form\Rule;

use CLSystems\PhalCMS\Library\Form\Field;
use CLSystems\PhalCMS\Library\Mvc\Model\UcmItem;

class ValidUcmItem implements Rule
{
	public function validate(Field $field)
	{
		$value   = (int) $field->getValue();
		$isValid = false;

		if ($value > 0)
		{
			$isValid = UcmItem::findFirst('state = \'P\' AND id = ' . $value) ? true : false;
		}

		return $isValid;
	}
}
