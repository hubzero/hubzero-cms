<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Users;

use Hubzero\Module\Module;
use App;

/**
 * Module class for com_users data
 */
class Helper extends Module
{
	/**
	 * Display module contents
	 *
	 * @return  void
	 */
	public function display()
	{
		if (!App::isAdmin())
		{
			return;
		}

		$database = App::get('db');
		$database->setQuery(
			"SELECT *
			FROM `#__users`
			WHERE `block`=0
			AND `activation`=1
			AND `approved`=0
			ORDER BY `registerDate` DESC
			LIMIT " . $this->params->get('limit', 25)
		);
		$this->unapproved = $database->loadObjectList();

		// Get the view
		parent::display();
	}
}
