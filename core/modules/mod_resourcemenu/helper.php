<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\ResourceMenu;

use Hubzero\Module\Module;
use stdClass;
use Event;

/**
 * Module class for displaying a megamenu
 */
class Helper extends Module
{
	/**
	 * Display module content
	 *
	 * @return  void
	 */
	public function display()
	{
		$this->moduleid    = $this->params->get('moduleid');
		$this->moduleclass = $this->params->get('moduleclass');

		// Build the HTML
		$obj = new stdClass;
		$obj->text = $this->params->get('content');

		// Get the search result totals
		$results = Event::trigger(
			'content.onPrepareContent',
			array(
				'',
				$obj,
				$this->params
			)
		);

		$this->html = $obj->text;

		// Push some CSS to the tmeplate
		$this->css()
		     ->js();

		require $this->getLayoutPath();
	}
}
