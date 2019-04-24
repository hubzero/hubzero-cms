<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Answers;

use Hubzero\Module\Module;
use User;
use App;

/**
 * Module class for com_answers data
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

		$queries = array(
			'closed'  => "state=1",
			'open'    => "state=0",
			'pastDay' => "created >= " . $database->quote(gmdate('Y-m-d', (time() - 24*3600)) . ' 00:00:00')
		);

		if ($this->params->get('showMine', 0))
		{
			$this->username = User::get('username');

			// My Open
			$queries['myclosed'] = "state=1 AND created_by=" . User::get('id');

			// My Closed
			$queries['myopen']   = "state=0 AND created_by=" . User::get('id');
		}

		foreach ($queries as $key => $where)
		{
			$database->setQuery("SELECT count(*) FROM `#__answers_questions` WHERE $where");
			$this->$key = $database->loadResult();
		}

		// Get the view
		parent::display();
	}
}
