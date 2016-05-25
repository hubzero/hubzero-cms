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
 * Members Plugin class for blog entries
 */
class plgMembersBlog extends \Hubzero\Plugin\Plugin
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
	 * @param   object  $user
	 * @param   object  $member
	 * @return  array
	 */
	public function &onMembersAreas($user, $member)
	{
		$areas = array(
			'blog' => Lang::txt('PLG_MEMBERS_BLOG'),
			'icon' => 'f075'
		);
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

		include_once(PATH_CORE . DS . 'components' . DS . 'com_blog' . DS . 'models' . DS . 'archive.php');

		// Get our model
		$this->model = new \Components\Blog\Models\Archive('member', $member->get('id'));

		if ($returnhtml)
		{
			$this->user    = $user;
			$this->member  = $member;
			$this->option  = $option;
			//$this->authorized = $authorized;
			$this->database = App::get('db');

			$this->params = \Hubzero\Plugin\Params::getParams($this->member->get('id'), 'members', $this->_name);

			if ($user->get('id') == $member->get('id'))
			{
				$this->params->set('access-edit-comment', true);
				$this->params->set('access-delete-comment', true);
			}

			// Append to document the title
			Document::setTitle(Document::getTitle() . ': ' . Lang::txt('PLG_MEMBERS_BLOG'));

			// Get and determine task
			$this->task = Request::getVar('action', '');

			if (!($task = Request::getVar('action', '', 'post')))
			{
				$bits = $this->_parseUrl();
				if ($this->task != 'deletecomment')
				{
					$num = count($bits);
					switch ($num)
					{
						case 3:
							$this->task = 'entry';
						break;

						case 2:
						case 1:
							if (is_numeric($bits[0]))
							{
								$this->task = 'browse';
							}
						break;
					}
				}
			}
			else
			{
				$this->task = $task;
			}

			switch ($this->task)
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

		// Build filters
		$filters = array(
			'scope'      => 'member',
			'scope_id'   => $member->get('id'),
			//'created_by' => $member->get('id'),
			'state'      => 1,
			'access'     => User::getAuthorisedViewLevels()
		);

		if (User::get('id') == $member->get('id'))
		{
			$filters['authorized'] = true;
		}

		// Get an entry count
		$arr['metadata']['count'] = $this->model->entries($filters)->count();

		return $arr;
	}

	/**
	 * Parse an SEF URL into its component bits
	 * stripping out the path leading up to the blog plugin
	 *
	 * @return  string
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

			$blog = '/members/' . $this->member->get('id') . '/' . $this->_name;

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
				if ($bit == $this->_name && !$start)
				{
					$start = true;
					continue;
				}
				if ($start)
				{
					$paths[] = preg_replace('/[^a-zA-Z0-9_\-\:]/', '', $bit);
				}
			}
			/*if (count($paths) >= 1)
			{
				//array_shift($paths);  // Remove member ID
				array_shift($paths);  // Remove 'blog'
			}*/
			$path = $paths;
		}

		return $path;
	}

	/**
	 * Display a list of latest blog entries
	 *
	 * @return  string
	 */
	private function _browse()
	{
		// Filters for returning results
		$filters = array(
			'year'       => Request::getInt('year', 0),
			'month'      => Request::getInt('month', 0),
			'scope'      => 'member',
			'scope_id'   => $this->member->get('id'),
			'search'     => Request::getVar('search',''),
			'authorized' => false,
			'state'      => 1,
			'access'     => User::getAuthorisedViewLevels()
		);

		// See what information we can get from the path
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

		if (User::get('id') == $this->member->get('id'))
		{
			$filters['authorized'] = $this->member->get('id');
		}

		$view = $this->view('default', 'browse')
			->set('option', $this->option)
			->set('member', $this->member)
			->set('task', $this->task)
			->set('config', $this->params)
			->set('archive', $this->model)
			->set('filters', $filters)
			->setErrors($this->getErrors());

		return $view->loadTemplate();
	}

	/**
	 * Display an RSS feed of latest entries
	 *
	 * @return  string
	 */
	private function _feed()
	{
		if (!$this->params->get('feeds_enabled', 1))
		{
			return $this->_browse();
		}

		include_once(PATH_CORE . DS . 'libraries' . DS . 'joomla' . DS . 'document' . DS . 'feed' . DS . 'feed.php');

		// Set the mime encoding for the document
		Document::setType('feed');

		// Start a new feed object
		$doc = Document::instance();
		$doc->link = Route::url($this->member->link() . '&active=' . $this->_name);

		// Filters for returning results
		$filters = array(
			'limit'      => Request::getInt('limit', Config::get('list_limit')),
			'start'      => Request::getInt('limitstart', 0),
			'year'       => Request::getInt('year', 0),
			'month'      => Request::getInt('month', 0),
			'scope'      => 'member',
			'scope_id'   => $this->member->get('id'),
			'search'     => Request::getVar('search',''),
			//'created_by' => $this->member->get('id')
			'state'      => 1,
			'access'     => User::getAuthorisedViewLevels()
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
		$doc->title       = Config::get('sitename') . ' - ' . stripslashes($this->member->get('name')) . ': ' . Lang::txt('Blog');
		$doc->description = Lang::txt('PLG_MEMBERS_BLOG_RSS_DESCRIPTION', Config::get('sitename'),stripslashes($this->member->get('name')));
		$doc->copyright   = Lang::txt('PLG_MEMBERS_BLOG_RSS_COPYRIGHT', date("Y"), Config::get('sitename'));
		$doc->category    = Lang::txt('PLG_MEMBERS_BLOG_RSS_CATEGORY');

		$filters['state'] = 'public';

		$rows = $this->model->entries($filters)
			->ordered()
			->paginated()
			->rows();

		// Start outputing results if any found
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

		// Output the feed
		echo $doc->render();
	}

	/**
	 * Display a blog entry
	 *
	 * @return  string
	 */
	private function _entry()
	{
		if (isset($this->entry) && is_object($this->entry))
		{
			$row = $this->entry;
		}
		else
		{
			$path = Request::path();
			$alias = '';
			if (strstr($path, '/'))
			{
				$bits = $this->_parseUrl();

				$alias = end($bits);
			}

			$row = \Components\Blog\Models\Entry::oneByScope(
				$alias,
				$this->model->get('scope'),
				$this->model->get('scope_id')
			);
		}

		if (!$row->get('id'))
		{
			App::abort(404, Lang::txt('PLG_MEMBERS_BLOG_NO_ENTRY_FOUND'));
		}

		// Check authorization
		if (($row->get('access') == 2 && User::isGuest())
		 || ($row->get('state') == 0 && User::get('id') != $this->member->get('id')))
		{
			App::abort(403, Lang::txt('PLG_MEMBERS_BLOG_NOT_AUTH'));
		}

		// Filters for returning results
		$filters = array(
			'limit'      => 10,
			'start'      => 0,
			'scope'      => 'member',
			'scope_id'   => $this->member->get('id'),
			'authorized' => false
		);

		if (User::get('id') != $this->member->get('id'))
		{
			$filters['state']  = 1;
			$filters['access'] = User::getAuthorisedViewLevels();
		}

		$view = $this->view('default', 'entry')
			->set('option', $this->option)
			->set('member', $this->member)
			->set('task', $this->task)
			->set('config', $this->params)
			->set('archive', $this->model)
			->set('row', $row)
			->set('filters', $filters)
			->setErrors($this->getErrors());

		return $view->loadTemplate();
	}

	/**
	 * Display a warning message
	 *
	 * @return  string
	 */
	private function _login()
	{
		$return = base64_encode(Route::url($this->member->link() . '&active=' . $this->_name, false, true));

		App::redirect(
			Route::url('index.php?option=com_users&view=login&return=' . $return, false),
			Lang::txt('MEMBERS_LOGIN_NOTICE')
		);
	}

	/**
	 * Display a form for creating an entry
	 *
	 * @return  string
	 */
	private function _new()
	{
		return $this->_edit();
	}

	/**
	 * Display a form for editing an entry
	 *
	 * @param   object  $entry
	 * @return  string
	 */
	private function _edit($entry = null)
	{
		// Login check
		if (User::isGuest())
		{
			return $this->_login();
		}

		if (User::get('id') != $this->member->get('id'))
		{
			$this->setError(Lang::txt('PLG_MEMBERS_BLOG_NOT_AUTHORIZED'));

			return $this->_browse();
		}

		// Load the entry
		if (!is_object($entry))
		{
			$entry = \Components\Blog\Models\Entry::oneOrNew(Request::getInt('entry', 0));
		}

		// Does it exist?
		if ($entry->isNew())
		{
			// Set some defaults
			$entry->set('allow_comments', 1);
			$entry->set('state', 1);
			$entry->set('scope', 'member');
			$entry->set('scope_id', $this->member->get('id'));
			$entry->set('created_by', $this->member->get('id'));
		}

		// Render view
		$view = $this->view('default', 'edit')
			->set('option', $this->option)
			->set('member', $this->member)
			->set('task', $this->task)
			->set('config', $this->params)
			->set('entry', $entry)
			->setErrors($this->getErrors());

		return $view->loadTemplate();
	}

	/**
	 * Save an entry
	 *
	 * @return  void
	 */
	private function _save()
	{
		// Login check
		if (User::isGuest())
		{
			return $this->_login();
		}

		if (User::get('id') != $this->member->get('id'))
		{
			$this->setError(Lang::txt('PLG_MEMBERS_BLOG_NOT_AUTHORIZED'));

			return $this->_browse();
		}

		// Check for request forgeries
		Request::checkToken();

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
		$row = \Components\Blog\Models\Entry::oneOrNew($entry['id'])->set($entry);

		// Store new content
		if (!$row->save())
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

		// Log activity
		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => ($entry['id'] ? 'updated' : 'created'),
				'scope'       => 'blog.entry',
				'scope_id'    => $row->get('id'),
				'description' => Lang::txt('PLG_MEMBERS_BLOG_ACTIVITY_ENTRY_' . ($entry['id'] ? 'UPDATED' : 'CREATED'), '<a href="' . Route::url($row->link()) . '">' . $row->get('title') . '</a>'),
				'details'     => array(
					'title' => $row->get('title'),
					'url'   => Route::url($row->link())
				)
			],
			'recipients' => [
				$this->member->get('id')
			]
		]);

		App::redirect(Route::url($row->link()));
	}

	/**
	 * Delete an entry
	 *
	 * @return  string
	 */
	private function _delete()
	{
		if (User::isGuest())
		{
			return $this->_login();
		}

		if (User::get('id') != $this->member->get('id'))
		{
			$this->setError(Lang::txt('PLG_MEMBERS_BLOG_NOT_AUTHORIZED'));
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
		$entry = \Components\Blog\Models\Entry::oneOrFail($id);

		// Did they confirm delete?
		if (!$process || !$confirmdel)
		{
			if ($process && !$confirmdel)
			{
				$this->setError(Lang::txt('PLG_MEMBERS_BLOG_ERROR_CONFIRM_DELETION'));
			}

			// Output HTML
			$view = $this->view('default', 'delete')
				->set('option', $this->option)
				->set('member', $this->member)
				->set('task', $this->task)
				->set('config', $this->params)
				->set('entry', $entry)
				->set('authorized', true)
				->setErrors($this->getErrors());

			return $view->loadTemplate();
		}

		// Delete the entry itself
		$entry->set('state', $entry::STATE_DELETED);

		if (!$entry->save())
		{
			$this->setError($entry->getError());
		}

		// Log the activity
		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => 'deleted',
				'scope'       => 'blog.entry',
				'scope_id'    => $id,
				'description' => Lang::txt('PLG_MEMBERS_BLOG_ACTIVITY_ENTRY_DELETED', '<a href="' . Route::url($entry->link()) . '">' . $entry->get('title') . '</a>'),
				'details'     => array(
					'title' => $entry->get('title'),
					'url'   => Route::url($entry->link())
				)
			],
			'recipients' => [
				$this->member->get('id')
			]
		]);

		// Return the topics list
		App::redirect(Route::url($this->member->link() . '&active=' . $this->_name));
	}

	/**
	 * Save a comment
	 *
	 * @return  string
	 */
	private function _savecomment()
	{
		// Ensure the user is logged in
		if (User::isGuest())
		{
			return $this->_login();
		}

		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$data = Request::getVar('comment', array(), 'post', 'none', 2);

		// Instantiate a new comment object and pass it the data
		$comment = \Components\Blog\Models\Comment::oneOrNew($data['id'])->set($data);

		// Store new content
		if (!$comment->save())
		{
			$this->setError($comment->getError());
			return $this->_entry();
		}

		// Log the activity
		$entry = \Components\Blog\Models\Entry::oneOrFail($comment->get('entry_id'));

		$recipients = array($comment->get('created_by'));
		if ($comment->get('created_by') != $entry->get('created_by'))
		{
			$recipients[] = $entry->get('created_by');
		}
		if ($comment->get('parent'))
		{
			$recipients[] = $comment->parent()->get('created_by');
		}

		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => ($data['id'] ? 'updated' : 'created'),
				'scope'       => 'blog.entry.comment',
				'scope_id'    => $comment->get('id'),
				'description' => Lang::txt('PLG_MEMBERS_BLOG_ACTIVITY_COMMENT_' . ($data['id'] ? 'UPDATED' : 'CREATED'), $comment->get('id'), '<a href="' . Route::url($entry->link() . '#c' . $comment->get('id')) . '">' . $entry->get('title') . '</a>'),
				'details'     => array(
					'title'    => $entry->get('title'),
					'entry_id' => $entry->get('id'),
					'url'      => $entry->link() . '#c' . $comment->get('id')
				)
			],
			'recipients' => $recipients
		]);

		return $this->_entry();
	}

	/**
	 * Delete a comment
	 *
	 * @return  string
	 */
	private function _deletecomment()
	{
		// Ensure the user is logged in
		if (User::isGuest())
		{
			return $this->_login();
		}

		// Incoming
		$id = Request::getInt('comment', 0);

		if (!$id)
		{
			return $this->_entry();
		}

		// Initiate a blog comment object
		$comment = \Components\Blog\Models\Comment::oneOrFail($id);

		// Delete all comments on an entry
		$comment->set('state', $comment::STATE_DELETED);

		// Delete the entry itself
		if (!$comment->save())
		{
			$this->setError($comment->getError());
		}

		// Log the activity
		$recipients = array($comment->get('created_by'));
		if ($comment->get('created_by') != $this->member->get('id'))
		{
			$recipients[] = $this->member->get('id');
		}

		$entry = \Components\Blog\Models\Entry::oneOrFail($comment->get('entry_id'));

		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => 'deleted',
				'scope'       => 'blog.entry.comment',
				'scope_id'    => $comment->get('id'),
				'description' => Lang::txt('PLG_MEMBERS_BLOG_ACTIVITY_COMMENT_DELETED', $comment->get('id'), '<a href="' . Route::url($entry->link()) . '">' . $entry->get('title') . '</a>'),
				'details'     => array(
					'title'    => $entry->get('title'),
					'entry_id' => $entry->get('id'),
					'url'      => $entry->link()
				)
			],
			'recipients' => $recipients
		]);

		// Return the topics list
		return $this->_entry();
	}

	/**
	 * Display blog settings
	 *
	 * @return  string
	 */
	private function _settings()
	{
		if (User::isGuest())
		{
			return $this->_login();
		}

		if (User::get('id') != $this->member->get('id'))
		{
			$this->setError(Lang::txt('PLG_MEMBERS_BLOG_NOT_AUTHORIZED'));

			return $this->_browse();
		}

		$settings = \Hubzero\Plugin\Params::oneByPlugin(
			$this->member->get('id'),
			'members',
			$this->_name
		);

		// Output HTML
		$view = $this->view('default', 'settings')
			->set('option', $this->option)
			->set('member', $this->member)
			->set('task', $this->task)
			->set('config', $this->params)
			->set('settings', $settings)
			->setErrors($this->getErrors());

		return $view->loadTemplate();
	}

	/**
	 * Save blog settings
	 *
	 * @return  void
	 */
	private function _savesettings()
	{
		if (User::isGuest())
		{
			return $this->_login();
		}

		if (User::get('id') != $this->member->get('id'))
		{
			$this->setError(Lang::txt('PLG_MEMBERS_BLOG_NOT_AUTHORIZED'));

			return $this->_browse();
		}

		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$settings = Request::getVar('settings', array(), 'post');

		$row = \Hubzero\Plugin\Params::blank()->set($settings);

		$p = new \Hubzero\Config\Registry(Request::getVar('params', array(), 'post'));

		$row->set('params', $p->toString());

		// Store new content
		if (!$row->save())
		{
			$this->setError($row->getError());
			return $this->_settings();
		}

		// Log the activity
		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => 'updated',
				'scope'       => 'blog.settings',
				'scope_id'    => $row->get('id'),
				'description' => Lang::txt('PLG_MEMBERS_BLOG_ACTIVITY_SETTINGS_UPDATED')
			],
			'recipients' => [
				$this->member->get('id')
			]
		]);

		App::redirect(
			Route::url($this->member->link() . '&active=' . $this->_name . '&task=settings'),
			Lang::txt('PLG_MEMBERS_BLOG_SETTINGS_SAVED')
		);
	}

	/**
	 * Utility method to act on a user after it has been saved.
	 *
	 * This method marks blog posts as "trashed" if an account has
	 * been disabled and/or marked as spam.
	 *
	 * @param   array    $user     Holds the new user data.
	 * @param   boolean  $isnew    True if a new user is stored.
	 * @param   boolean  $success  True if user was succesfully stored in the database.
	 * @param   string   $msg      Message.
	 * @return  void
	 */
	public function onMemberAfterSave($user, $isnew, $success, $msg)
	{
		if (!$success)
		{
			return false;
		}

		// New user = shouldn't be anything to do here
		if ($isnew)
		{
			return true;
		}

		// If the user was blocked and account not approved
		// OR email address starts with SPAM_
		if (($user['block'] && !$user['approved'])
		 || substr($user['email'], 0, strlen('SPAM_')) == 'SPAM_')
		{
			try
			{
				// Mark all content as trashed
				include_once(PATH_CORE . DS . 'components' . DS . 'com_blog' . DS . 'models' . DS . 'archive.php');

				$entries = \Components\Blog\Models\Entry::all()
					->whereEquals('created_by', $user['id'])
					->rows();

				foreach ($entries as $entry)
				{
					$entry->set('state', 2);

					if (!$entry->save())
					{
						throw new Exception($entry->getError());
					}
				}
			}
			catch (Exception $e)
			{
				return false;
			}
		}
	}

	/**
	 * Remove all user blog entries for the given user ID
	 *
	 * Method is called after user data is deleted from the database
	 *
	 * @param   array    $user     Holds the user data
	 * @param   boolean  $success  True if user was succesfully stored in the database
	 * @param   string   $msg      Message
	 * @return  boolean
	 */
	public function onMemberAfterDelete($user, $success, $msg)
	{
		if (!$success)
		{
			return false;
		}

		$userId = \Hubzero\Utility\Arr::getValue($user, 'id', 0, 'int');

		if ($userId)
		{
			try
			{
				include_once(PATH_CORE . DS . 'components' . DS . 'com_blog' . DS . 'models' . DS . 'archive.php');

				$entries = \Components\Blog\Models\Entry::all()
					->whereEquals('created_by', $user['id'])
					->rows();

				foreach ($entries as $entry)
				{
					if (!$entry->destroy())
					{
						throw new Exception($entry->getError());
					}
				}
			}
			catch (Exception $e)
			{
				return false;
			}
		}

		return true;
	}
}
