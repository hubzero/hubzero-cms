<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Projects Feed plugin
 */
class plgProjectsFeed extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Component name
	 *
	 * @var  string
	 */
	protected $_option = 'com_projects';

	/**
	 * Store internal message
	 *
	 * @var	 array
	 */
	protected $_msg = null;

	/**
	 * Repository path
	 *
	 * @var	 array
	 */
	protected $_path = null;

	/**
	 * Event call to determine if this plugin should return data
	 *
	 * @param   string  $alias
	 * @return  array   Plugin name and title
	 */
	public function &onProjectAreas($alias = null)
	{
		$area = array(
			'name'    => 'feed',
			'alias'   => null,
			'title'   => Lang::txt('PLG_PROJECTS_UPDATES'),
			'submenu' => null,
			'show'    => true,
			'icon'    => 'f053'
		);
		return $area;
	}

	/**
	 * Event call to return count of items
	 *
	 * @param   object  $model  Project
	 * @return  array   integer
	 */
	public function &onProjectCount($model)
	{
		// New activity count
		$counts['feed'] = $model->newCount();

		return $counts;
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
		$returnhtml = true;

		$arr = array(
			'html'     =>'',
			'metadata' =>''
		);

		// Get this area details
		$this->_area = $this->onProjectAreas();

		// Check if our area is in the array of areas we want to return results for
		if (is_array($areas))
		{
			if (empty($this->_area) || !in_array($this->_area['name'], $areas))
			{
				return;
			}
		}
		// Check that project exists
		if (!$model->exists())
		{
			return $arr;
		}

		// Check authorization
		if (!$model->access('member'))
		{
			return $arr;
		}

		// Model
		$this->model = $model;

		// Are we returning HTML?
		if ($returnhtml)
		{
			$ajax = Request::getInt('ajax', 0);

			// Record page visit
			if (!$ajax)
			{
				// First-time visit, record join activity
				$model->recordFirstJoinActivity();

				// Record page visit
				$model->recordVisit();
			}

			// Hide welcome screen?
			$c = Request::getInt('c', 0);
			if ($c)
			{
				$model->member()->saveParam(
					$model->get('id'),
					User::get('id'),
					$param = 'hide_welcome',
					1
				);
				App::redirect(Route::url($model->link()));
				return;
			}

			// Set vars
			$this->_config = $model->config();
			$this->_task   = Request::getString('action', '');
			$this->_uid    = User::get('id');

			switch ($this->_task)
			{
				case 'delete':
					$arr['html'] = $this->_delete();
					break;
				case 'save':
					$arr['html'] = $this->_save();
					break;
				case 'savecomment':
					$arr['html'] = $this->_saveComment();
					break;
				case 'deletecomment':
					$arr['html'] = $this->_deleteComment();
					break;
				case 'update':
					$arr['html'] = $this->updateFeed();
					break;
				case 'page':
				default:
					$arr['html'] = $this->page();
					break;
			}
		}

		// Return data
		return $arr;
	}

	/**
	 * Event call to get side content
	 *
	 * @param   object  $model
	 * @param   string  $area
	 * @return  mixed
	 */
	public function onProjectExtras($model, $area)
	{
		// Check if our area is the one we want to return results for
		if ($area != 'feed')
		{
			return;
		}

		// No suggestions for read-only users
		if (!$model->access('content'))
		{
			return false;
		}

		// Allow to place custom modules on project pages
		$html = \Hubzero\Module\Helper::renderModules('projectpage');

		// Side blocks from other plugins?
		$sections = Event::trigger('projects.onProjectMiniList', array($model));

		if (!empty($sections))
		{
			// Show subscription to feed (new)
			$subscribe = Event::trigger('projects.onProjectMember', array($model));

			$html .= !empty($subscribe[0]) ? $subscribe[0] : null;
			foreach ($sections as $section)
			{
				$html .= !empty($section) ? $section : null;
			}
		}

		return $html;
	}

	/**
	 * Event call to get plugin notification
	 *
	 * @param   object  $model
	 * @param   string  $area
	 * @return  mixed
	 */
	public function onProjectNotification($model, $area)
	{
		// Check if our area is the one we want to return results for
		if ($area != 'feed')
		{
			return;
		}

		$html = '';

		// Acting member
		$member = $model->member();
		if ($member && !is_object($member->params))
		{
			$member->params = new \Hubzero\Config\Registry($member->params);
		}

		// Show welcome screen?
		$showWelcome = $member && is_object($member->params) && $member->params->get('hide_welcome') == 0  ? 1 : 0;

		// Show welcome banner with suggestions
		if ($showWelcome)
		{
			// Get suggestions
			$suggestions = \Components\Projects\Helpers\Html::getSuggestions($model);

			// Display welcome message
			$view = $this->view('_welcome', 'modules')
				->set('option', $this->_option)
				->set('suggestions', $suggestions)
				->set('model', $model);

			$html .= $view->loadTemplate();
		}

		return $html;
	}

	/**
	 * View of project updates
	 *
	 * @return  string
	 */
	public function page()
	{
		$limit = intval($this->params->get('limit', 25));

		$filters = array(
			'role'  => ($this->model->member() ? $this->model->member()->role : 0),
			'limit' => Request::getInt('limit', $limit),
			'start' => Request::getInt('start', 0),
			'search' => Request::getString('search', '')
		);

		$recipient = Hubzero\Activity\Recipient::all();

		$r = $recipient->getTableName();
		$l = Hubzero\Activity\Log::blank()->getTableName();

		$scopes = array('project');
		$managers = $this->model->table('Owner')->getIds($this->model->get('id')); //team(array('role' => 1));
		if (in_array(User::get('id'), $managers))
		{
			$scopes[] = 'project_managers';
		}

		$recipient
			->select($r . '.*')
			->including('log')
			->join($l, $l . '.id', $r . '.log_id')
			->whereIn($r . '.scope', $scopes)
			->whereEquals($r . '.scope_id', $this->model->get('id'))
			->whereEquals($r . '.state', Hubzero\Activity\Recipient::STATE_PUBLISHED);

		if ($filters['search'])
		{
			$recipient->whereLike($l . '.description', $filters['search']);
		}

		$recipient->whereEquals($l . '.parent', 0);

		$total = $recipient->copy()->total();

		$activities = $recipient
			->ordered()
			->limit($filters['limit'])
			->start($filters['start'])
			->rows();

		// Output html
		$view = $this->view('default', 'view')
			->set('params', $this->model->params)
			->set('option', $this->_option)
			->set('model', $this->model)
			->set('uid', $this->_uid)
			->set('filters', $filters)
			->set('limit', $limit)
			->set('total', $total)
			->set('activities', $activities)
			->set('title', $this->_area['title']);

		return $view->loadTemplate();
	}

	/**
	 * Save new blog entry
	 *
	 * @return  void  redirect
	 */
	protected function _save()
	{
		// Check permission
		if (!$this->model->access('content'))
		{
			throw new Exception(Lang::txt('ALERTNOTAUTH'), 403);
		}

		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		// Incoming
		$managers  = Request::getInt('managers_only', 0);
		$comment = array(
			'id'          => Request::getInt('activity', 0),
			'description' => trim(Request::getString('comment', '')),
			'scope'       => 'project.comment',
			'scope_id'    => $this->model->get('id'),
			'parent'      => Request::getInt('parent_activity', 0),
		);
		$isNew = true;

		// Text clean-up
		$comment['description'] = \Hubzero\Utility\Sanitize::stripScripts($comment['description']);
		$comment['description'] = \Hubzero\Utility\Sanitize::stripImages($comment['description']);

		$row = Hubzero\Activity\Log::oneOrNew($comment['id'])->set($comment);

		if ($row->get('id'))
		{
			$isNew = false;
		}

		if ($comment['description'])
		{
			// Save new blog entry
			if (!$row->save())
			{
				$this->setError($row->getError());
			}
			else
			{
				$this->_msg = ($isNew ? Lang::txt('PLG_PROJECTS_BLOG_NEW_BLOG_ENTRY_SAVED') : Lang::txt('PLG_PROJECTS_BLOG_BLOG_ENTRY_SAVED'));
			}

			// Record activity
			if ($row->get('id') && $isNew)
			{
				// Record the activity
				$recipients = array();
				// Log to the project
				$recipients[] = ['project', $this->model->get('id')];
				// Log the activity to the creator
				$recipients[] = ['user', $row->get('created_by')];
				// Notify the parent group
				if ($gid = $this->model->get('owned_by_group'))
				{
					$recipients[] = ['group', $gid];
				}

				// Notify the creator of the parent comment
				if ($row->get('parent'))
				{
					$recipients[] = ['user', $row->parent()->row()->get('created_by')];

					// We have a child comment
					// So, we want to force the parent to show up more recent in the list
					// to reflect the new comment.
					$currentRecipients = Hubzero\Activity\Recipient::all()
						->whereEquals('log_id', $row->get('parent'))
						->whereEquals('state', Hubzero\Activity\Recipient::STATE_PUBLISHED)
						->rows();

					foreach ($currentRecipients as $recipient)
					{
						$recipient->set(array(
							'created'  => Date::toSql(),
							'viewed'   => null
						));
						$recipient->save();
					}
				}

				Event::trigger('system.logActivity', [
					'activity' => [
						'id'          => $row->get('id'),
						'action'      => ($comment['id'] ? 'updated' : 'created'),
						'scope'       => $row->get('scope'),
						'scope_id'    => $row->get('scope_id'),
						'anonymous'   => $row->get('anonymous', 0),
						'description' => $row->get('description'),
						'details'     => array(
							'url'   => Route::url($this->model->link() . '&active=' . $this->_name . '#activity' . $row->get('id')),
							'class' => ($row->get('parent') ? 'quote' : 'blog')
						)
					],
					'recipients' => $recipients
				]);

				Event::trigger('projects.onWatch', array(
					$this->model,
					($row->get('parent') ? 'quote' : 'blog'),
					array($row->get('id')),
					User::get('id')
				));
			}
		}

		// Pass error or success message
		if ($this->getError())
		{
			Notify::message($this->getError(), 'error', 'projects');
		}
		elseif (!empty($this->_msg))
		{
			Notify::message($this->_msg, 'success', 'projects');
		}

		// Redirect
		App::redirect(Route::url($this->model->link()));
	}

	/**
	 * Delete entry
	 *
	 * @return  void  redirect
	 */
	protected function _delete()
	{
		// Check permission
		if (!$this->model->access('content'))
		{
			throw new Exception(Lang::txt('ALERTNOTAUTH'), 403);
		}

		$id = Request::getInt('activity', 0);

		$entry = Hubzero\Activity\Log::oneOrFail($id);

		if ($this->model->access('content') || $entry->get('created_by') == User::get('id'))
		{
			foreach ($entry->recipients as $recipient)
			{
				// Note: We're just unpublishing the recipient entry rather
				// that removing the activity entry itself.
				if (!$recipient->markAsUnpublished())
				{
					$this->setError($recipient->getError());
				}
			}

			// Unpublish comments on this entry too
			foreach ($entry->children as $child)
			{
				foreach ($child->recipients as $recipient)
				{
					// Note: We're just unpublishing the recipient entry rather
					// that removing the activity entry itself.
					if (!$recipient->markAsUnpublished())
					{
						$this->setError($recipient->getError());
					}
				}
			}
		}
		else
		{
			// Unauthorized
			$this->setError(Lang::txt('COM_PROJECTS_ERROR_ACTION_NOT_AUTHORIZED'));
		}

		// Pass error or success message
		if ($this->getError())
		{
			Notify::message($this->getError(), 'error', 'projects');
		}
		elseif (!empty($this->_msg))
		{
			Notify::message($this->_msg, 'success', 'projects');
		}

		// Redirect
		App::redirect(Route::url($this->model->link('feed')));
	}

	/**
	 * Update activity feed (load more entries)
	 *
	 * @return  string
	 */
	public function updateFeed()
	{
		$limit = intval($this->params->get('limit', 25));

		$filters = array(
			'limit' => Request::getInt('limit', $limit),
			'start' => Request::getInt('start', 0),
			'created' => ''
		);

		if ($start = Request::getString('recorded'))
		{
			$filters['created'] = $start;
			$filters['sortby']  = 'created';
			$filters['sortdir'] = 'ASC';
		}

		$recipient = Hubzero\Activity\Recipient::all();

		$r = $recipient->getTableName();
		$l = Hubzero\Activity\Log::blank()->getTableName();

		$scopes = array('project');
		$managers = $this->model->table('Owner')->getIds($this->model->get('id')); //team(array('role' => 1));
		if (in_array(User::get('id'), $managers))
		{
			$scopes[] = 'project_managers';
		}

		$recipient
			->select($r . '.*')
			->including('log')
			->join($l, $l . '.id', $r . '.log_id')
			->whereIn($r . '.scope', $scopes)
			->whereEquals($r . '.scope_id', $this->model->get('id'))
			->whereEquals($r . '.state', Hubzero\Activity\Recipient::STATE_PUBLISHED)
			->whereEquals($l . '.parent', 0);

		if ($filters['created'])
		{
			$recipient->where($r . '.created', '>', $filters['created']);
		}

		$total = $recipient->copy()->total();

		$activities = $recipient
			->ordered()
			->limit($filters['limit'])
			->start($filters['start'])
			->rows();

		// In this case, we're expecting JSON output
		// @TODO: Move to API
		if (isset($filters['created']) && $filters['created'])
		{
			$data = new stdClass();
			$data->activities = array();

			if (count($activities))
			{
				$shown = array();

				// Loop through activities
				foreach ($activities as $a)
				{
					if (in_array($a->get('id'), $shown))
					{
						continue;
					}

					$shown[] = $a->get('id');

					$prep = array(
						'activity'   => $a->log->toObject(),
						'eid'        => $a->log->get('id'),
						//'etbl'       => $etbl,
						'body'       => $a->log->get('description'),
						//'raw'        => $content,
						//'deletable'  => $deletable,
						//'comments'   => $comments,
						'class'      => $a->log->details->get('class', ($a->log->get('parent') ? 'quote' : '')),
						//'preview'    => $preview,
						'parent'     => $a->log->get('parent')
					);

					$prep['body'] = $this->view('_activity', 'activity')
						->set('option', $this->_option)
						->set('model', $this->model)
						->set('activity', $a)
						->set('online', array())
						->loadTemplate();

					$data->activities[] = $prep;
				}
			}

			ob_clean();
			header('Content-type: text/plain');
			echo json_encode($data);
			exit();
		}

		$view = $this->view('default', 'activity')
			->set('option', $this->_option)
			->set('model', $this->model)
			->set('filters', $filters)
			->set('limit', $limit)
			->set('total', $total)
			->set('activities', $activities)
			->set('uid', $this->_uid)
			->set('title', $this->_area['title']);

		return $view->loadTemplate();
	}

	/**
	 * Activity data in multiple projects (members/groups plugins)
	 *
	 * @param   string   $area
	 * @param   object   $model
	 * @param   array    $projects
	 * @param   integer  $uid
	 * @param   array    $filters  Query filters
	 * @return  string
	 */
	public function onShared($area, $model, $projects, $uid, $filters)
	{
		// Check if our area is the one we want to return results for
		if ($area != 'feed')
		{
			return '';
		}

		$limit = (isset($filters['limit']) ? $filters['limit'] : 0);
		if (!isset($filters['start']))
		{
			$filters['start'] = Request::getInt('start', 0);
		}

		// Get and sort activities
		$recipient = Hubzero\Activity\Recipient::all();

		$r = $recipient->getTableName();
		$l = Hubzero\Activity\Log::blank()->getTableName();

		$recipient
			->select($r . '.*')
			->including('log')
			->join($l, $l . '.id', $r . '.log_id');

		// return feed view now if no projects found/provided
		$view = $this->view('shared', 'activity')
			->set('filters', $filters)
			->set('uid', $uid)
			->set('model', $model)
			->set('limit', $limit)
			->set('activities', array())
			->set('total', 0);
		if (empty($projects))
		{
			return $view->loadTemplate();
		}
		// Only pull activity the user has explicit access to
		foreach ($projects as $project_id)
		{
			$scopes = array('project');
			$managers = $model->table('Owner')->getIds($project_id);
			if (!$managers)
			{
				$managers = array();
			}
			if (in_array(User::get('id'), $managers))
			{
				$scopes[] = 'project_managers';
			}

			$recipient->orWhereEquals($r . '.scope_id', $project_id, 1)
					->whereIn($r . '.scope', $scopes, 1)
					->resetDepth();
		}

		$recipient
			->whereEquals($r . '.state', Hubzero\Activity\Recipient::STATE_PUBLISHED)
			->whereEquals($l . '.parent', 0);
		$total = $recipient->copy()->total();

		$activities = $recipient
			->ordered()
			->limit($filters['limit'])
			->start($filters['start'])
			->rows();

		// Output HTML
		$view
			->set('activities', $activities)
			->set('total', $total);

		return $view->loadTemplate();
	}

	/**
	 * Event call to post an activity
	 *
	 * @param   object   $model      Project to post to
	 * @param   string   $entry      Content to post
	 * @param   integer  $managers   Manager sonly?
	 * @param   integer  $posted_by  Who's posting? If not set, uses current user ID
	 * @param   string   $posted     Timestamp. If not set, uses Date("now")
	 * @return  void
	 */
	public function onSharedUpdate($model, $entry, $managers = 0, $posted_by = 0, $posted = null)
	{
		if (!$model || !$model->get('id'))
		{
			return;
		}

		if (!$entry)
		{
			return;
		}

		$entry = Hubzero\Utility\Sanitize::stripScripts((string) $entry);
		$entry = Hubzero\Utility\Sanitize::stripImages($entry);

		// Record the activity
		$recipients = array();
		// Log to the project
		$recipients[] = ['project', $model->get('id')];
		// Log the activity to the creator
		$recipients[] = ['user', $posted_by];

		// Notify the parent group
		if ($gid = $model->get('owned_by_group'))
		{
			$recipients[] = ['group', $gid];
		}

		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => 'created',
				'scope'       => 'project.comment',
				'scope_id'    => $model->get('id'),
				'anonymous'   => 0,
				'description' => $entry,
				'details'     => array(
					'url'   => Route::url($model->link() . '&active=feed'),
					'class' => 'blog'
				)
			],
			'recipients' => $recipients
		]);
	}
}
