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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Cron plugin for support tickets
 */
class plgCronGroups extends \Hubzero\Plugin\Plugin
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
				'name'   => 'cleanGroupFolders',
				'label'  => Lang::txt('PLG_CRON_GROUPS_REMOVE_ABANDONED_ASSETS'),
				'params' => ''
			),
			array(
				'name'   => 'sendGroupAnnouncements',
				'label'  => Lang::txt('PLG_CRON_GROUPS_SEND_ANNOUNCEMENTS'),
				'params' => ''
			)
		);

		return $obj;
	}

	/**
	 * Remove unused group folders
	 *
	 * @param   object   $job  \Components\Cron\Models\Job
	 * @return  boolean
	 */
	public function cleanGroupFolders(\Components\Cron\Models\Job $job)
	{
		// get group params
		$groupParameters = Component::params('com_groups');

		// get group upload path
		$groupUploadPath = ltrim($groupParameters->get('uploadpath', '/site/groups'), DS);

		// get group folders
		$groupFolders = Filesystem::directories(PATH_APP . DS . $groupUploadPath);

		// loop through each group folder
		foreach ($groupFolders as $groupFolder)
		{
			// load group object for each folder
			$hubzeroGroup = \Hubzero\User\Group::getInstance(trim($groupFolder));

			// if we dont have a group object delete folder
			if (!is_object($hubzeroGroup))
			{
				// delete folder
				Filesystem::delete(PATH_APP . DS . $groupUploadPath . DS . $groupFolder);
			}
		}

		// job is no longer active
		return true;
	}


	/**
	 * Send scheduled group announcements
	 *
	 * @param   object   $job  \Components\Cron\Models\Job
	 * @return  boolean
	 */
	public function sendGroupAnnouncements(\Components\Cron\Models\Job $job)
	{
		// Get all announcements that are not yet sent but want to be mailed
		$announcements = \Hubzero\Item\Announcement::all()
			->whereEquals('email', 1)
			->whereEquals('sent', 0)
			->whereEquals('state', 1)
			->whereEquals('scope', 'group')
			->rows();

		include_once(dirname(dirname(__DIR__)) . DS . 'groups' . DS . 'announcements' . DS . 'announcements.php');

		// Loop through each announcement
		foreach ($announcements as $announcement)
		{
			// check to see if we can send
			if ($announcement->inPublishWindow())
			{
				// get all group members
				$group = \Hubzero\User\Group::getInstance($announcement->get('scope_id'));

				if (plgGroupsAnnouncements::send($announcement, $group))
				{
					// mark as sent
					$announcement->set('sent', 1);
					$announcement->save();
				}
			}
		}

		return true;
	}
}

