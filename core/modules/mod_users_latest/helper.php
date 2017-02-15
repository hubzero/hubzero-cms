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

		$query = $db->getQuery()
			->select('a.id')
			->select('a.name')
			->select('a.username')
			->select('a.registerDate')
			->order('a.registerDate', 'desc')
			->from('#__users', 'a');

		if (!User::authorise('core.admin') && $params->get('filter_groups', 0) == 1)
		{
			$groups = User::getAuthorisedGroups();
			if (empty($groups))
			{
				return array();
			}
			$query
				->join('#__user_usergroup_map AS m', 'm.user_id', 'a.id', 'left')
				->join('#__usergroups AS ug', 'ug.id', 'm.group_id', 'left')
				->whereIn('ug.id', $groups)
				->where('ug.id', '<>', '1');
		}
		$query
			->limit($params->get('shownumber'))
			->start(0);
		$db->setQuery($query->toString());
		$result = $db->loadObjectList();

		return (array) $result;
	}
}
