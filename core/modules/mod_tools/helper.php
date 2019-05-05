<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Tools;

use Hubzero\Module\Module;
use App;

/**
 * Module class for com_tools data
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

		$this->total      = 0;
		$this->registered = 0;
		$this->created    = 0;
		$this->uploaded   = 0;
		$this->installed  = 0;
		$this->updated    = 0;
		$this->approved   = 0;
		$this->published  = 0;
		$this->retired    = 0;
		$this->abandoned  = 0;

		$database = App::get('db');
		$database->setQuery(
			"SELECT f.state FROM `#__tool` as f
				JOIN `#__tool_version` AS v ON f.id=v.toolid AND v.state=3
				WHERE f.id != 0 ORDER BY f.state_changed DESC"
		);
		$this->data = $database->loadObjectList();
		if ($this->data)
		{
			$this->total = count($this->data);

			foreach ($this->data as $data)
			{
				switch ($data->state)
				{
					case 1:
						$this->registered++;
						break;
					case 2:
						$this->created++;
						break;
					case 3:
						$this->uploaded++;
						break;
					case 4:
						$this->installed++;
						break;
					case 5:
						$this->updated++;
						break;
					case 6:
						$this->approved++;
						break;
					case 7:
						$this->published++;
						break;
					case 8:
						$this->retired++;
						break;
					case 9:
						$this->abandoned++;
						break;
				}
			}
		}

		// Get the view
		parent::display();
	}
}
