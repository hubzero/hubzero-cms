<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Developer\Site\Controllers;

use Hubzero\Component\SiteController;

/**
 * Web ceveloper controller
 */
class Web extends SiteController
{
	/**
	 * Display intro page
	 * 
	 * @return void
	 */
	public function displayTask()
	{
		$this->view->display();
	}
}
