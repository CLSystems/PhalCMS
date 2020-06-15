<?php

namespace CLSystems\PhalCMS\Lib\Form\Rule;

use CLSystems\PhalCMS\Lib\Form\Field;

interface Rule
{
	public function validate(Field $field);
}
