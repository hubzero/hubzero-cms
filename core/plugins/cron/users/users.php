<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Cron plugin for user events
 */
class plgCronUsers extends \Hubzero\Plugin\Plugin
{
	/**
	 * Return a list of events
	 *
	 * @return  array
	 */
	public function onCronEvents()
	{
		$this->loadLanguage();

		$obj = new stdClass();
		$obj->plugin = $this->_name;
		$obj->events = array(
			array(
				'name'   => 'cleanAuthTempAccounts',
				'label'  => Lang::txt('PLG_CRON_USERS_REMOVE_TEMP_ACCOUNTS'),
				'params' => ''
			)
		);

		return $obj;
	}

	/**
	 * Remove user accounts with negative, numeric, usernames
	 *
	 * @param   object   $job  \Components\Cron\Models\Job
	 * @return  boolean
	 */
	public function cleanAuthTempAccounts(\Components\Cron\Models\Job $job)
	{
		$db = App::get('db');

		$query = "SELECT `id` FROM `#__users` WHERE `username` < 0;";
		$db->setQuery($query);
		$users = $db->loadObjectList();

		$yesterday = strtotime("yesterday");

		if ($users && count($users) > 0)
		{
			foreach ($users as $u)
			{
				$user = User::getInstance($u->id);

				if (is_object($user) && strtotime($user->get('lastvisitDate')) < $yesterday)
				{
					if (is_numeric($user->get('username')) && $user->get('username') < 0)
					{
						// Further check to make sure this was an abandoned auth_link account
						if (substr($user->get('email'), -8) == '@invalid')
						{
							// Delete the user
							$user->destroy();
						}
					}
				}
			}
		}
	}
}
