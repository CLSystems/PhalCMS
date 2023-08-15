<?php

namespace CLSystems\PhalCMS\Lib\Form\Field;

use CLSystems\PhalCMS\Lib\Helper\Editor;

class CmsEditor extends TextArea
{
	public function toString()
	{
		$this->class = rtrim('js-editor-tinyMCE ' . $this->class);
		Editor::initTinyMCE();

		return parent::toString();
	}
}