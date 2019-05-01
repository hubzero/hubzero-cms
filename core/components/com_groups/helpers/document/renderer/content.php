<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Helpers\Document\Renderer;

use Components\Groups\Helpers\Document\Renderer;
use Components\Groups\Helpers\View;

class Content extends Renderer
{
	/**
	 * Render content to group template
	 *
	 * @param    string
	 */
	public function render()
	{
		// get the scope
		$scope = (isset($this->params->scope)) ? $this->params->scope : 'main';

		// based on which scope display content
		switch ($scope)
		{
			case 'before':
				return View::displayBeforeSectionsContent($this->group);
			break;
			default:
				return $this->content;
		}
	}
}
