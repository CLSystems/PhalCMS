<?php

namespace CLSystems\PhalCMS\Lib\Form\Rule;

use CLSystems\PhalCMS\Lib\Form\Field;
use CLSystems\PhalCMS\Lib\Mvc\Model\UcmItem;

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
