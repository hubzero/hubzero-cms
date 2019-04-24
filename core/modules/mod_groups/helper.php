<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Groups;

use Hubzero\Module\Module;
use Hubzero\Utility\Date;
use App;

/**
 * Module class for com_groups data
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

		$type = $this->params->get('type', '1');

		switch ($type)
		{
			case '0':
				$this->type = 'system';
				break;
			case '1':
				$this->type = 'hub';
				break;
			case '2':
				$this->type = 'project';
				break;
			case '3':
				$this->type = 'partner';
				break;
		}

		$queries = array(
			'visible'    => "approved=1 AND discoverability=0",
			'hidden'     => "approved=1 AND discoverability=1",
			'closed'     => "join_policy=3",
			'invite'     => "join_policy=2",
			'restricted' => "join_policy=1",
			'open'       => "join_policy=0",
			'approved'   => "approved=1",
			'pending'    => "approved=0"
		);

		$database = App::get('db');

		foreach ($queries as $key => $where)
		{
			$database->setQuery("SELECT count(*) FROM `#__xgroups` WHERE type='$type' AND $where");
			$this->$key = $database->loadResult();
		}

		// Last 24 hours
		$lastDay = with(new Date('now'))->subtract('1 Day')->toSql();

		$database->setQuery("SELECT count(*) FROM `#__xgroups` WHERE created >= '$lastDay' AND type='$type'");
		$this->pastDay = $database->loadResult();

		// Get the view
		parent::display();
	}
}
