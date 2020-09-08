<?php

namespace CLSystems\PhalCMS\Library\Form\Field;

use CLSystems\PhalCMS\Library\Helper\Language;

class CmsLanguage extends Select
{
	public function getOptions()
	{
		$options = [];

		foreach (Language::getExistsLanguages() as $langCode => $language)
		{
			$options[$langCode] = $language->get('locale.title');
		}

		return $options;
	}
}
