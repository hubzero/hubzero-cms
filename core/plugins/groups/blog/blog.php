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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Groups Plugin class for blog entries
 */
class plgGroupsBlog extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Return the alias and name for this category of content
	 *
	 * @return     array
	 */
	public function &onGroupAreas()
	{
		$area = array(
			'name' => $this->_name,
			'title' => Lang::txt('PLG_GROUPS_BLOG'),
			'default_access' => $this->params->get('plugin_access', 'members'),
			'display_menu_tab' => $this->params->get('display_tab', 1),
			'icon' => 'f075'
		);
		return $area;
	}

	/**
	 * Return data on a group view (this will be some form of HTML)
	 *
	 * @param      object  $group      Current group
	 * @param      string  $option     Name of the component
	 * @param      string  $authorized User's authorization level
	 * @param      integer $limit      Number of records to pull
	 * @param      integer $limitstart Start of records to pull
	 * @param      string  $action     Action to perform
	 * @param      array   $access     What can be accessed
	 * @param      array   $areas      Active area(s)
	 * @return     array
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

		include_once(PATH_CORE . DS . 'components' . DS . 'com_blog' . DS . 'models' . DS . 'archive.php');

		$this->model = new \Components\Blog\Models\Archive('group', $group->get('gidNumber'));

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

			//user vars
			$this->authorized = $authorized;

			//group vars
			$this->group      = $group;
			$this->members    = $members;

			// Set some variables so other functions have access
			$this->action     = $action;
			$this->option     = $option;
			$this->database   = App::get('db');

			//get the plugins params
			$p = new \Hubzero\Plugin\Params($this->database);
			$this->params = $p->getParams($group->gidNumber, 'groups', $this->_name);

			if ($authorized == 'manager' || $authorized == 'admin')
			{
				$this->params->set('access-edit-comment', true);
				$this->params->set('access-delete-comment', true);
			}

			// Append to document the title
			Document::setTitle(Document::getTitle() . ': ' . Lang::txt('PLG_GROUPS_BLOG'));

			switch ($this->action)
			{
				// Feeds
				case 'feed.rss': $this->_feed();   break;
				case 'feed':     $this->_feed();   break;
				//case 'comments.rss': $this->_commentsFeed();   break;
				//case 'comments':     $this->_commentsFeed();   break;

				// Settings
				case 'savesettings': $arr['html'] = $this->_savesettings(); break;
				case 'settings':     $arr['html'] = $this->_settings();     break;

				// Comments
				case 'savecomment':   $arr['html'] = $this->_savecomment();   break;
				case 'newcomment':    $arr['html'] = $this->_newcomment();    break;
				case 'editcomment':   $arr['html'] = $this->_entry();         break;
				case 'deletecomment': $arr['html'] = $this->_deletecomment(); break;

				// Entries
				case 'save':   $arr['html'] = $this->_save();   break;
				case 'new':    $arr['html'] = $this->_new();    break;
				case 'edit':   $arr['html'] = $this->_edit();   break;
				case 'delete': $arr['html'] = $this->_delete(); break;
				case 'entry':  $arr['html'] = $this->_entry();  break;

				case 'archive':
				case 'browse':
				default: $arr['html'] = $this->_browse(); break;
			}
		}

		$filters = array(
			'scope'    => 'group',
			'scope_id' => $group->get('gidNumber'),
			'state'    => 'all'
		);

		if (User::isGuest())
		{
			$filters['state'] = 'public';
		}
		else
		{
			if ($authorized != 'member'
			 && $authorized != 'manager'
			 && $authorized != 'admin')
			{
				$filters['state'] = 'registered';
			}
		}

		// Build the HTML meant for the "profile" tab's metadata overview
		$arr['metadata']['count'] = $this->model->entries('count', $filters);

		return $arr;
	}

	/**
	 * Remove any associated data when group is deleted
	 *
	 * @param   object  $group  Group being deleted
	 * @return  string  Log of items removed
	 */
	public function onGroupDelete($group)
	{
		// Import needed libraries
		include_once(PATH_CORE . DS . 'components' . DS . 'com_blog' . DS . 'models' . DS . 'archive.php');

		$database = App::get('db');

		$tbl = new \Components\Blog\Tables\Entry($database);

		// Get all the IDs for entries associated with this group
		$entries = $tbl->find(
			'list',
			array(
				'scope' => 'group',
				'scope_id' => $group->get('gidNumber')
			),
			array('m.id')
		);

		// Start the log text
		$log = Lang::txt('PLG_GROUPS_BLOG_LOG') . ': ';

		if (count($entries) > 0)
		{
			// Loop through all the IDs for pages associated with this group
			foreach ($entries as $entry)
			{
				$tbl->id = $entry->id;
				$tbl->state = '-1';
				$tbl->store();

				// Add the ID to the log
				$log .= $entry->id . ' ' . "\n";
			}
		}
		else
		{
			$log .= Lang::txt('PLG_GROUPS_BLOG_NO_RESULTS_FOUND') . "\n";
		}

		// Return the log
		return $log;
	}

	/**
	 * Return a count of items that will be removed when group is deleted
	 *
	 * @param   object  $group  Group to delete
	 * @return  string
	 */
	public function onGroupDeleteCount($group)
	{
		include_once(PATH_CORE . DS . 'components' . DS . 'com_blog' . DS . 'models' . DS . 'archive.php');

		$database = App::get('db');

		$tbl = new \Components\Blog\Tables\Entry($database);

		// Get all the IDs for entries associated with this group
		$entries = $tbl->find(
			'count',
			array(
				'scope' => 'group',
				'scope_id' => $group->get('gidNumber')
			)
		);

		return Lang::txt('PLG_GROUPS_BLOG_LOG') . ': ' . $entries;
	}

	/**
	 * Parse an SEF URL into its component bits
	 * stripping out the path leading up to the blog plugin
	 *
	 * @return     string
	 */
	private function _parseUrl()
	{
		static $path;

		if (!$path)
		{
			$path = Request::path();

			$path = str_replace(Request::base(true), '', $path);
			$path = str_replace('index.php', '', $path);
			$path = '/' . trim($path, '/');

			$blog = '/groups/' . $this->group->get('cn') . '/blog';

			if ($path == $blog)
			{
				$path = array();
				return $path;
			}

			$path = ltrim($path, '/');
			$path = explode('/', $path);
			$path = array_map('urldecode', $path);

			/*while ($path[0] != 'members' && !empty($path));
			{
				array_shift($path);
			}*/
			$paths = array();
			$start = false;
			foreach ($path as $bit)
			{
				if ($bit == 'groups' && !$start)
				{
					$start = true;
					continue;
				}
				if ($start)
				{
					$paths[] = preg_replace('/[^a-zA-Z0-9_\-\:]/', '', $bit);
				}
			}
			if (count($paths) >= 2)
			{
				array_shift($paths);  // Remove group cn
				array_shift($paths);  // Remove 'blog'
			}
			$path = $paths;
		}

		return $path;
	}

	/**
	 * Display a list of latest blog entries
	 *
	 * @return     string
	 */
	private function _browse()
	{
		$view = $this->view('default', 'browse');
		$view->option     = $this->option;
		$view->group      = $this->group;
		$view->config     = $this->params;
		$view->authorized = $this->authorized;
		$view->model      = $this->model;

		// Filters for returning results
		$view->filters = array(
			'limit'      => Request::getInt('limit', Config::get('list_limit')),
			'start'      => Request::getInt('limitstart', 0),
			'created_by' => Request::getInt('author', 0),
			'year'       => Request::getInt('year', 0),
			'month'      => Request::getInt('month', 0),
			'scope'      => 'group',
			'scope_id'   => $this->group->get('gidNumber'),
			'search'     => Request::getVar('search',''),
			'authorized' => false,
			'state'      => 'public'
		);

		// See what information we can get from the path
		$path = Request::path();
		if (strstr($path, '/'))
		{
			$bits = $this->_parseUrl();

			// if we have 3 pieces, then there is year/month/entry
			// display entry
			if (count($bits) > 2)
			{
				return $this->_entry();
			}

			$view->filters['year']  = (isset($bits[0]) && is_numeric($bits[0])) ? $bits[0] : $view->filters['year'];
			$view->filters['month'] = (isset($bits[1]) && is_numeric($bits[1])) ? $bits[1] : $view->filters['month'];
		}
		if ($view->filters['year'] > date("Y"))
		{
			$view->filters['year'] = 0;
		}
		if ($view->filters['month'] > 12)
		{
			$view->filters['month'] = 0;
		}

		$view->canpost = $this->_getPostingPermissions();

		if (User::isGuest())
		{
			$view->filters['state'] = 'public';
		}
		else
		{
			if ($this->authorized != 'member'
			 && $this->authorized != 'manager'
			 && $this->authorized != 'admin')
			{
				$view->filters['state'] = 'registered';
			}
			else
			{
				if ($this->authorized == 'member'
				 || $this->authorized == 'manager'
				 || $this->authorized == 'admin')
				{
					$view->filters['authorized'] = true;
					$view->filters['state'] = 'all';
				}
				else
				{
					$view->filters['authorized'] = User::get('id');
				}
			}
		}

		$view->year   = $view->filters['year'];
		$view->month  = $view->filters['month'];
		$view->search = $view->filters['search'];

		foreach ($this->getErrors() as $error)
		{
			$view->setError($error);
		}

		return $view->loadTemplate();
	}

	/**
	 * Display an RSS feed of latest entries
	 *
	 * @return     string
	 */
	private function _feed()
	{
		if (!$this->params->get('feeds_enabled', 1))
		{
			$this->_browse();
			return;
		}

		include_once(PATH_CORE . DS . 'libraries' . DS . 'joomla' . DS . 'document' . DS . 'feed' . DS . 'feed.php');

		// Set the mime encoding for the document
		Document::setType('feed');

		// Start a new feed object
		$doc = Document::instance();
		$doc->link = Route::url('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=' . $this->_name);

		// Filters for returning results
		$filters = array(
			'limit'      => Request::getInt('limit', Config::get('list_limit')),
			'start'      => Request::getInt('limitstart', 0),
			'year'       => Request::getInt('year', 0),
			'month'      => Request::getInt('month', 0),
			'scope'      => 'group',
			'scope_id'   => $this->group->get('gidNumber'),
			'search'     => Request::getVar('search',''),
			'created_by' => Request::getInt('author', 0),
			'state'      => 'public'
		);

		$path = Request::path();
		if (strstr($path, '/'))
		{
			$bits = $this->_parseUrl();

			$filters['year']  = (isset($bits[0]) && is_numeric($bits[0])) ? $bits[0] : $filters['year'];
			$filters['month'] = (isset($bits[1]) && is_numeric($bits[1])) ? $bits[1] : $filters['month'];
		}
		if ($filters['year'] > date("Y"))
		{
			$filters['year'] = 0;
		}
		if ($filters['month'] > 12)
		{
			$filters['month'] = 0;
		}

		// Build some basic RSS document information
		$doc->title       = Config::get('sitename') . ': ' . Lang::txt('Groups') . ': ' . stripslashes($this->group->get('description')) . ': ' . Lang::txt('Blog');
		$doc->description = Lang::txt('PLG_GROUPS_BLOG_RSS_DESCRIPTION', $this->group->get('cn'), Config::get('sitename'));
		$doc->copyright   = Lang::txt('PLG_GROUPS_BLOG_RSS_COPYRIGHT', date("Y"), Config::get('sitename'));
		$doc->category    = Lang::txt('PLG_GROUPS_BLOG_RSS_CATEGORY');

		$rows = $this->model->entries('list', $filters);

		// Start outputing results if any found
		if ($rows->total() > 0)
		{
			foreach ($rows as $row)
			{
				$item = new \Hubzero\Document\Type\Feed\Item();

				// Strip html from feed item description text
				$item->description = $row->content('parsed');
				$item->description = html_entity_decode(\Hubzero\Utility\Sanitize::stripAll($item->description));
				if ($this->params->get('feed_entries') == 'partial')
				{
					$item->description = \Hubzero\Utility\String::truncate($item->description, 300);
				}

				// Load individual item creator class
				$item->title       = html_entity_decode(strip_tags($row->get('title')));
				$item->link        = Route::url($row->link());
				$item->date        = date('r', strtotime($row->published()));
				$item->category    = '';
				$item->author      = $row->creator('name');

				// Loads item info into rss array
				$doc->addItem($item);
			}
		}

		// Output the feed
		echo $doc->render();
	}

	/**
	 * Determine permissions to post an entry
	 *
	 * @return     boolean True if user cna post, false if not
	 */
	private function _getPostingPermissions()
	{
		switch ($this->params->get('posting'))
		{
			case 1:
				if ($this->authorized == 'manager' || $this->authorized == 'admin')
				{
					return true;
				}
			break;

			case 0:
			default:
				if ($this->authorized == 'member' || $this->authorized == 'manager' || $this->authorized == 'admin')
				{
					return true;
				}
				else
				{
					return false;
				}
			break;
		}

		return false;
	}

	/**
	 * Display a blog entry
	 *
	 * @return     string
	 */
	private function _entry()
	{
		$view = $this->view('default', 'entry');
		$view->option     = $this->option;
		$view->group      = $this->group;
		$view->config     = $this->params;
		$view->authorized = $this->authorized;
		$view->model      = $this->model;

		if (isset($this->entry) && is_object($this->entry))
		{
			$view->row = $this->entry;
		}
		else
		{
			$path = Request::path();
			if (strstr($path, '/'))
			{
				$bits = $this->_parseUrl();

				$alias = end($bits);
			}

			$view->row = $this->model->entry($alias);
		}

		if (!$view->row->exists())
		{
			App::abort(404, Lang::txt('PLG_GROUPS_BLOG_NO_ENTRY_FOUND'));
			return; // $this->_browse(); Can cause infinite loop
		}

		// Check authorization
		if (($view->row->get('state') == 2 && User::isGuest())
		 || ($view->row->get('state') == 0 && User::get('id') != $view->row->get('created_by') && $this->authorized != 'member' && $this->authorized != 'manager' && $this->authorized != 'admin'))
		{
			App::abort(403, Lang::txt('PLG_GROUPS_BLOG_NOT_AUTH'));
			return;
		}

		// make sure the group owns this
		if ($view->row->get('scope_id') != $this->group->get('gidNumber'))
		{
			App::abort(403, Lang::txt('PLG_GROUPS_BLOG_NOT_AUTH'));
			return;
		}

		// Filters for returning results
		$view->filters = array(
			'limit'      => 10,
			'start'      => 0,
			'scope'      => 'group',
			'scope_id'   => $this->group->get('gidNumber'),
			'created_by' => 0
		);

		if (User::isGuest())
		{
			$view->filters['state'] = 'public';
		}
		else
		{
			$view->filters['state'] = 'registered';
		}

		$view->canpost = $this->_getPostingPermissions();

		foreach ($this->getErrors() as $error)
		{
			$view->setError($error);
		}

		return $view->loadTemplate();
	}

	/**
	 * Display a form for creating an entry
	 *
	 * @return     string
	 */
	private function _new()
	{
		return $this->_edit();
	}

	/**
	 * Display a form for editing an entry
	 *
	 * @return     string
	 */
	private function _edit($row=null)
	{
		$blog = Route::url('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=' . $this->_name);

		if (User::isGuest())
		{
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . base64_encode($blog))
			);
			return;
		}

		if (!$this->authorized || !$this->_getPostingPermissions())
		{
			App::redirect(
				$blog,
				Lang::txt('PLG_GROUPS_BLOG_ERROR_PERMISSION_DENIED'),
				'error'
			);
			return;
		}

		// Instantiate view
		$view = $this->view('default', 'edit');
		$view->option     = $this->option;
		$view->group      = $this->group;
		$view->task       = $this->action;
		$view->config     = $this->params;
		$view->authorized = $this->authorized;
		$view->model      = $this->model;

		if (is_object($row))
		{
			$view->entry = $row;
		}
		else
		{
			$id = Request::getInt('entry', 0);
			$view->entry = new \Components\Blog\Models\Entry($id);
		}

		// Does it exist?
		if (!$view->entry->exists())
		{
			// Set some defaults
			$view->entry->set('allow_comments', 1);
			$view->entry->set('state', 1);
			$view->entry->set('scope', 'group');
			$view->entry->set('scope_id', $this->group->get('gidNumber'));
		}

		foreach ($this->getErrors() as $error)
		{
			$view->setError($error);
		}
		return $view->loadTemplate();
	}

	/**
	 * Save an entry
	 *
	 * @return     void
	 */
	private function _save()
	{
		if (User::isGuest())
		{
			$blog = Route::url('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=' . $this->_name, false, true);

			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . base64_encode($blog)),
				Lang::txt('GROUPS_LOGIN_NOTICE'),
				'warning'
			);
			return;
		}

		if (!$this->authorized)
		{
			$this->setError(Lang::txt('PLG_GROUPS_BLOG_NOT_AUTHORIZED'));
			return $this->_browse();
		}

		if (!$this->_getPostingPermissions())
		{
			$this->setError(Lang::txt('PLG_GROUPS_BLOG_ERROR_PERMISSION_DENIED'));
			return $this->_browse();
		}

		$entry = Request::getVar('entry', array(), 'post', 'none', 2);

		if (isset($entry['publish_up']) && $entry['publish_up'] != '')
		{
			$entry['publish_up']   = Date::of($entry['publish_up'], Config::get('offset'))->toSql();
		}

		if (isset($entry['publish_down']) && $entry['publish_down'] != '')
		{
			$entry['publish_down'] = Date::of($entry['publish_down'], Config::get('offset'))->toSql();
		}

		// make sure we dont want to turn off comments
		$entry['allow_comments'] = (isset($entry['allow_comments'])) ? : 0;

		// Instantiate model
		$row = $this->model->entry($entry['id']);

		// Bind data
		if (!$row->bind($entry))
		{
			$this->setError($row->getError());
			return $this->_edit($row);
		}

		if (!$row->get('id'))
		{
			// Fills in missing pieces, mainly generate the alias
			if ($row->get('alias') == '')
			{
				$row->check();
			}

			// Instantiate a new model
			$checkModel = new \Components\Blog\Models\Archive('group', $this->group->get('gidNumber'));

			// Check for unique alias name
			$item = $checkModel->entry($row->get('alias'));

			if ($item->get('alias') == $row->get('alias'))
			{
				$this->setError(Lang::txt('PLG_GROUPS_BLOG_ERROR_ALIAS_EXISTS'));
				return $this->_edit($row);
			}
		}

		// Store new content
		if (!$row->store(true))
		{
			$this->setError($row->getError());
			return $this->_edit($row);
		}

		// Process tags
		if (!$row->tag(Request::getVar('tags', '')))
		{
			$this->setError($row->getError());
			return $this->_edit($row);
		}

		App::redirect(
			Route::url($row->link())
		);
	}

	/**
	 * Delete an entry
	 *
	 * @return     string
	 */
	private function _delete()
	{
		if (User::isGuest())
		{
			$this->setError(Lang::txt('GROUPS_LOGIN_NOTICE'));
			return;
		}

		if (!$this->authorized)
		{
			$this->setError(Lang::txt('PLG_GROUPS_BLOG_NOT_AUTHORIZED'));
			return $this->_browse();
		}

		if (!$this->_getPostingPermissions())
		{
			$this->setError(Lang::txt('PLG_GROUPS_BLOG_ERROR_PERMISSION_DENIED'));
			return $this->_browse();
		}

		// Incoming
		$id = Request::getInt('entry', 0);
		if (!$id)
		{
			return $this->_browse();
		}

		$process    = Request::getVar('process', '');
		$confirmdel = Request::getVar('confirmdel', '');

		// Initiate a blog entry object
		$entry = new \Components\Blog\Models\Entry($id);

		// Did they confirm delete?
		if (!$process || !$confirmdel)
		{
			if ($process && !$confirmdel)
			{
				$this->setError(Lang::txt('PLG_GROUPS_BLOG_ERROR_CONFIRM_DELETION'));
			}

			// Output HTML
			$view = $this->view('default', 'delete');
			$view->option     = $this->option;
			$view->group      = $this->group;
			$view->task       = $this->action;
			$view->config     = $this->params;
			$view->entry      = $entry;
			$view->authorized = $this->authorized;
			$view->model      = $this->model;

			foreach ($this->getErrors() as $error)
			{
				$view->setError($error);
			}

			return $view->loadTemplate();
		}

		// Delete the entry itself
		$entry->set('state', -1);
		if (!$entry->store())
		{
			$this->setError($entry->getError());
		}

		// Return the topics list
		return $this->_browse();
	}

	/**
	 * Save a comment
	 *
	 * @return     string
	 */
	private function _savecomment()
	{
		// Ensure the user is logged in
		if (User::isGuest())
		{
			$blog = Route::url('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=' . $this->_name, false, true);

			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . base64_encode($blog)),
				Lang::txt('GROUPS_LOGIN_NOTICE'),
				'warning'
			);
			return;
		}

		// Incoming
		$comment = Request::getVar('comment', array(), 'post', 'none', 2);

		// Instantiate a new comment object and pass it the data
		$row = new \Components\Blog\Models\Comment($comment['id']);
		if (!$row->bind($comment))
		{
			$this->setError($row->getError());
			return $this->_entry();
		}

		// Store new content
		if (!$row->store(true))
		{
			$this->setError($row->getError());
			return $this->_entry();
		}

		return $this->_entry();
	}

	/**
	 * Delete a comment
	 *
	 * @return     string
	 */
	private function _deletecomment()
	{
		// Ensure the user is logged in
		if (User::isGuest())
		{
			$this->setError(Lang::txt('GROUPS_LOGIN_NOTICE'));
			return;
		}

		// Incoming
		$id = Request::getInt('comment', 0);
		if (!$id)
		{
			return $this->_entry();
		}

		// Initiate a blog comment object
		$comment = new \Components\Blog\Models\Comment($id);

		// Delete all comments on an entry
		$comment->set('state', 2);

		// Delete the entry itself
		if (!$comment->store(false))
		{
			$this->setError($comment->getError());
		}

		// Return the topics list
		return $this->_entry();
	}

	/**
	 * Display blog settings
	 *
	 * @return     string
	 */
	private function _settings()
	{
		if (User::isGuest())
		{
			$this->setError(Lang::txt('GROUPS_LOGIN_NOTICE'));
			return;
		}

		if ($this->authorized != 'manager' && $this->authorized != 'admin')
		{
			$this->setError(Lang::txt('PLG_GROUPS_BLOG_NOT_AUTHORIZED'));
			return $this->_browse();
		}

		// Output HTML
		$view = $this->view('default', 'settings');
		$view->option     = $this->option;
		$view->group      = $this->group;
		$view->task       = $this->action;
		$view->config     = $this->params;
		$view->model      = $this->model;

		$view->settings   = new \Hubzero\Plugin\Params($this->database);
		$view->settings->loadPlugin($this->group->gidNumber, $this->_type, $this->_name);

		$view->authorized = $this->authorized;
		$view->message    = (isset($this->message)) ? $this->message : '';

		foreach ($this->getErrors() as $error)
		{
			$view->setError($error);
		}

		return $view->loadTemplate();
	}

	/**
	 * Save blog settings
	 *
	 * @return     void
	 */
	private function _savesettings()
	{
		if (User::isGuest())
		{
			$this->setError(Lang::txt('GROUPS_LOGIN_NOTICE'));
			return;
		}

		if ($this->authorized != 'manager' && $this->authorized != 'admin')
		{
			$this->setError(Lang::txt('PLG_GROUPS_BLOG_NOT_AUTHORIZED'));
			return $this->_browse();
		}

		$settings = Request::getVar('settings', array(), 'post');

		$row = new \Hubzero\Plugin\Params($this->database);
		if (!$row->bind($settings))
		{
			$this->setError($row->getError());
			return $this->_entry();
		}

		// Get parameters
		$p = new \Hubzero\Config\Registry(Request::getVar('params', array(), 'post'));

		$row->params = $p->toString();

		// Check content
		if (!$row->check())
		{
			$this->setError($row->getError());
			return $this->_settings();
		}

		// Store new content
		if (!$row->store())
		{
			$this->setError($row->getError());
			return $this->_settings();
		}

		App::redirect(
			Route::url('index.php?option=com_groups&cn=' . $this->group->get('cn') . '&active=' . $this->_name . '&action=settings'),
			Lang::txt('PLG_GROUPS_BLOG_SETTINGS_SAVED'),
			'passed'
		);
	}
}
