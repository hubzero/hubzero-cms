<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
