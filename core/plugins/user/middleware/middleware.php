<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * User plugin for updating quotas and session limits
 */
class plgUserMiddleware extends \Hubzero\Plugin\Plugin
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
		$userId = \Hubzero\Utility\Arr::getValue($user, 'id', 0, 'int');

		if ($userId && $success)
		{
			try
			{
				$gids = User::getInstance($userId)
					->disableCaching()
					->purgeCache()
					->accessgroups()
					->rows()
					->fieldsByKey('group_id');
				$db = App::get('db');

				//
				// Quota class
				//

				require_once Component::path('com_members') . DS . 'models' . DS . 'quota.php';

				// Check for an existing quota record
				$row = Components\Members\Models\Quota::all()
					->whereEquals('user_id', $userId)
					->row();

				$row->set('user_id', $userId);

				// If (no quota record OR a record and a quota class [e.g., not custom]) ...
				if (!$row->get('id') || ($row->get('id') && $row->get('class_id')))
				{
					$val = array(
						'hard_files'  => $row->set('hard_files', 0),
						'soft_files'  => $row->set('soft_files', 0),
						'hard_blocks' => $row->set('hard_blocks', 0),
						'soft_blocks' => $row->set('soft_blocks', 0)
					);

					$db->setQuery("SELECT c.* FROM `#__users_quotas_classes` AS c LEFT JOIN `#__users_quotas_classes_groups` AS g ON g.`class_id`=c.`id` WHERE g.`group_id` IN (" . implode(',', $gids) . ")");
					$cids = $db->loadObjectList();
					if (count($cids) <= 0)
					{
						$db->setQuery("SELECT c.* FROM `#__users_quotas_classes` AS c WHERE c.`alias`=" . $db->quote('default'));
						$cids = $db->loadObjectList();
					}
					// Loop through each usergroup and find the highest quota values
					foreach ($cids as $cls)
					{
						$cls->hard_blocks = intval($cls->hard_blocks);
						$cls->soft_blocks = intval($cls->soft_blocks);

						if ($cls->hard_blocks > $val['hard_blocks']
						 && $cls->soft_blocks > $val['soft_blocks'])
						{
							$row->set('class_id', $cls->id);
						}
						//$val['hard_files']  = ($val['hard_files']  > $cls->hard_files  ?: $cls->hard_files);
						//$val['soft_files']  = ($val['soft_files']  > $cls->soft_files  ?: $cls->soft_files);
						$val['hard_blocks'] = ($val['hard_blocks'] > $cls->hard_blocks ? $val['hard_blocks'] : $cls->hard_blocks);
						$val['soft_blocks'] = ($val['soft_blocks'] > $cls->soft_blocks ? $val['soft_blocks'] : $cls->soft_blocks);
					}

					$row->set('hard_files', $val['hard_files']);
					$row->set('soft_files', $val['soft_files']);
					$row->set('hard_blocks', $val['hard_blocks']);
					$row->set('soft_blocks', $val['soft_blocks']);

					if (!$row->save())
					{
						throw new Exception($row->getError());
					}
				}

				//
				// Session limits
				//

				require_once Component::path('com_tools') . DS . 'tables' . DS . 'sessionclass.php';
				require_once Component::path('com_tools') . DS . 'tables' . DS . 'preferences.php';

				$row = new \Components\Tools\Tables\Preferences($db);

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
				Log::error($e->getMessage());
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

		$userId	= \Hubzero\Utility\Arr::getValue($user, 'id', 0, 'int');

		if ($userId)
		{
			try
			{
				$db = App::get('db');
				$db->setQuery("DELETE FROM `#__users_quotas` WHERE `user_id`=" . $userId);

				if (!$db->query())
				{
					throw new Exception($db->getErrorMsg());
				}

				$db->setQuery("DELETE FROM `#__users_tool_preferences` WHERE `user_id`=" . $userId);

				if (!$db->query())
				{
					throw new Exception($db->getErrorMsg());
				}
			}
			catch (Exception $e)
			{
				Log::error($e->getMessage());
				return false;
			}
		}

		return true;
	}
}
