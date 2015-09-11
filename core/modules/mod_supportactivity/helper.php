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
		if ($start = Request::getVar('start', ''))
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
