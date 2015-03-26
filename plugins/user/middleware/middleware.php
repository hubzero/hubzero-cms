<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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

defined('JPATH_BASE') or die;

/**
 * User plugin for updating quotas and session limits
 */
class plgUserMiddleware extends JPlugin
{
	/**
	 * Utility method to act on a user after it has been saved.
	 *
	 * @param   array    $user     Holds the new user data.
	 * @param   boolean  $isnew    True if a new user is stored.
	 * @param   boolean  $success  True if user was succesfully stored in the database.
	 * @param   string   $msg      Message.
	 * @return  void
	 */
	public function onUserAfterSave($user, $isnew, $success, $msg)
	{
		$userId = JArrayHelper::getValue($user, 'id', 0, 'int');

		if ($userId && $success)
		{
			try
			{
				$gids = JUserHelper::getUserGroups($userId);
				$db = JFactory::getDbo();

				//
				// Quota class
				//

				require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_members' . DS . 'tables' . DS . 'quotas_classes.php');
				require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_members' . DS . 'tables' . DS . 'users_quotas.php');

				$row = new UsersQuotas($db);

				// Check for an existing quota record
				$db->setQuery("SELECT * FROM `#__users_quotas` WHERE `user_id`=" . $userId);
				if ($quota = $db->loadObject())
				{
					$row->bind($quota);
				}
				else
				{
					$row->user_id = $userId;
				}

				// If (no quota record OR a record and a quota class [e.g., not custom]) ...
				if (!$row->id || ($row->id && $row->class_id))
				{
					$val = array(
						'hard_files'  => 0,
						'soft_files'  => 0,
						'hard_blocks' => 0,
						'soft_blocks' => 0
					);

					$db->setQuery("SELECT c.* FROM `#__users_quotas_classes` AS c LEFT JOIN `#__users_quotas_classes_groups` AS g ON g.`class_id`=c.`id` WHERE g.`group_id` IN (" . implode(',', $gids) . ")");
					$cids = $db->loadObjectList();
					if (count($cids) <= 0)
					{
						$db->setQuery("SELECT c.* FROM `#__users_quotas_classes` AS c WHERE c.`alias`=" . $db->quote('default'));
						$cids = $db->loadObjectList();
					}
					// Loop through each usergroup and find the highest quota values
					foreach ($cids as $cls);
					{
						$cls->hard_blocks = intval($cls->hard_blocks);
						$cls->soft_blocks = intval($cls->soft_blocks);

						if ($cls->hard_blocks > $val['hard_blocks']
						 && $cls->soft_blocks > $val['soft_blocks'])
						{
							$row->class_id = $cls->id;
						}
						//$val['hard_files']  = ($val['hard_files']  > $cls->hard_files  ?: $cls->hard_files);
						//$val['soft_files']  = ($val['soft_files']  > $cls->soft_files  ?: $cls->soft_files);
						$val['hard_blocks'] = ($val['hard_blocks'] > $cls->hard_blocks ? $val['hard_blocks'] : $cls->hard_blocks);
						$val['soft_blocks'] = ($val['soft_blocks'] > $cls->soft_blocks ? $val['soft_blocks'] : $cls->soft_blocks);
					}

					$row->hard_files  = $val['hard_files'];
					$row->soft_files  = $val['soft_files'];
					$row->hard_blocks = $val['hard_blocks'];
					$row->soft_blocks = $val['soft_blocks'];

					if (!$row->check())
					{
						throw new Exception($row->getError());
					}

					if (!$row->store())
					{
						throw new Exception($row->getError());
					}
				}

				//
				// Session limits
				//

				require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_tools' . DS . 'tables' . DS . 'sessionclass.php');
				require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_tools' . DS . 'tables' . DS . 'preferences.php');

				$row = new ToolsTablePreferences($db);

				// Check for an existing quota record
				$db->setQuery("SELECT * FROM `#__users_tool_preferences` WHERE `user_id`=" . $userId);
				if ($quota = $db->loadObject())
				{
					$row->bind($quota);
				}
				else
				{
					$row->user_id = $userId;
				}

				// If (no quota record OR a record and a quota class [e.g., not custom]) ...
				if (!$row->id || ($row->id && $row->class_id))
				{
					$val = array(
						'jobs'  => 0
					);

					$db->setQuery("SELECT c.* FROM `#__tool_session_classes` AS c LEFT JOIN `#__tool_session_class_groups` AS g ON g.`class_id`=c.`id` WHERE g.`group_id` IN (" . implode(',', $gids) . ")");
					$cids = $db->loadObjectList();
					if (count($cids) <= 0)
					{
						$db->setQuery("SELECT c.* FROM `#__tool_session_classes` AS c WHERE c.`alias`=" . $db->quote('default'));
						$cids = $db->loadObjectList();
					}
					// Loop through each usergroup and find the highest 'jobs allowed' value
					foreach ($cids as $cls);
					{
						$cls->jobs = intval($cls->jobs);

						if ($cls->jobs > $val['jobs'])
						{
							$row->class_id = $cls->id;
						}

						$val['jobs'] = ($val['jobs'] > $cls->jobs ? $val['jobs'] : $cls->jobs);
					}

					$row->jobs  = $val['jobs'];

					if (!$row->check())
					{
						throw new Exception($row->getError());
					}

					if (!$row->store())
					{
						throw new Exception($row->getError());
					}
				}
			}
			catch (Exception $e)
			{
				$this->_subject->setError($e->getMessage());
				return false;
			}
		}

		return true;
	}

	/**
	 * Remove all user quota information for the given user ID
	 *
	 * Method is called after user data is deleted from the database
	 *
	 * @param   array    $user     Holds the user data
	 * @param   boolean  $success  True if user was succesfully stored in the database
	 * @param   string   $msg      Message
	 * @return  boolean
	 */
	public function onUserAfterDelete($user, $success, $msg)
	{
		if (!$success)
		{
			return false;
		}

		$userId	= JArrayHelper::getValue($user, 'id', 0, 'int');

		if ($userId)
		{
			try
			{
				$db = JFactory::getDbo();
				$db->setQuery("DELETE FROM `#__users_quotas` WHERE `user_id`=" . $userId);

				if (!$db->query())
				{
					throw new Exception($db->getErrorMsg());
				}

				$db = JFactory::getDbo();
				$db->setQuery("DELETE FROM `#__users_tool_preferences` WHERE `user_id`=" . $userId);

				if (!$db->query())
				{
					throw new Exception($db->getErrorMsg());
				}
			}
			catch (Exception $e)
			{
				$this->_subject->setError($e->getMessage());
				return false;
			}
		}

		return true;
	}
}
