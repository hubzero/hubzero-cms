<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Members;

use Hubzero\Module\Module;
use App;

/**
 * Module class for com_members data
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
		if (!App::isAdmin())
		{
			return;
		}

		$database = App::get('db');

		$database->setQuery("SELECT count(u.id) FROM `#__users` AS u WHERE u.activation < -1");
		$this->unconfirmed = $database->loadResult();

		$database->setQuery("SELECT count(u.id) FROM `#__users` AS u WHERE u.activation >= 1");
		$this->confirmed = $database->loadResult();

		$database->setQuery("SELECT count(*) FROM `#__users` WHERE registerDate >= " . $database->quote(gmdate('Y-m-d', (time() - 24*3600)) . ' 00:00:00'));
		$this->pastDay = $database->loadResult();

		// Get the view
		parent::display();
	}
}
