<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
		$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx',''));

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
