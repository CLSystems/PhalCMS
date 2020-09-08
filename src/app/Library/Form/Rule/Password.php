<?php

namespace CLSystems\PhalCMS\Library\Form\Rule;

use CLSystems\PhalCMS\Library\Form\Field;
use CLSystems\PhalCMS\Library\Helper\User;

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
