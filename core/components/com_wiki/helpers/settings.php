<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Wiki\Helpers;

use Hubzero\Config\Registry;
use Hubzero\User\Group;
use Component;

/**
 * Wiki settings that a group can override
 */
class Settings
{
	/**
	 * Get a setting's effective value for a wiki
	 *
	 * A group wiki follows the group's Wiki Settings where the group
	 * has set a value; an empty value there, and every other wiki,
	 * follows the component's configuration.
	 *
	 * @param   string   $name      Setting name
	 * @param   mixed    $default   Value when neither the group nor the component has one
	 * @param   string   $scope     The wiki's scope (site, group, ...)
	 * @param   integer  $scope_id  The group ID for a group wiki
	 * @return  mixed
	 */
	public static function get($name, $default = null, $scope = null, $scope_id = 0)
	{
		if ($scope == 'group' && $scope_id)
		{
			$group = Group::getInstance($scope_id);

			if ($group)
			{
				$params = new Registry($group->get('params'));
				$value  = $params->get($name, '');

				// Registry::get() returns the default for an empty string too
				if ($value !== '' && $value !== null)
				{
					return $value;
				}
			}
		}

		return Component::params('com_wiki')->get($name, $default);
	}
}
