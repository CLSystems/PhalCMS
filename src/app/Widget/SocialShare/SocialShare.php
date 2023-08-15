<?php

namespace CLSystems\PhalCMS\Widget\SocialShare;

use CLSystems\PhalCMS\Lib\Widget;

/**
 * Class SocialShare
 *
 * @package CLSystems\PhalCMS\Widget\SocialShare
 */
class SocialShare extends Widget
{
	/**
	 * @return string
	 */
	public function getContent() : string
	{
		// Init renderer
		$renderer = $this->getRenderer();
		$partial = 'Content/' . $this->getPartialId();

		return $renderer->getPartial($partial, []);
	}
}
