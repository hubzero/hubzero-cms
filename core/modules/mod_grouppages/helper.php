<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\GroupPages;

use Hubzero\Module\Module;
use Components\Groups\Models;
use Component;

/**
 * Module class for showing group pages
 */
class Helper extends Module
{
	/**
	 * Display module contents
	 *
	 * @return     void
	 */
	public function display()
	{
		if (!\App::isAdmin())
		{
			return;
		}

		// include group page archive model
		require_once Component::path('com_groups') . DS . 'models' . DS . 'page' . DS . 'archive.php';

		// include group module archive model
		require_once Component::path('com_groups') . DS . 'models' . DS . 'module' . DS . 'archive.php';

		// get unapproved pages
		$groupModelPageArchive = new Models\Page\Archive();
		$this->unapprovedPages = $groupModelPageArchive->pages('unapproved', array(
			'state' => array(0, 1)
		), true);

		// get unapproved modules
		$groupModelModuleArchive = new Models\Module\Archive();
		$this->unapprovedModules = $groupModelModuleArchive->modules('unapproved', array(
			'state' => array(0, 1)
		), true);

		// Get the view
		parent::display();
	}
}
