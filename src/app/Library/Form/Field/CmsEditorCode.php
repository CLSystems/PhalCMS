<?php

namespace CLSystems\PhalCMS\Library\Form\Field;

use CLSystems\PhalCMS\Library\Helper\Editor;
use CLSystems\PhalCMS\Library\Helper\Text;

class CmsEditorCode extends TextArea
{
	public function toString()
	{
		$this->class = rtrim('js-editor-codemirror ' . $this->class);
		Editor::initCodeMirror();

		return '<p class="uk-text-meta">' . Text::_('f10-toggle-full-screen-desc') . '</p>' . parent::toString();
	}
}
