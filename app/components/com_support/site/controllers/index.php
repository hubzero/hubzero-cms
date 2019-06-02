<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Support\Site\Controllers;

use Hubzero\Component\SiteController;
use Document;
use Pathway;
use Lang;

/**
 * Displays support pages
 */
class Index extends SiteController
{
	/**
	 * Displays the main page for support
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Set the page title
		$this->view->title = Lang::txt(strtoupper($this->_option));

		Document::setTitle($this->view->title);

		// Set the pathway
		if (Pathway::count() <= 0)
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_option)),
				'index.php?option=' . $this->_option
			);
		}

		// Output HTML
		$this->view->display();
	}
}
