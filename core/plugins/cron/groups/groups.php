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
			),
			array(
				'name'   => 'expireGroupMemberships',
				'label'  => Lang::txt('PLG_CRON_GROUPS_EXPIRE_MEMBERSHIPS'),
				'params' => ''
			),
			array(
				'name'   => 'notifyExpiringMemberships',
				'label'  => Lang::txt('PLG_CRON_GROUPS_NOTIFY_EXPIRING_MEMBERSHIPS'),
				'params' => ''
			)
		);

		return $obj;
	}

	/**
	 * Revoke memberships whose term has lapsed
	 *
	 * This is what actually enforces a membership end date: the row is deleted
	 * so that every consumer of the membership table, including the many that
	 * read it with raw SQL, stops seeing the user without having to know that
	 * expiry exists.
	 *
	 * @param   object   $job  \Components\Cron\Models\Job
	 * @return  boolean
	 */
	public function expireGroupMemberships(\Components\Cron\Models\Job $job)
	{
		if (!\Hubzero\User\Group\Membership::supported())
		{
			return true;
		}

		$result = \Hubzero\User\Group\Membership::reap();

		// A stopped reaper is the difference between terms being enforced and
		// terms being decorative, so every run leaves a mark the admin panel
		// can show.
		\Hubzero\User\Group\Membership::heartbeat($result);

		return true;
	}

	/**
	 * Warn members whose term is approaching
	 *
	 * Thresholds come from the component's warning-days param; each one fires
	 * once per term, and extending a term restarts the cycle.
	 *
	 * @param   object   $job  \Components\Cron\Models\Job
	 * @return  boolean
	 */
	public function notifyExpiringMemberships(\Components\Cron\Models\Job $job)
	{
		$membership = '\Hubzero\User\Group\Membership';

		// Deliberately not gated on the hub's membership_expiration switch:
		// that governs whether managers can *set* an end date, not whether an
		// end date already set is honored. Switching it off must not leave
		// people being removed with no warning.
		if (!$membership::supported())
		{
			return true;
		}

		$this->loadLanguage();

		Lang::load('com_groups')
			|| Lang::load('com_groups', PATH_CORE . DS . 'components' . DS . 'com_groups' . DS . 'site');

		$from = Config::get('mailfrom');
		$site = Config::get('sitename');

		// Under cron there is no request to derive a host from, so the link
		// has to come from configuration. Without it, send a mail that reads
		// correctly rather than one containing a broken URL.
		$base = rtrim((string) Config::get('live_site'), '/');

		if (!$base)
		{
			// Request::root() answers "http://:/" under CLI, which is worse
			// than no link at all, so require a real host before using it.
			$root = rtrim((string) Request::root(), '/');
			$base = parse_url($root, PHP_URL_HOST) ? $root : '';
		}

		foreach ($membership::warningDays() as $days)
		{
			foreach ($membership::dueForWarning($days) as $row)
			{
				$group = \Hubzero\User\Group::getInstance($row->gidNumber);
				$user  = \Hubzero\User\User::oneOrNew($row->uidNumber);

				if (!is_object($group) || !$user->get('id') || !$user->get('email'))
				{
					// Nothing we can do with this one; stamp it so a broken
					// row cannot make the job spin every night
					$membership::markNotified($row->gidNumber, $row->uidNumber);
					continue;
				}

				$when = Date::of($row->expires)->toLocal(Lang::txt('DATE_FORMAT_HZ1'));

				$subject = Lang::txt('PLG_CRON_GROUPS_EXPIRING_SUBJECT', $group->get('description'), $when);

				$body = $base
					? Lang::txt(
						'PLG_CRON_GROUPS_EXPIRING_BODY',
						$user->get('name'),
						$group->get('description'),
						$when,
						$base . '/groups/' . $group->get('cn'),
						$site
					)
					: Lang::txt(
						'PLG_CRON_GROUPS_EXPIRING_BODY_NOLINK',
						$user->get('name'),
						$group->get('description'),
						$when,
						$site
					);

				try
				{
					$message = new \Hubzero\Mail\Message();
					$message->setSubject($subject)
							->addFrom($from, $site)
							->addTo($user->get('email'), $user->get('name'))
							->addPart($body, 'text/plain')
							->send();
				}
				catch (\Exception $e)
				{
					// A mail failure must not stall the queue or re-warn
					// everyone tomorrow; the stamp below still applies
				}

				$membership::markNotified($row->gidNumber, $row->uidNumber);
			}
		}

		return true;
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
