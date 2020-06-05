<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Let project members/public subscribe to project activity notifications
 */
class plgProjectsWatch extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Event call to determine if this plugin should return data
	 *
	 * @param   string  $alias
	 * @return  array   Plugin name and title
	 */
	public function &onProjectAreas($alias = null)
	{
		$area = array(
			'name'    => 'watch',
			'title'   => 'Watch',
			'submenu' => null,
			'show'    => false
		);

		return $area;
	}

	/**
	 * Event call to return data for a specific project
	 *
	 * @param   object  $model   Project model
	 * @param   string  $action  Plugin task
	 * @param   string  $areas   Plugins to return data
	 * @return  array   Return array of html
	 */
	public function onProject($model, $action = '', $areas = null)
	{
		// Get this area details
		$this->_area = $this->onProjectAreas();

		// Check if our area is in the array of areas we want to return results for
		if (is_array( $areas ))
		{
			if (empty($this->_area) || !in_array($this->_area['name'], $areas))
			{
				return;
			}
		}

		return $this->onProjectMember($model);
	}

	/**
	 * Return data on a project team member
	 *
	 * @param   object  $project  Current publication
	 * @return  mixed
	 */
	public function onProjectMember($project)
	{
		// Only show for logged-in users
		if (User::isGuest())
		{
			return false;
		}

		// Only show to members
		if (!$project->access('member'))
		{
			return false;
		}

		$this->database = App::get('db');
		$this->project  = $project;

		// Item watch class
		$this->watch    = new Hubzero\Item\Watch($this->database);
		$this->action   = strtolower(Request::getWord('action', ''));

		switch ($this->action)
		{
			case 'save':
				return $this->_save();
			break;

			case 'manage':
				return array('html' => $this->_manage());
			break;

			default:
				return $this->_status();
			break;
		}
	}

	/**
	 * Show subscription status
	 *
	 * @return  string  HTML
	 */
	private function _status()
	{
		// Instantiate a view
		$view = $this->view('default', 'index')
			->set('project', $this->project)
			->set('watched', Hubzero\Item\Watch::isWatching(
				$this->project->get('id'),
				'project',
				User::get('id')
			));

		// Return the output
		return $view->loadTemplate();
	}

	/**
	 * Show manage subscription screen
	 *
	 * @return  string  HTML
	 */
	private function _manage()
	{
		// Is user watching item?
		$watch = Hubzero\Item\Watch::oneByScope(
			$this->project->get('id'),
			'project',
			User::get('id')
		);

		$params = new Hubzero\Config\Registry($watch->get('params', ''));

		$dflt = 0;
		if ($this->params->get('autosubscribe'))
		{
			$dflt = 1;
		}

		$cats = array(
			'blog'         => $params->get('blog', $dflt),
			'team'         => $params->get('team', $dflt),
			'files'        => $params->get('files', $dflt),
			'publications' => $params->get('publications', $dflt),
			'todo'         => $params->get('todo', $dflt),
			'notes'        => $params->get('notes', $dflt)
		);

		// Instantiate a view
		$view = $this->view('default', 'manage')
			->set('project', $this->project)
			->set('watch', $watch)
			->set('cats', $cats);

		// Return the output
		return $view->loadTemplate();
	}

	/**
	 * Subscribe
	 *
	 * @return  void
	 */
	private function _save()
	{
		// Check for request forgeries
		Request::checkToken();

		// Login required
		if (User::isGuest() || !$this->project->exists())
		{
			App::redirect(
				Route::url($this->project->link())
			);
		}

		// Incoming
		$email      = User::get('email');
		$categories = Request::getArray('category', array());
		$frequency  = Request::getWord('frequency', 'immediate');

		// Save subscription
		$watch = Hubzero\Item\Watch::oneByScope(
			$this->project->get('id'),
			'project',
			User::get('id'),
			$email
		);

		$watch->set('item_id', $this->project->get('id'));
		$watch->set('item_type', 'project');
		$watch->set('created_by', User::get('id'));
		$watch->set('state', empty($categories) ? 2 : 1);

		$cats = array(
			'blog'         => 0,
			'quote'        => 0,
			'team'         => 0,
			'files'        => 0,
			'publications' => 0,
			'todo'         => 0,
			'notes'        => 0
		);

		$params = new Hubzero\Config\Registry($watch->get('params', ''));

		$params->set('frequency', $frequency);

		foreach ($cats as $param => $value)
		{
			if (isset($categories[$param]))
			{
				$value = intval($categories[$param]);
			}
			if ($param == 'quote' && isset($categories['blog']))
			{
				$value = 1;
			}

			$params->set($param, $value);
		}

		$watch->set('params', $params->toString());
		$watch->save();

		if ($err = $watch->getError())
		{
			Notify::error($err);
		}
		else
		{
			Notify::message(Lang::txt('PLG_PROJECTS_WATCH_SUCCESS_SAVED'), 'success', 'projects');
		}

		App::redirect(
			Route::url($this->project->link())
		);
	}

	/**
	 * Notify subscribers of new activity
	 *
	 * @param   object   $project     Project model
	 * @param   string   $area        Project area of activity
	 * @param   array    $activities  Project activities (array of IDs)
	 * @param   integer  $actor       Uid of team member posting the activity (to exclude from subscribers)
	 * @return  array
	 */
	public function onWatch($project, $area = '', $activities = array(), $actor = 0)
	{
		$this->project = $project;

		// Get full activity info from IDs
		if ($activities)
		{
			$recipient = Hubzero\Activity\Recipient::all();

			$r = $recipient->getTableName();
			$l = Hubzero\Activity\Log::blank()->getTableName();

			$recipient
				->select($r . '.*')
				->including('log')
				->join($l, $l . '.id', $r . '.log_id')
				->whereIn($r . '.scope', array('project'))
				->whereEquals($r . '.scope_id', $project->get('id'))
				->whereEquals($r . '.state', Hubzero\Activity\Recipient::STATE_PUBLISHED);
				//->whereEquals($l . '.parent', 0);

			if (!empty($activities))
			{
				$recipient->whereIn($l . '.id', $activities);
			}
			$activities = $recipient->rows();
		}

		if (count($activities) <= 0)
		{
			// Nothing to report
			return false;
		}

		// Get subscribers
		$watchers = Hubzero\Item\Watch::all()
			->whereEquals('item_type', 'project')
			->whereEquals('item_id', $project->get('id'));

		if (!$this->params->get('autosubscribe'))
		{
			$watchers
				->whereEquals('state', 1)
				->whereLike('params', '"' . $area . '":1')
				->whereLike('params', '"frequency":"immediate"');
		}

		$subscribers = $watchers->rows();

		// Is auto-subscribed turned on?
		if ($this->params->get('autosubscribe'))
		{
			// Get the entire team
			$team = $project->table('Owner')->getIds($project->get('id'), 'all', 1);

			// Filter out people who have opted-out
			foreach ($subscribers as $subscriber)
			{
				if (in_array($subscriber->get('created_by'), $team))
				{
					$params = new Hubzero\Config\Registry($subscriber->get('params'));

					// Do they meet the requirements for being messaged?
					if ($params->get('frequency') != 'immediate' || !$params->get($area))
					{
						// Unset the user from the team list
						$key = array_search($subscriber->get('created_by'), $team);
						unset($team[$key]);
					}
				}
			}

			// Rebuild the subscriber list from the team
			$subscribers = new Hubzero\Database\Rows();
			foreach ($team as $t)
			{
				$subscriber = new Hubzero\Item\Watch();
				$subscriber->set('created_by', $t);

				$subscribers->push($subscriber);
			}
		}

		$subject = Lang::txt('PLG_PROJECTS_WATCH_EMAIL_SUBJECT');

		// Do we have subscribers?
		if ($subscribers->count() > 0)
		{
			$processed = array();

			foreach ($subscribers as $subscriber)
			{
				if ($actor && $subscriber->created_by == $actor)
				{
					// Skip
					continue;
				}

				// No duplicates
				if (in_array($subscriber->created_by, $processed))
				{
					continue;
				}

				// Send message
				$this->_sendEmail($project, $subscriber, $activities, $subject);

				$processed[] = $subscriber->created_by;
			}
		}

		return;
	}

	/**
	 * Handles the actual sending of emails
	 *
	 * @param   object  $project
	 * @param   object  $subscriber
	 * @param   array   $activities
	 * @param   string  $subject
	 * @return  bool
	 */
	private function _sendEmail($project, $subscriber, $activities = array(), $subject)
	{
		$name  = Config::get('sitename') . ' ' . Lang::txt('PLG_PROJECTS_WATCH_SUBSCRIBER');
		$email = $subscriber->get('email');

		// Get profile information
		if ($subscriber->get('created_by'))
		{
			$user = User::getInstance($subscriber->get('created_by'));
			if ($user->get('id'))
			{
				$name  = $user->get('name');
				$email = $user->get('email');
			}
		}

		if (empty($email))
		{
			return false;
		}

		$eview = new Hubzero\Mail\View(array(
			'base_path' => Component::path('com_projects') . DS . 'site',
			'name'      => 'emails',
			'layout'    => 'watch_plain'
		));
		$eview->activities = $activities;
		$eview->subject    = $subject;
		$eview->project    = $project;

		$plain = $eview->loadTemplate(false);
		$plain = str_replace("\n", "\r\n", $plain);

		// HTML
		$eview->setLayout('watch_html');

		$html = $eview->loadTemplate();
		$html = str_replace("\n", "\r\n", $html);

		// Build message
		$message = new Hubzero\Mail\Message();
		$message->setSubject($subject)
				->addFrom(Config::get('mailfrom'), Config::get('sitename'))
				->addTo($email, $name)
				->addHeader('X-Component', 'com_projects')
				->addHeader('X-Component-Object', 'projects_watch_email')
				->addPart($plain, 'text/plain')
				->addPart($html, 'text/html');

		// Send mail
		if (!$message->send())
		{
			$this->setError(Lang::txt('Failed to mail %s', $email));
			return false;
		}

		return true;
	}
}
