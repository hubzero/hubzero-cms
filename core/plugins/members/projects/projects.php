<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Members Plugin class for projects
 */
class plgMembersProjects extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Constructor
	 *
	 * @param   object  &$subject  Event observer
	 * @param   array   $config    Optional config values
	 * @return  void
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_config = Component::params('com_projects');
		$this->_database = App::get('db');
	}

	/**
	 * Return the alias and name for this category of content
	 *
	 * @param   object  $user
	 * @param   object  $member
	 * @return  array
	 */
	public function &onMembersAreas($user, $member)
	{
		// default areas returned to nothing
		$areas = array();

		// if this is the logged in user show them
		if ($this->params->get('show', 'none') != 'none' || $user->get('id') == $member->get('id'))
		{
			$areas['projects'] = Lang::txt('PLG_MEMBERS_PROJECTS');
			$areas['icon'] = 'f03f';
			$areas['icon-class'] = 'icon-paper-airplane';
			$areas['menu'] = $this->params->get('display_tab', 1);
		}
		return $areas;
	}

	/**
	 * Perform actions when viewing a member profile
	 *
	 * @param   object  $user    Current user
	 * @param   object  $member  Current member page
	 * @param   string  $option  Start of records to pull
	 * @param   array   $areas   Active area(s)
	 * @return  array
	 */
	public function onMembers($user, $member, $option, $areas)
	{
		$returnhtml = true;
		$returnmeta = true;

		// Check if our area is in the array of areas we want to return results for
		if (is_array($areas))
		{
			if (!array_intersect($areas, $this->onMembersAreas($user, $member))
			 && !array_intersect($areas, array_keys($this->onMembersAreas($user, $member))))
			{
				$returnhtml = false;
			}
		}

		$arr = array(
			'html'     => '',
			'metadata' => ''
		);

		// Load classes
		require_once Component::path('com_projects') . DS . 'models' . DS . 'project.php';

		// Model
		$this->model = new \Components\Projects\Models\Project();

		// Set filters
		$this->_filters = array(
			'mine'     => 1,
			'updates'  => 1,
			'getowner' => 1,
			'sortby'   => Request::getString('sortby', 'title'),
			'sortdir'  => Request::getString('sortdir', 'ASC'),
			'filterby' => Request::getString('filterby', 'active'),
			'uid'      => $member->get('id')
		);
		if (!in_array($this->_filters['filterby'], array('active', 'archived')))
		{
			$this->_filters['filterby'] = 'active';
		}

		// If configured to only show public projects to other users
		// We'll let admins see everything
		if ($this->params->get('show') == 'public' && !$user->authorise('core.manage', 'com_projects'))
		{
			if ($user->get('id') != $member->get('id'))
			{
				$this->_filters['private'] = 0;
			}
		}

		// Get a record count
		$this->_total = $this->model->entries('count', $this->_filters);

		$this->_user = $member;

		if ($returnhtml)
		{
			// Which view
			$task = Request::getCmd('action', '');

			if ($user->get('id') != $member->get('id'))
			{
				$task = '';
			}

			switch ($task)
			{
				case 'all':
					$arr['html'] = $this->_view('all');
					break;
				case 'group':
					$arr['html'] = $this->_view('group');
					break;
				case 'owned':
					$arr['html'] = $this->_view('owned');
					break;
				case 'updates':
					$arr['html'] = $this->_updates();
					break;
				default:
					$arr['html'] = $this->_view('all');
					break;
			}
		}

		//get meta
		$arr['metadata'] = array();

		$prefix = ($user->get('id') == $member->get('id')) ? 'I have' : $member->get('name') . ' has';
		$title = $prefix . ' ' . $this->_total . ' active projects.';

		//return total message count
		$arr['metadata']['count'] = $this->_total;

		$invites = $this->model->table('Owner')->checkInvitesByEmail($member->get('email'));

		if (count($invites))
		{
			$title = Lang::txt('PLG_MEMBERS_PROJECTS_NEW_INVITATIONS', count($invites));
			$link = Route::url($member->link() . '&active=projects');

			$arr['metadata']['alert'] = '<a class="alrt" href="' . $link . '"><span>' . $title . '</span></a>';

			Notify::info($arr['metadata']['alert'], 'com_members.profile');
		}

		return $arr;
	}

	/**
	 * View entries
	 *
	 * @param   string  $which  The type of entries to display
	 * @return  string
	 */
	protected function _view($which = 'all')
	{
		// Build the final HTML
		$view = $this->view('default', 'browse');

		$view->projects = $this->model->table()->getUserProjectIds($this->_user->get('id'));
		$view->newcount = $this->model->table()->getUpdateCount($view->projects, $this->_user->get('id'));

		$invites = $this->model->table('Owner')->checkInvitesByEmail($this->_user->get('email'));

		if ($which == 'all')
		{
			$this->_filters['which'] = 'owned';
			$view->owned = $this->model->entries('list', $this->_filters);

			$this->_filters['which'] = 'other';
			$view->rows = $this->model->entries('list', $this->_filters);
		}
		else
		{
			// Get records
			$options = array('owned', 'other', 'group');
			if (!in_array($which, $options))
			{
				$which = 'owned';
			}
			$this->_filters['which'] = $which;
			$view->rows = $this->model->entries('list', $this->_filters);
		}

		$view->invites = $invites;
		$view->which   = $which;
		$view->total   = $this->_total;
		$view->user    = $this->_user;
		$view->filters = $this->_filters;
		$view->config  = $this->_config;
		$view->option  = 'com_projects';
		if ($this->getError())
		{
			$view->setError($this->getError());
		}

		return $view->loadTemplate();
	}

	/**
	 * Display updates
	 *
	 * @return  string
	 */
	protected function _updates()
	{
		// Build the final HTML
		$view = $this->view('default', 'updates');

		// Get all projects user has access to
		$projects = $this->model->table()->getUserProjectIds($this->_user->get('id'));

		$view->filters = array(
			'limit' => Request::getInt('limit', 25, 'request')
		);

		// Get shared updates feed from blog plugin
		$results = Event::trigger('projects.onShared', array(
			'feed',
			$this->model,
			$projects,
			$this->_user->get('id'),
			$view->filters
		));

		$view->content      = !empty($results) && isset($results[0]) ? $results[0] : null;
		$view->newcount     = $this->model->table()->getUpdateCount(
			$projects,
			$this->_user->get('id')
		);
		$view->projectcount = $this->_total;
		$view->uid          = $this->_user->get('id');

		if ($this->getError())
		{
			$view->setError($this->getError());
		}

		return $view->loadTemplate();
	}
}
