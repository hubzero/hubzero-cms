<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\SupportActivity;

use Hubzero\Module\Module;
use Request;

/**
 * Module class for an activity feed
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
		if (!\App::isAdmin())
		{
			return;
		}

		$database = \App::get('db');

		$where = "";
		if ($start = Request::getString('start', ''))
		{
			$where = "WHERE a.created > " . $database->quote($start);
		}

		$query = "SELECT a.* FROM (
					(SELECT c.id, c.ticket, c.created, (CASE WHEN `comment` != '' THEN 'comment' ELSE 'change' END) AS 'category' FROM `#__support_comments` AS c)
					UNION
					(SELECT '0' AS id, t.id AS ticket, t.created, 'ticket' AS 'category' FROM `#__support_tickets` AS t)
				) AS a $where ORDER BY a.created DESC LIMIT 0, " . $this->params->get('limit', 25);

		$database->setQuery($query);
		$this->results = $database->loadObjectList();

		$this->feed = Request::getInt('feedactivity', 0);

		if ($this->feed == 1)
		{
			ob_clean();
			foreach ($this->results as $result)
			{
				require $this->getLayoutPath('default_item');
			}
			exit();
		}

		parent::display();
	}
}
