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
 * Module class for displaying a user's wishes
 */
class modMyWishes extends \Hubzero\Module\Module
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

		$limit = intval($this->params->get('limit', 10));

		// Find the user's most recent wishes
		$database->setQuery(
			"(
				SELECT id, wishlist, subject, about, proposed, status, accepted, assigned,
					(SELECT wl.title FROM #__wishlist as wl WHERE wl.id=w.wishlist) as listtitle
				FROM #__wishlist_item as w WHERE w.proposed_by='" . $juser->get('id') . "' AND (w.status=0 or w.status=3)
				ORDER BY proposed DESC
				LIMIT $limit
			)
			UNION
			(
				SELECT id, wishlist, subject, about, proposed, status, accepted, assigned,
					(SELECT wl.title FROM #__wishlist as wl WHERE wl.id=w.wishlist) as listtitle
				FROM #__wishlist_item as w WHERE w.assigned='" . $juser->get('id') . "' AND (w.status=0 or w.status=3)
				ORDER BY proposed DESC
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
				if ($row->assigned == $juser->get('id'))
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

		// Push the module CSS to the template
		$this->css();

		require(JModuleHelper::getLayoutPath($this->module->module));
	}
}
