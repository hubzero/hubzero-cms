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

namespace Modules\MyTickets;

use Hubzero\Module\Module;
use User;

/**
 * Module class for displaying a user's support tickets
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
		$limit = intval($this->params->get('limit', 10));

		// Find the user's most recent support tickets
		$database->setQuery(
			"(
				SELECT id, summary, category, open, status, severity, owner, created, login, name,
					(SELECT COUNT(*) FROM #__support_comments as sc WHERE sc.ticket=st.id AND sc.access=0) as comments
				FROM #__support_tickets as st
				WHERE st.login=" . $database->quote(User::get('username')) . " AND st.open=1 AND type=0
				ORDER BY created DESC
				LIMIT $limit
			)
			UNION
			(
				SELECT id, summary, category, open, status, severity, owner, created, login, name,
					(SELECT COUNT(*) FROM #__support_comments as sc WHERE sc.ticket=st.id AND sc.access=0) as comments
				FROM #__support_tickets as st
				WHERE st.owner=" . $database->quote(User::get('id')) . " AND st.open=1 AND type=0
				ORDER BY created DESC
				LIMIT $limit
			)"
		);
		$this->rows = $database->loadObjectList();
		if ($database->getErrorNum())
		{
			$this->setError($database->stderr());
			$this->rows = array();
		}

		$rows1 = array();
		$rows2 = array();

		if ($this->rows)
		{
			foreach ($this->rows as $row)
			{
				if ($row->owner == User::get('id'))
				{
					$rows2[] = $row;
				}
				else
				{
					$rows1[] = $row;
				}
			}
		}

		$this->rows1 = $rows1;
		$this->rows2 = $rows2;

		$xgroups = \Hubzero\User\Helper::getGroups(User::get('id'), 'members', 1);

		$groups = '';
		if ($xgroups)
		{
			$g = array();
			foreach ($xgroups as $xgroup)
			{
				$g[] = $database->quote($xgroup->cn);
			}
			$groups = implode(",", $g);
		}

		$this->rows3 = null;
		if ($groups)
		{
			// Find support tickets on the user's contributions
			$database->setQuery(
				"SELECT id, summary, category, open, status, severity, owner, created, login, name,
					(SELECT COUNT(*) FROM `#__support_comments` as sc WHERE sc.ticket=st.id AND sc.access=0) as comments
				FROM `#__support_tickets` as st
				WHERE st.open=1 AND type=0 AND st.group IN ($groups)
				ORDER BY created DESC
				LIMIT $limit"
			);
			$this->rows3 = $database->loadObjectList();
			if ($database->getErrorNum())
			{
				$this->setError($database->stderr());
				$this->rows3 = null;
			}
		}

		require $this->getLayoutPath();
	}
}
