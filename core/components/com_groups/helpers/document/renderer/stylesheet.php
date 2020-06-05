<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Helpers\Document\Renderer;

use Components\Groups\Helpers\Document\Renderer;

class Stylesheet extends Renderer
{
	/**
	 * Render content to group template
	 *
	 * @param    string
	 */
	public function render()
	{
		// get stylehseet base & source
		$base   = (isset($this->params->base)) ? strtolower($this->params->base) : 'uploads';
		$source = (isset($this->params->source)) ? trim($this->params->source) : null;

		// if we want base to be template,
		// shortcut to template/assets/css folder
		if ($base == 'template')
		{
			$base = 'template' . DS . 'assets' . DS . 'css';
		}

		// we must have a source
		if ($source === null)
		{
			return;
		}

		// get download path for source (serve up file)
		if ($path = $this->group->downloadLinkForPath($base, $source))
		{
			// add stylsheet to document
			\Document::addStylesheet($path);
		}
	}
}
