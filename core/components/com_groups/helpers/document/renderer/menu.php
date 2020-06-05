<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Helpers\Document\Renderer;

use Components\Groups\Helpers\Document\Renderer;
use Components\Groups\Helpers\View;

class Menu extends Renderer
{
	/**
	 * Render menu to group template
	 *
	 * @param    string
	 */
	public function render()
	{
		return View::displaySections($this->group, 'class="cf"');
	}
}
