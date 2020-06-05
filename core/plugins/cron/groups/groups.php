<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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

		include_once dirname(dirname(__DIR__)) . DS . 'groups' . DS . 'announcements' . DS . 'announcements.php';

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
