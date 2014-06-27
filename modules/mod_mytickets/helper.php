<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 * All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Module class for displaying a user's support tickets
 */
class modMyTickets extends \Hubzero\Module\Module
{
	/**
	 * Display module content
	 *
	 * @return     void
	 */
	public function display()
	{
		$juser = JFactory::getUser();
		$database = JFactory::getDBO();

		$this->moduleclass = $this->params->get('moduleclass');
		$limit = intval($this->params->get('limit', 10));

		// Find the user's most recent support tickets
		$database->setQuery(
			"(
				SELECT id, summary, category, open, status, severity, owner, created, login, name,
					(SELECT COUNT(*) FROM #__support_comments as sc WHERE sc.ticket=st.id AND sc.access=0) as comments
				FROM #__support_tickets as st
				WHERE st.login='" . $juser->get('username') . "' AND st.open=1 AND type=0
				ORDER BY created DESC
				LIMIT $limit
			)
			UNION
			(
				SELECT id, summary, category, open, status, severity, owner, created, login, name,
					(SELECT COUNT(*) FROM #__support_comments as sc WHERE sc.ticket=st.id AND sc.access=0) as comments
				FROM #__support_tickets as st
				WHERE st.owner='" . $juser->get('id') . "' AND st.open=1 AND type=0
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
				if ($row->owner == $juser->get('id'))
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

		$profile = \Hubzero\User\Profile::getInstance($juser->get('id'));
		$xgroups = $profile->getGroups('members');

		$groups = '';
		if ($xgroups)
		{
			$g = array();
			foreach ($xgroups as $xgroup)
			{
				$g[] = $xgroup->cn;
			}
			$groups = implode("','", $g);
		}

		$this->rows3 = null;
		if ($groups)
		{
			// Find support tickets on the user's contributions
			$database->setQuery(
				"SELECT id, summary, category, open, status, severity, owner, created, login, name,
				 	(SELECT COUNT(*) FROM #__support_comments as sc WHERE sc.ticket=st.id AND sc.access=0) as comments
				 FROM #__support_tickets as st
				 WHERE st.open=1 AND type=0 AND st.group IN ('$groups')
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

		// Push the module CSS to the template
		$this->css();

		require(JModuleHelper::getLayoutPath($this->module->module));
	}
}
