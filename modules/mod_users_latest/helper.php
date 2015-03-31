<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Modules\UsersLatest;

use Hubzero\Module\Module;
use JFactory;
use User;

/**
 * Module class for displaying latest registered users
 */
class Helper extends Module
{
	/**
	 * Display module
	 *
	 * @return  void
	 */
	public function display()
	{
		// [!] Legacy compatibility
		$params = $this->params;

		$shownumber = $params->get('shownumber', 5);
		$linknames  = $params->get('linknames', 0);
		$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

		$names = self::getUsers($params);

		require $this->getLayoutPath($params->get('layout', 'default'));
	}

	/**
	 * Get users sorted by activation date
	 *
	 * @param   object  $params  JRegistry
	 * @return  array
	 */
	static function getUsers($params)
	{
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);
		$query->select('a.id, a.name, a.username, a.registerDate');
		$query->order('a.registerDate DESC');
		$query->from('#__users AS a');

		if (!User::authorise('core.admin') && $params->get('filter_groups', 0) == 1)
		{
			$groups = $user->getAuthorisedGroups();
			if (empty($groups))
			{
				return array();
			}
			$query->leftJoin('#__user_usergroup_map AS m ON m.user_id = a.id');
			$query->leftJoin('#__usergroups AS ug ON ug.id = m.group_id');
			$query->where('ug.id in (' . implode(',', $groups) . ')');
			$query->where('ug.id <> 1');
		}
		$db->setQuery($query, 0, $params->get('shownumber'));
		$result = $db->loadObjectList();

		return (array) $result;
	}
}
