<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
			case '0': $this->type = 'system'; break;
			case '1': $this->type = 'hub'; break;
			case '2': $this->type = 'project'; break;
			case '3': $this->type = 'partner'; break;
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
		$lastDay = with(new Date('now'))->subtract('1 Day')->toSql(); //gmdate('Y-m-d', (time() - 24*3600)) . ' 00:00:00';

		$database->setQuery("SELECT count(*) FROM `#__xgroups` WHERE created >= '$lastDay' AND type='$type'");
		$this->pastDay = $database->loadResult();

		// Get the view
		parent::display();
	}
}
