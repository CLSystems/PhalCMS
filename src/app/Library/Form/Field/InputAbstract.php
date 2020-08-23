<?php

namespace CLSystems\PhalCMS\Library\Form\Field;

use CLSystems\PhalCMS\Library\Helper\Asset;
use CLSystems\PhalCMS\Library\Form\Field;

class InputAbstract extends Field
{
	/** @var string */
	protected $inputType = 'text';

	/** @var string */
	protected $inputClass = 'uk-input';

	/** @var string */
	protected $hint = null;

	/** @var string */
	protected $autocomplete = null;

	/** @var boolean */
	protected $useEmoji = false;

	public function toString()
	{
		$class = trim($this->class . ' ' . $this->inputClass);

		if ($this->useEmoji)
		{
			Asset::addFile('emoji.js');
			$class .= ' input-emoji';
		}

		$value = $this->getValue();

		if (is_array($value) || is_object($value))
		{
			$value = json_encode($value);
		}

		$input = '<input class="' . $class . '"'
			. ' name="' . $this->getName() . '" type="' . $this->inputType . '" id="' . $this->getId() . '"'
			. ' value="' . htmlspecialchars($value, ENT_COMPAT, 'UTF-8') . '"'
			. $this->getDataAttributesString();

		if ($this->required)
		{
			$input .= ' required';
		}

		if ($this->readonly)
		{
			$input .= ' readonly';
		}

		if ($this->hint)
		{
			$input .= ' placeholder="' . htmlspecialchars($this->hint, ENT_COMPAT, 'UTF-8') . '"';
		}

		if ($this->autocomplete)
		{
			$input .= ' autocomplete="' . htmlspecialchars($this->autocomplete, ENT_COMPAT, 'UTF-8') . '"';
		}

		$this->prepareInputAttribute($input);

		$input .= '/>';

		return $input;
	}

	protected function prepareInputAttribute(&$input)
	{

	}
}
