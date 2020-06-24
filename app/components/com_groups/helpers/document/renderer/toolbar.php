<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Helpers\Document\Renderer;

use Components\Groups\Helpers\Document\Renderer;
use Components\Groups\Helpers\View;

class Toolbar extends Renderer
{
	/**
	 * Render toolbar to group template
	 *
	 * @param    string
	 */
	public function render()
	{
		return View::displayToolbar($this->group, 'id="group_options"', true);
	}
}
