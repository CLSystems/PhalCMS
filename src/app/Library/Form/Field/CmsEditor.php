<?php

namespace CLSystems\PhalCMS\Library\Form\Field;

use CLSystems\PhalCMS\Library\Helper\Editor;

class CmsEditor extends TextArea
{
	public function toString()
	{
		$this->class = rtrim('js-editor-tinyMCE ' . $this->class);
		Editor::initTinyMCE();

		return parent::toString();
	}
}
