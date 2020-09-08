<?php

namespace CLSystems\PhalCMS\Library\Form\Rule;

use CLSystems\PhalCMS\Library\Form\Field;

interface Rule
{
	public function validate(Field $field);
}
