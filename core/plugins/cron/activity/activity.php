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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

/**
 * Cron plugin for activity
 */
class plgCronActivity extends \Hubzero\Plugin\Plugin
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
				'name'   => 'emailMemberDigest',
				'label'  => Lang::txt('PLG_CRON_ACTIVITY_EMAIL_MEMBER_DIGEST'),
				'params' => 'emailmemberdigest'
			),
		);

		return $obj;
	}

	/**
	 * Email member activity digest
	 *
	 * Current limitations include a lack of queuing/scheduling. This means that this cron job
	 * should not be set to run more than once daily, otherwise it will continue to send out the
	 * same digest to people over and over again.
	 *
	 * @param   object  $job  \Components\Cron\Models\Job
	 * @return  bool
	 */
	public function emailMemberDigest(\Components\Cron\Models\Job $job)
	{
		// Make sure digests are enabled?  The cron job being on may be evidence enough...
		if (!Plugin::params('members', 'activity')->get('email_digests', false))
		{
			return true;
		}

		// Load language files
		Lang::load('plg_members_activity') ||
		Lang::load('plg_members_activity', PATH_CORE . DS . 'plugins' . DS . 'members' . DS . 'activity');

		// Database connection
		$db = App::get('db');

		// 0 = no email
		// 1 = immediately
		// 2 = digest daily
		// 3 = digest weekly
		// 4 = digest monthly
		$intervals = array(
			0 => 'none',
			1 => 'now',
			2 => 'day',
			3 => 'week',
			4 => 'month'
		);

		// Check frequency (this plugin should run early every morning)
		// If daily, run every day
		// If weekly, only run this one on mondays
		// If monthly, only run this on on the 1st of the month
		$isDay   = true;
		$isWeek  = (Date::of('now')->toLocal('N') == 1) ? true : false;
		$isMonth = (Date::of('now')->toLocal('j') == 1) ? true : false;

		foreach ($intervals as $val => $interval)
		{
			// Skip the first two options for now
			if ($val < 2)
			{
				continue;
			}

			if ($val == 3 && !$isWeek)
			{
				continue;
			}

			if ($val == 4 && !$isMonth)
			{
				continue;
			}

			// Find all users that want weekly digests and the last time the digest
			// was sent was NEVER or older than 1 month ago.
			$previous = Date::of('now')->subtract('1 ' . $interval)->toSql();

			// Set up the query
			$query = "SELECT DISTINCT(scope_id) FROM `#__activity_digests` WHERE `scope`=" . $db->quote('user') . " AND `frequency`=" . $db->quote($val) . " AND (`sent` = '0000-00-00 00:00:00' OR `sent` <= " . $db->quote($previous) . ")";
			$db->setQuery($query);
			$users = $db->loadColumn();

			// Loop through members and get their groups that have the digest set
			if ($users && count($users) > 0)
			{
				foreach ($users as $user)
				{
					$posts = \Hubzero\Activity\Recipient::all()
						->including('log')
						->whereEquals('scope', 'user')
						->whereEquals('scope_id', $user)
						->whereEquals('state', 1)
						->where('created', '>', $previous)
						->ordered()
						->rows();

					// Gather up applicable posts and queue up the emails
					if (count($posts) > 0)
					{
						if ($this->sendEmail($user, $posts, $interval))
						{
							// Update the last sent timestamp
							$query = "UPDATE `#__activity_digests` SET `sent`=" . $db->quote(Date::toSql()) . " WHERE `scope`=" . $db->quote('user') . " AND `scope_id`=" . $db->quote($user);
							$db->setQuery($query);
							$db->query();
						}
					}
				}
			}
		}

		return true;
	}

	/**
	 * Handles the actual sending of emails (or queuing them to be sent)
	 *
	 * @param   int     $user      the user id to send to
	 * @param   array   $posts     the posts to include in the email
	 * @param   string  $interval  the distribution interval
	 * @return  bool
	 **/
	private function sendEmail($user, $posts, $interval='day')
	{
		if (!is_dir(PATH_CORE . DS . 'plugins' . DS . 'members' . DS . 'activity'))
		{
			$this->setError('PLG_CRON_ACTIVITY_REQUIRED_PLUGIN_NOT_FOUND');
			return false;
		}

		$user = User::oneOrNew($user);

		if (!$user->get('id'))
		{
			$this->setError('PLG_CRON_ACTIVITY_USER_NOT_FOUND', $user->get('id'));
			return false;
		}

		$eview = new \Hubzero\Mail\View(array(
			'base_path' => PATH_CORE . DS . 'plugins' . DS . 'members' . DS . 'activity',
			'name'      => 'emails',
			'layout'    => 'digest_plain'
		));
		$eview->member   = $user;
		$eview->rows     = $posts;
		$eview->interval = $interval;

		$plain = $eview->loadTemplate();
		$plain = str_replace("\n", "\r\n", $plain);

		// HTML
		$eview->setLayout('digest_html');

		$html = $eview->loadTemplate();
		$html = str_replace("\n", "\r\n", $html);

		// Build message
		$message = App::get('mailer');
		$message->setSubject(Lang::txt('PLG_MEMBERS_ACTIVITY_EMAIL_SUBJECT'))
				->addFrom(Config::get('mailfrom'), Config::get('sitename'))
				->addTo($user->get('email'), $user->get('name'))
				->addHeader('X-Component', 'com_members')
				->addHeader('X-Component-Object', 'members_activity_email_digest');

		$message->addPart($plain, 'text/plain');
		$message->addPart($html, 'text/html');

		// Send mail
		if (!$message->send($this->params->get('email_transport_mechanism')))
		{
			$this->setError(Lang::txt('PLG_CRON_ACTIVITY_EMAIL_FAILED', $user->get('email')));
			return false;
		}

		return true;
	}
}