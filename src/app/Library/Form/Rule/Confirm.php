<?php

namespace CLSystems\PhalCMS\Lib\Form\Rule;

use CLSystems\PhalCMS\Lib\Form\Field;

class Confirm implements Rule
{
	public function validate(Field $field)
	{
		if ($confirmField = $field->getConfirmField())
		{
			return $field->getValue() === $confirmField->getValue();
		}

		return false;
	}
}