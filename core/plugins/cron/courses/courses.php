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
 * Cron plugin for courses
 */
class plgCronCourses extends \Hubzero\Plugin\Plugin
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
				'name'   => 'syncPassportBadgeStatus',
				'label'  => Lang::txt('PLG_CRON_COURSES_SYNC_PASSPORT_BADGE_STATUS'),
				'params' => ''
			),
			array(
				'name'   => 'emailInstructorDigest',
				'label'  => Lang::txt('PLG_CRON_COURSES_EMAIL_INSTRUCTOR_DIGEST'),
				'params' => 'emaildigest'
			),
		);

		return $obj;
	}

	/**
	 * Sync claimed/denied passport badges
	 *
	 * @param   object   $job  \Components\Cron\Models\Job
	 * @return  boolean
	 */
	public function syncPassportBadgeStatus(\Components\Cron\Models\Job $job)
	{
		$params = Component::params('com_courses');

		$badgesHandler  = new Hubzero\Badges\Wallet('passport', $params->get('badges_request_type'));
		$badgesProvider = $badgesHandler->getProvider();

		$creds = new \stdClass();
		$creds->consumer_key    = $params->get('passport_consumer_key');
		$creds->consumer_secret = $params->get('passport_consumer_secret');

		$badgesProvider->setCredentials($creds);

		require_once PATH_CORE . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'courses.php';
		require_once PATH_CORE . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'memberBadge.php';
		$coursesObj = new \Components\Courses\Models\Courses();
		$courses    = $coursesObj->courses();

		if (isset($courses) && count($courses) > 0)
		{
			foreach ($courses as $course)
			{
				if (!$course->isAvailable())
				{
					continue;
				}

				$students = $course->students();
				$emails   = array();

				if ($students && count($students) > 0)
				{
					foreach ($students as $student)
					{
						$emails[] = User::getInstance($student->get('user_id'))->get('email');
					}
				}

				if (count($emails) > 0)
				{
					$assertions = $badgesProvider->getAssertionsByEmailAddress($emails);

					if (isset($assertions) && count($assertions) > 0)
					{
						foreach ($assertions as $assertion)
						{
							$status = false;
							if ($assertion->IsPending)
							{
								$status = false;
							}
							else if ($assertion->IsAccepted)
							{
								$status = 'accept';
							}
							else
							{
								$status = 'deny';
							}

							if ($status)
							{
								preg_match('/validation\/([[:alnum:]-]{20})/', $assertion->EvidenceUrl, $match);

								if (isset($match[1]))
								{
									$badge = \Components\Courses\Models\MemberBadge::loadByToken($match[1]);

									if ($badge && !$badge->get('action'))
									{
										$badge->set('action', $status);
										$badge->set('action_on', Date::toSql());
										$badge->store();
									}
								}
							}
						}
					}
				}
			}
		}

		// Job is no longer active
		return true;
	}

	/**
	 * Email instructor course digest
	 *
	 * @param   object   $job  \Components\Cron\Models\Job
	 * @return  boolean
	 */
	public function emailInstructorDigest(\Components\Cron\Models\Job $job)
	{
		$database = \App::get('db');
		$cconfig  = Component::params('com_courses');

		Lang::load('com_courses') ||
		Lang::load('com_courses', PATH_CORE . DS . 'components' . DS . 'com_courses' . DS . 'site');

		$from = array(
			'name'  => Config::get('sitename') . ' ' . Lang::txt('COM_COURSES'),
			'email' => Config::get('mailfrom')
		);

		$subject = Lang::txt('COM_COURSES') . ': ' . Lang::txt('COM_COURSES_SUBJECT_EMAIL_DIGEST');

		require_once PATH_CORE . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'courses.php';

		$course_id = 0;

		$params = $job->params;
		if (isset($params) && is_object($params))
		{
			$course_id = $params->get('course');
		}

		$coursesObj = new \Components\Courses\Models\Courses();

		if ($course_id)
		{
			$courses = array($coursesObj->course($course_id));
		}
		else
		{
			$courses = $coursesObj->courses();
		}

		if (isset($courses) && count($courses) > 0)
		{
			foreach ($courses as $course)
			{
				if (!$course->isAvailable())
				{
					continue;
				}

				$mailed      = array();
				$managers    = $course->managers();
				$enrollments = $course->students(array('count'=>true));
				$offerings   = $course->offerings();

				if (isset($offerings) && count($offerings) > 0)
				{
					foreach ($offerings as $offering)
					{
						if (!$offering->isAvailable())
						{
							continue;
						}

						$offering->gradebook()->refresh();
						$passing = $offering->gradebook()->countPassing(false);
						$failing = $offering->gradebook()->countFailing(false);

						if (isset($managers) && count($managers) > 0)
						{
							require_once PATH_CORE . DS . 'components' . DS . 'com_forum' . DS . 'models' . DS . 'manager.php';

							foreach ($managers as $manager)
							{
								// Get the user's account
								$user = User::getInstance($manager->get('user_id'));
								if (!$user->get('id'))
								{
									continue;
								}

								// Try to ensure no duplicates
								if (in_array($user->get('username'), $mailed))
								{
									continue;
								}

								// Only mail instructors (i.e. not managers)
								if ($manager->get('role_alias') != 'instructor')
								{
									continue;
								}

								// Get discussion stats and posts
								$posts = \Components\Forum\Models\Post::all()
									->whereEquals('scope', 'course')
									->whereEquals('scope_id', $offering->get('id'))
									->whereEquals('state', \Components\Forum\Models\Post::STATE_PUBLISHED)
									->order('created', 'desc')
									->limit(100)
									->rows();
								$posts_cnt  = $posts->count();
								$latest     = array();
								$latest_cnt = 0;

								if (isset($posts) && $posts_cnt > 0)
								{
									foreach ($posts as $post)
									{
										if (strtotime($post->created) > strtotime('-1 day'))
										{
											$latest[] = $post;
										}
										else
										{
											break;
										}
									}

									$latest_cnt = count($latest);
								}

								$eview = new \Hubzero\Component\View(array(
									'base_path' => PATH_CORE . DS . 'components' . DS . 'com_courses' . DS . 'site',
									'name'      => 'emails',
									'layout'    => 'digest_plain'
								));
								$eview->option      = 'com_courses';
								$eview->controller  = 'courses';
								$eview->delimiter   = '~!~!~!~!~!~!~!~!~!~!';
								$eview->course      = $course;
								$eview->enrollments = $enrollments;
								$eview->passing     = $passing;
								$eview->failing     = $failing;
								$eview->offering    = $offering;
								$eview->posts_cnt   = $posts_cnt;
								$eview->latest      = $latest;
								$eview->latest_cnt  = $latest_cnt;

								$plain = $eview->loadTemplate();
								$plain = str_replace("\n", "\r\n", $plain);

								// HTML
								$eview->setLayout('digest_html');

								$html = $eview->loadTemplate();
								$html = str_replace("\n", "\r\n", $html);

								// Build message
								$message = new \Hubzero\Mail\Message();
								$message->setSubject($subject)
										->addFrom($from['email'], $from['name'])
										->addTo($user->get('email'), $user->get('name'))
										->addHeader('X-Component', 'com_courses')
										->addHeader('X-Component-Object', 'courses_instructor_digest');

								$message->addPart($plain, 'text/plain');
								$message->addPart($html, 'text/html');

								// Send mail
								if (!$message->send())
								{
									$this->setError('Failed to mail %s', $user->get('email'));
								}

								$mailed[] = $user->get('username');
							}
						}
					}
				}
			}
		}

		return true;
	}
}