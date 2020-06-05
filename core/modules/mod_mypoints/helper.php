<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\MyPoints;

use Hubzero\Module\Module;
use Hubzero\Bank\Teller;
use Config;
use User;

/**
 * Module class for displaying point total and recent transactions
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
		$database = \App::get('db');

		$this->moduleclass = $this->params->get('moduleclass');
		$this->limit = intval($this->params->get('limit', 10));
		$this->error = false;

		// Check for the existence of required tables that should be
		// installed with the com_support component
		$tables = $database->getTableList();

		if ($tables && array_search(Config::get('dbprefix') . 'users_points', $tables) === false)
		{
			// Points table not found
			$this->error = true;
		}
		else
		{
			// Get the user's point summary and history
			$BTL = new Teller(User::get('id'));
			$this->summary = $BTL->summary();
			$this->history = $BTL->history($this->limit);

			// Push the module CSS to the template
			$this->css();
		}

		require $this->getLayoutPath();
	}
}
