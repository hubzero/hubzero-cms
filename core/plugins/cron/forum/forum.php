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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

/**
 * Cron plugin for forum
 */
class plgCronForum extends \Hubzero\Plugin\Plugin
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
				'name'   => 'emailGroupForumDigest',
				'label'  => Lang::txt('PLG_CRON_FORUM_EMAIL_GROUP_DIGEST'),
				'params' => 'emailgroupdigest'
			),
		);

		return $obj;
	}

	/**
	 * Email group forum digest
	 *
	 * Current limitations include a lack of queuing/scheduling. This means that this cron job
	 * should not be set to run more than once daily, otherwise it will continue to send out the
	 * same digest to people over and over again.
	 *
	 * @param   object  $job  \Components\Cron\Models\Job
	 * @return  bool
	 */
	public function emailGroupForumDigest(\Components\Cron\Models\Job $job)
	{
		// Require posts file
		require_once PATH_CORE . DS . 'components' . DS . 'com_forum' . DS . 'tables' . DS . 'post.php';
		require_once PATH_CORE . DS . 'components' . DS . 'com_forum' . DS . 'models' . DS . 'post.php';
		require_once PATH_CORE . DS . 'components' . DS . 'com_forum' . DS . 'models' . DS . 'category.php';
		require_once PATH_CORE . DS . 'components' . DS . 'com_forum' . DS . 'models' . DS . 'section.php';

		// Load language files
		Lang::load('plg_groups_forum') ||
		Lang::load('plg_groups_forum', PATH_CORE . DS . 'plugins' . DS . 'groups' . DS . 'forum');

		// Make sure digests are enabled?  The cron job being on may be evidence enough...
		if (!Component::params('com_groups')->get('enable_forum_email_digest', false))
		{
			return true;
		}

		// Database connection
		// We clone the db here so that we can use our prepared statement below without interruption
		$db = clone(App::get('db'));

		// Get all site members who have the digest option set for at least one group
		// Currently stored in jos_xgroups_memberoption - this will transition to a central
		// location when we implement the large messaging/digest functionality
		// 0 = no email
		// 1 = immediately
		// 2 = digest daily
		// 3 = digest weekly
		// 4 = digest monthly
		$query = "SELECT DISTINCT(userid) FROM `#__xgroups_memberoption` WHERE `optionname` = 'receive-forum-email' and `optionvalue` > 1";
		$db->setQuery($query);
		$users = $db->loadColumn();

		// Check frequency (this plugin should run early every morning)
		// If daily, include all daily posts from previous day
		// If weekly, include all weekly posts from past week (only run this one on mondays)
		// If monthly, include all monthly posts from past month (only run this on on the 1st of the month)
		$isDay   = true;
		$isWeek  = (Date::of('now')->toLocal('N') == 1) ? true : false;
		$isMonth = (Date::of('now')->toLocal('j') == 1) ? true : false;

		// Loop through members and get their groups that have the digest set
		if ($users && count($users) > 0)
		{
			$query = "SELECT `gidNumber` FROM `#__xgroups_memberoption` WHERE `userid` = ? AND `optionname` = 'receive-forum-email' AND `optionvalue` = ?";
			$db->prepare($query);

			foreach ($users as $user)
			{
				// It's possible to receive up to three emails if the 1st were a monday and you had groups set to all three options
				// Or should we combine and overlapping emails when they fall on the same day?
				if ($isDay)
				{
					$groups = $db->bind([$user, 2])->loadColumn();
					$posts  = $this->getPosts($groups);

					// Gather up applicable posts and queue up the emails
					if (count($posts) > 0)
					{
						$this->sendEmail($user, $posts);
					}
				}

				if ($isWeek)
				{
					$groups = $db->bind([$user, 3])->loadColumn();
					$posts  = $this->getPosts($groups, 'week');

					// Gather up applicable posts and queue up the emails
					if (count($posts) > 0)
					{
						$this->sendEmail($user, $posts, 'weekly');
					}
				}

				if ($isMonth)
				{
					$groups = $db->bind([$user, 4])->loadColumn();
					$posts  = $this->getPosts($groups, 'month');

					// Gather up applicable posts and queue up the emails
					if (count($posts) > 0)
					{
						$this->sendEmail($user, $posts, 'monthly');
					}
				}
			}
		}

		return true;
	}

	/**
	 * Grabs the post for a given set of groups over a certain period of time
	 *
	 * @param  array  $groups   the group ids to look up
	 * @param  string $interval the length of time to go back to look for posts
	 * @return array
	 **/
	private function getPosts($groups, $interval='day')
	{
		$return = [];

		if ($groups && count($groups) > 0)
		{
			foreach ($groups as $group)
			{
				$db      = App::get('db');
				$posts   = new \Components\Forum\Tables\Post($db);
				$filters = [
					'scope'    => 'group',
					'scope_id' => $group,
					'state'    => 1,
					'sort'     => 'created',
					'start_at' => Date::of(strtotime("now -1 {$interval}"))->toSql(),
					'sort_Dir' => 'DESC',
					'limit'    => 10
				];

				$results = $posts->find($filters);

				if (count($results) > 0)
				{
					$return[$group] = $results;
				}
			}
		}

		return $return;
	}

	/**
	 * Handles the actual sending of emails (or queuing them to be sent)
	 *
	 * @param  int    $user     the user id to send to
	 * @param  array  $posts    the posts to include in the email
	 * @param  string $interval the distribution interval
	 * @return bool
	 **/
	private function sendEmail($user, $posts, $interval='daily')
	{
		$eview = new \Hubzero\Component\View(array(
			'base_path' => PATH_CORE . DS . 'components' . DS . 'com_forum' . DS . 'site',
			'name'      => 'emails',
			'layout'    => 'digest_plain'
		));
		$eview->option    = 'com_forum';
		$eview->delimiter = '~!~!~!~!~!~!~!~!~!~!';
		$eview->posts     = $posts;
		$eview->interval  = $interval;

		$plain = $eview->loadTemplate();
		$plain = str_replace("\n", "\r\n", $plain);

		// HTML
		$eview->setLayout('digest_html');

		$html = $eview->loadTemplate();
		$html = str_replace("\n", "\r\n", $html);

		$user = User::getInstance($user);

		// Build message
		$message = App::get('mailer');
		$message->setSubject(Lang::txt('PLG_GROUPS_FORUM') . ': ' . Lang::txt('PLG_GROUPS_FORUM_SUBJECT_EMAIL_DIGEST'))
				->addFrom(Config::get('mailfrom'), Config::get('sitename'))
				->addTo($user->get('email'), $user->get('name'))
				->addHeader('X-Component', 'com_forum')
				->addHeader('X-Component-Object', 'groups_forum_email_digest');

		$message->addPart($plain, 'text/plain');
		$message->addPart($html, 'text/html');

		// Send mail
		if (!$message->send($this->params->get('email_transport_mechanism')))
		{
			$this->setError('Failed to mail %s', $user->get('email'));
		}

		$mailed[] = $user->get('username');
	}
}