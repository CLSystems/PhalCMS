<?php

namespace CLSystems\PhalCMS\Library\Form\Rule;

use CLSystems\PhalCMS\Library\Form\Field;

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
