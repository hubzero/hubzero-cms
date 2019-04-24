<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\MyTodos;

use Hubzero\Module\Module;
use User;
use App;

/**
 * Module class for displaying a user's to do items
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
		$this->rows = array();

		// Find the user's most recent to do items
		if (!User::isGuest())
		{
			$database = App::get('db');
			$database->setQuery(
				"SELECT a.id, a.content, b.title, b.alias
				FROM `#__project_todo` a
				INNER JOIN `#__projects` b ON b.id=a.projectid
				WHERE a.assigned_to=" . $database->escape(User::get('id')) . " AND a.state=0 LIMIT 0, " . intval($this->params->get('limit', 10))
			);
			$this->rows = $database->loadObjectList();
		}

		// Push the module CSS to the template
		$this->css();

		require $this->getLayoutPath();
	}
}
