<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

/**
 * Cron plugin for user events
 */
class plgCronUsers extends JPlugin
{
	/**
	 * Return a list of events
	 * 
	 * @return     array
	 */
	public function onCronEvents()
	{
		$obj = new stdClass();
		$obj->plugin = $this->_name;
		$obj->events = array(
			array(
				'name'   => 'cleanAuthTempAccounts',
				'label'  => JText::_('Remove temporary acounts that have been created and abandoned during the third party authentication process.'),
				'params' => ''
			)
		);

		return $obj;
	}

	/**
	 * Remove user accounts with negative, numeric, usernames
	 * 
	 * @return     array
	 */
	public function cleanAuthTempAccounts($params=null)
	{
		$db = JFactory::getDBO();

		$query = "SELECT `id` FROM `#__users` WHERE `username` < 0;";
		$db->setQuery($query);
		$users = $db->loadObjectList();

		$yesterday = strtotime("yesterday");

		if ($users && count($users) > 0)
		{
			foreach ($users as $u)
			{
				$juser =& JFactory::getUser($u->id);

				if (is_object($juser) && strtotime($juser->get('lastvisitDate')) < $yesterday)
				{
					if (is_numeric($juser->get('username')) && $juser->get('username') < 0)
					{
						// Further check to make sure this was an abandoned auth_link account
						if (substr($juser->get('email'), -8) == '@invalid')
						{
							// Delete the user
							$juser->delete();
						}
					}
				}
			}
		}
	}
}