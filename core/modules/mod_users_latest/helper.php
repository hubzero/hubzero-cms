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

namespace Modules\UsersLatest;

use Hubzero\Module\Module;
use User;
use App;

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
	 * @param   object  $params  Registry
	 * @return  array
	 */
	public static function getUsers($params)
	{
		$db = App::get('db');

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
