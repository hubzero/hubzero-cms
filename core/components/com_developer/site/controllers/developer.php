<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Developer\Site\Controllers;

use Hubzero\Component\SiteController;
use Document;
use Pathway;
use Lang;

/**
 * Main developer portal controller
 */
class Developer extends SiteController
{
	/**
	 * Developer Intro Page
	 * 
	 * @return void
	 */
	public function displayTask()
	{
		if (Pathway::count() <= 0)
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_option)),
				'index.php?option=' . $this->_option
			);
		}

		Document::setTitle(Lang::txt(strtoupper($this->_option)));

		$this->view->display();
	}
}
