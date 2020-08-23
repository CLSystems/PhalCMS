<?php

namespace CLSystems\PhalCMS\Library\Form\Field;

class Hidden extends InputAbstract
{
	protected $inputType = 'hidden';
	protected $inputClass = 'uk-hidden';

	public function render()
	{
		return $this->toString();
	}
}
