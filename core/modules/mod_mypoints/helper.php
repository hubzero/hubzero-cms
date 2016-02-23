<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 * All rights reserved.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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

