<?php

namespace CLSystems\PhalCMS\Lib\Form\Rule;

use CLSystems\PhalCMS\Lib\Form\Field;
use CLSystems\PhalCMS\Lib\Helper\User;

class Password implements Rule
{
	public function validate(Field $field)
	{
		$value    = $field->getValue();
		$required = $field->get('required', false);

		if (empty($value) && !$required)
		{
			return true;
		}

		return true === User::validatePassword($value);
	}
}