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

// No direct access
defined('_HZEXEC_') or die();

/**
 * Group plugin class for activity
 */
class plgGroupsActivity extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Return the alias and name for this category of content
	 *
	 * @return  array
	 */
	public function &onGroupAreas()
	{
		$area = array(
			'name' => $this->_name,
			'title' => Lang::txt('PLG_GROUPS_ACTIVITY'),
			'default_access' => $this->params->get('plugin_access', 'members'),
			'display_menu_tab' => $this->params->get('display_tab', 1),
			'icon' => 'f056'
		);
		return $area;
	}

	/**
	 * Return data on a group view (this will be some form of HTML)
	 *
	 * @param   object   $group       Current group
	 * @param   string   $option      Name of the component
	 * @param   string   $authorized  User's authorization level
	 * @param   integer  $limit       Number of records to pull
	 * @param   integer  $limitstart  Start of records to pull
	 * @param   string   $action      Action to perform
	 * @param   array    $access      What can be accessed
	 * @param   array    $areas       Active area(s)
	 * @return  array
	 */
	public function onGroup($group, $option, $authorized, $limit=0, $limitstart=0, $action='', $access, $areas=null)
	{
		$return = 'html';
		$active = $this->_name;

		// The output array we're returning
		$arr = array(
			'html'     => '',
			'metadata' => ''
		);

		//get this area details
		$this_area = $this->onGroupAreas();

		// Check if our area is in the array of areas we want to return results for
		if (is_array($areas) && $limit)
		{
			if (!in_array($this_area['name'], $areas))
			{
				$return = 'metadata';
			}
		}

		//are we returning html
		if ($return == 'html')
		{
			//set group members plugin access level
			$group_plugin_acl = $access[$active];

			//get the group members
			$members = $group->get('members');

			//if set to nobody make sure cant access
			if ($group_plugin_acl == 'nobody')
			{
				$arr['html'] = '<p class="info">' . Lang::txt('GROUPS_PLUGIN_OFF', ucfirst($active)) . '</p>';
				return $arr;
			}

			//check if guest and force login if plugin access is registered or members
			if (User::isGuest()
			 && ($group_plugin_acl == 'registered' || $group_plugin_acl == 'members'))
			{
				$url = Route::url('index.php?option=com_groups&cn=' . $group->get('cn') . '&active=' . $active, false, true);

				App::redirect(
					Route::url('index.php?option=com_users&view=login&return=' . base64_encode($url)),
					Lang::txt('GROUPS_PLUGIN_REGISTERED', ucfirst($active)),
					'warning'
				);
				return;
			}

			//check to see if user is member and plugin access requires members
			if (!in_array(User::get('id'), $members)
			 && $group_plugin_acl == 'members'
			 && $authorized != 'admin')
			{
				$arr['html'] = '<p class="info">' . Lang::txt('GROUPS_PLUGIN_REQUIRES_MEMBER', ucfirst($active)) . '</p>';
				return $arr;
			}

			include_once __DIR__ . '/models/attachment.php';

			$this->group = $group;
			$this->base  = 'index.php?option=com_groups&cn=' . $group->get('cn');

			$action = Request::getCmd('action', 'feed');

			if ($this->group->published != 1)
			{
				$action = 'feed';
			}

			switch ($action)
			{
				case 'post':
					$arr['html'] = $this->postAction();
					break;
				case 'remove':
					$arr['html'] = $this->removeAction();
					break;
				case 'unstar':
					$arr['html'] = $this->starAction();
					break;
				case 'star':
					$arr['html'] = $this->starAction();
					break;
				case 'feed':
				default:
					$arr['html'] = $this->feedAction();
					break;
			}

			$arr['html'] = $this->feedAction();
		}

		// Get the number of unread messages
		$unread = Hubzero\Activity\Recipient::all()
			->whereEquals('scope', 'group')
			->whereEquals('scope_id', $group->get('gidNumber'))
			->whereEquals('state', 1)
			->whereEquals('viewed', '0000-00-00 00:00:00')
			->total();

		// Return total message count
		$arr['metadata']['count'] = $unread;

		// Return data
		return $arr;
	}

	/**
	 * Show a feed
	 *
	 * @return  string
	 */
	protected function feedAction()
	{
		$filters = array();
		$filters['filter'] = Request::getWord('filter');
		$filters['search'] = Request::getVar('q');
		$filters['limit']  = Request::getInt('limit', Config::get('list_limit'));
		$filters['start']  = Request::getInt('start', 0);

		if (!in_array($filters['filter'], ['starred']))
		{
			$filters['filter'] = '';
		}

		$recipient = Hubzero\Activity\Recipient::all();

		$r = $recipient->getTableName();
		$l = Hubzero\Activity\Log::blank()->getTableName();

		$scopes = array('group');
		if (in_array(User::get('id'), $this->group->get('managers')))
		{
			$scopes[] = 'group_managers';
		}

		$recipient
			->select($r . '.*')
			->including('log')
			->join($l, $l . '.id', $r . '.log_id')
			->whereIn($r . '.scope', $scopes)
			->whereEquals($r . '.scope_id', $this->group->get('gidNumber'))
			->whereEquals($r . '.state', Hubzero\Activity\Recipient::STATE_PUBLISHED);

		if ($filters['filter'] == 'starred')
		{
			$recipient->whereEquals($r . '.starred', 1);
		}

		if ($filters['search'])
		{
			$recipient->whereLike($l . '.description', $filters['search']);
		}

		if (!$filters['filter'] && !$filters['search'])
		{
			$recipient->whereEquals($l . '.parent', 0);
		}

		$total = $recipient->copy()->total();

		$entries = $recipient
			->ordered()
			//->paginated()
			->limit($filters['limit'])
			->start($filters['start'])
			->rows();

		$view = $this->view('default', 'activity')
			->set('name', $this->_name)
			->set('group', $this->group)
			->set('categories', null)
			->set('filters', $filters)
			->set('total', $total)
			->set('rows', $entries);

		return $view->loadTemplate();
	}

	/**
	 * Unpublish an entry
	 *
	 * @return  string
	 */
	protected function removeAction()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		$id      = Request::getInt('activity', 0);
		$no_html = Request::getInt('no_html', 0);

		$entry = Hubzero\Activity\Recipient::oneOrFail($id);

		if (!$entry->markAsUnpublished())
		{
			$this->setError($entry->getError());
		}

		$success = Lang::txt('PLG_GROUPS_ACTIVITY_RECORD_REMOVED');

		if ($no_html)
		{
			$response = new stdClass;
			$response->success = true;
			$response->message = $success;
			if ($err = $this->getError())
			{
				$response->success = false;
				$response->message = $err;
			}

			ob_clean();
			header('Content-type: text/plain');
			echo json_encode($response);
			exit();
		}

		if ($err = $this->getError())
		{
			Notify::error($err);
		}
		else
		{
			Notify::success($success);
		}

		// Redirect
		App::redirect(
			Route::url($this->base . '&active=' . $this->_name, false)
		);
	}

	/**
	 * Star/unstar an entry
	 *
	 * @return  string
	 */
	protected function starAction()
	{
		$id      = Request::getInt('activity', 0);
		$no_html = Request::getInt('no_html', 0);
		$action  = Request::getVar('action', 'star');

		$entry = Hubzero\Activity\Recipient::oneOrFail($id);
		$entry->set('starred', ($action == 'star' ? 1 : 0));

		if (!$entry->save())
		{
			$this->setError($entry->getError());
		}

		$success = $action == 'star'
			? Lang::txt('PLG_GROUPS_ACTIVITY_RECORD_STARRED')
			: Lang::txt('PLG_GROUPS_ACTIVITY_RECORD_UNSTARRED');

		if ($no_html)
		{
			$response = new stdClass;
			$response->success = true;
			$response->message = $success;
			if ($err = $this->getError())
			{
				$response->success = false;
				$response->message = $err;
			}

			ob_clean();
			header('Content-type: text/plain');
			echo json_encode($response);
			exit();
		}

		if ($err = $this->getError())
		{
			Notify::error($err);
		}
		else
		{
			Notify::success($success);
		}

		// Redirect
		App::redirect(
			Route::url($this->base . '&active=' . $this->_name, false)
		);
	}

	/**
	 * Save a comment
	 *
	 * @return  void
	 */
	protected function postAction()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$comment = Request::getVar('activity', array(), 'post', 'none', 2);

		// Instantiate a new object and bind data
		$row = Hubzero\Activity\Log::oneOrNew($comment['id'])->set($comment);

		// Process attachment
		$upload = Request::getVar('activity_file', '', 'files', 'array');

		if (!empty($upload) && $upload['name'])
		{
			if ($upload['error'])
			{
				$this->setError(\Lang::txt('PLG_GROUPS_ACTIVITY_ERROR_UPLOADING_FILE'));
			}

			$file = new Plugins\Groups\Activity\Models\Attachment();
			$file->setUploadDir('/site/groups/' . $this->group->get('gidNumber') . '/uploads');

			if (!$file->upload($upload['name'], $upload['tmp_name'], $upload['size']))
			{
				App::redirect(
					Route::url($this->base . '&active=' . $this->_name),
					$file->getError(),
					'error'
				);
			}
			else
			{
				$row->details->set('attachments', array(
					$file->toArray()
				));
				$row->set('details', $row->details->toString());
			}
		}

		// Store new content
		if (!$row->save())
		{
			User::setState(
				'failed_comment',
				$row->get('description')
			);

			App::redirect(
				Route::url($this->base . '&active=' . $this->_name),
				$row->getError(),
				'error'
			);
		}

		$who = Request::getWord('activity_recipients', 'all');

		// Record the activity
		$recipients = array();

		if ($who == 'managers')
		{
			$recipients[] = ['group_managers', $this->group->get('gidNumber')];
		}
		else
		{
			$recipients[] = ['group', $this->group->get('gidNumber')];
		}
		$recipients[] = ['user', $row->get('created_by')];
		if ($row->get('parent'))
		{
			$recipients[] = ['user', $row->parent()->get('created_by')];
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
					'url'         => Route::url($this->base . '&active=' . $this->_name . '#activity' . $row->get('id')),
					'title'       => $this->group->get('description'),
					'attachments' => $row->details->get('attachments')
				)
			],
			'recipients' => $recipients
		]);

		// Redirect
		App::redirect(
			Route::url($this->base . '&active=' . $this->_name),
			Lang::txt('PLG_GROUPS_ACTIVITY_COMMENTS_SAVED')
		);
	}
}
