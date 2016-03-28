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

namespace Components\Forum\Site\Controllers;

use Hubzero\Component\SiteController;
use Hubzero\Utility\String;
use Components\Forum\Models\Manager;
use Components\Forum\Models\Section;
use Components\Forum\Models\Category;
use Components\Forum\Models\Post;
use Components\Forum\Models\Attachment;
use Document;
use Pathway;
use Request;
use Notify;
use Config;
use Route;
use User;
use Lang;
use App;

/**
 * Forum controller class for threads
 */
class Threads extends SiteController
{
	/**
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->forum = new Manager('site', 0);

		$this->registerTask('latest', 'feed');
		$this->registerTask('latest', 'feed.rss');
		$this->registerTask('latest', 'latest.rss');

		parent::execute();
	}

	/**
	 * Method to set the document path
	 *
	 * @param   object  $section
	 * @param   object  $category
	 * @param   object  $thread
	 * @return  void
	 */
	protected function buildPathway($section=null, $category=null, $thread=null)
	{
		if (Pathway::count() <= 0)
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_option)),
				'index.php?option=' . $this->_option
			);
		}
		if (isset($section))
		{
			Pathway::append(
				String::truncate(stripslashes($section->get('title')), 100, array('exact' => true)),
				'index.php?option=' . $this->_option . '&section=' . $section->get('alias')
			);
		}
		if (isset($category))
		{
			Pathway::append(
				String::truncate(stripslashes($category->get('title')), 100, array('exact' => true)),
				'index.php?option=' . $this->_option . '&section=' . $section->get('alias') . '&category=' . $category->get('alias')
			);
		}
		if (isset($thread) && $thread->get('id'))
		{
			Pathway::append(
				'#' . $thread->get('id') . ' - ' . String::truncate(stripslashes($thread->get('title')), 100, array('exact' => true)),
				'index.php?option=' . $this->_option . '&section=' . $section->get('alias') . '&category=' . $category->get('alias') . '&thread=' . $thread->get('id')
			);
		}
	}

	/**
	 * Method to build and set the document title
	 *
	 * @param   object  $section
	 * @param   object  $category
	 * @param   object  $thread
	 * @return  void
	 */
	protected function buildTitle($section=null, $category=null, $thread=null)
	{
		$this->_title = Lang::txt(strtoupper($this->_option));
		if (isset($section))
		{
			$this->_title .= ': ' . String::truncate(stripslashes($section->get('title')), 100, array('exact' => true));
		}
		if (isset($category))
		{
			$this->_title .= ': ' . String::truncate(stripslashes($category->get('title')), 100, array('exact' => true));
		}
		if (isset($thread) && $thread->get('id'))
		{
			$this->_title .= ': #' . $thread->get('id') . ' - ' . String::truncate(stripslashes($thread->get('title')), 100, array('exact' => true));
		}

		Document::setTitle($this->_title);
	}

	/**
	 * Display a thread
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Incoming
		$filters = array(
			'limit'    => Request::getInt('limit', 25),
			'start'    => Request::getInt('limitstart', 0),
			'section'  => Request::getVar('section', ''),
			'category' => Request::getCmd('category', ''),
			'thread'   => Request::getInt('thread', 0),
			'state'    => Post::STATE_PUBLISHED,
			'access'   => User::getAuthorisedViewLevels()
		);

		// Section
		$section = Section::all()
			->whereEquals('alias', $filters['section'])
			->whereEquals('scope', $this->forum->get('scope'))
			->whereEquals('scope_id', $this->forum->get('scope_id'))
			->row();
		if (!$section->get('id'))
		{
			App::abort(404, Lang::txt('COM_FORUM_SECTION_NOT_FOUND'));
		}

		// Get the category
		$category = Category::all()
			->whereEquals('alias', $filters['category'])
			->whereEquals('scope', $this->forum->get('scope'))
			->whereEquals('scope_id', $this->forum->get('scope_id'))
			->row();
		if (!$category->get('id'))
		{
			App::abort(404, Lang::txt('COM_FORUM_CATEGORY_NOT_FOUND'));
		}

		$filters['category_id'] = $category->get('id');

		// Load the topic
		$thread = Post::oneOrFail($filters['thread']);

		// Check logged in status
		if (User::isGuest() && !in_array($thread->get('access'), User::getAuthorisedViewLevels()))
		{
			$return = base64_encode(Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&section=' . $this->view->filters['section'] . '&category=' . $this->view->filters['category'] . '&thread=' . $this->view->filters['parent'], false, true));
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . $return)
			);
			return;
		}

		$filters['state'] = array(1, 3);

		// Get authorization
		$this->_authorize('category', $category->get('id'));
		$this->_authorize('thread', $thread->get('id'));
		$this->_authorize('post');

		// Set the page title
		$this->buildTitle($section, $category, $thread);

		// Set the pathway
		$this->buildPathway($section, $category, $thread);

		// Output the view
		$this->view
			->set('config', $this->config)
			->set('forum', $this->forum)
			->set('section', $section)
			->set('category', $category)
			->set('thread', $thread)
			->set('filters', $filters)
			->setErrors($this->getErrors())
			->display();
	}

	/**
	 * Produce a feed of the latest entries
	 *
	 * @return  void
	 */
	/*public function latestTask()
	{
		// Set the mime encoding for the document
		Document::setType('feed');

		// Start a new feed object
		$doc = Document::instance();
		$doc->link = Route::url('index.php?option=' . $this->_option);

		// Paging variables
		$start = Request::getInt('limitstart', 0);
		$limit = Request::getInt('limit', Config::get('list_limit'));

		// Build some basic RSS document information
		$doc->title  = Config::get('sitename') . ' - ' . Lang::txt('COM_FORUM_RSS_TITLE');
		$doc->description = Lang::txt('COM_FORUM_RSS_DESCRIPTION', Config::get('sitename'));
		$doc->copyright   = Lang::txt('COM_FORUM_RSS_COPYRIGHT', date("Y"), Config::get('sitename'));
		$doc->category    = Lang::txt('COM_FORUM_RSS_CATEGORY');

		// get all forum posts on site forum
		$this->database->setQuery("SELECT f.* FROM `#__forum_posts` f WHERE f.scope_id='0' AND scope='site' AND f.state='1'");
		$site_forum = $this->database->loadAssocList();

		// get any group posts
		$this->database->setQuery("SELECT f.* FROM `#__forum_posts` f WHERE f.scope_id<>'0' AND scope='group' AND f.state='1'");
		$group_forum = $this->database->loadAssocList();

		// make sure that the group for each forum post has the right privacy setting
		foreach ($group_forum as $k => $gf)
		{
			$group = \Hubzero\User\Group::getInstance($gf['scope_id']);
			if (is_object($group))
			{
				$forum_access = \Hubzero\User\Group\Helper::getPluginAccess($group, 'forum');

				if ($forum_access == 'nobody'
				 || ($forum_access == 'registered' && User::isGuest())
				 || ($forum_access == 'members' && !in_array(User::get('id'), $group->get('members'))))
				{
					unset($group_forum[$k]);
				}
			}
			else
			{
				unset($group_forum[$k]);
			}
		}

		//based on param decide what to include
		switch ($this->config->get('forum', 'both'))
		{
			case 'site':  $rows = $site_forum;  break;
			case 'group': $rows = $group_forum; break;
			case 'both':
			default:
				$rows = array_merge($site_forum, $group_forum);
			break;
		}

		$categories = array();
		$ids = array();
		foreach ($rows as $post)
		{
			$ids[] = $post['category_id'];
		}
		$this->database->setQuery("SELECT c.id, c.alias, s.alias as section FROM `#__forum_categories` c LEFT JOIN `#__forum_sections` as s ON s.id=c.section_id WHERE c.id IN (" . implode(',', $ids) . ") AND c.state='1'");
		$cats = $this->database->loadObjectList();
		if ($cats)
		{
			foreach ($cats as $category)
			{
				$categories[$category->id] = $category;
			}
		}

		//function to sort by created date
		function sortbydate($a, $b)
		{
			$d1 = date("Y-m-d H:i:s", strtotime($a['created']));
			$d2 = date("Y-m-d H:i:s", strtotime($b['created']));

			return ($d1 > $d2) ? -1 : 1;
		}

		//sort using function above - date desc
		usort($rows, 'sortbydate');

		// Start outputing results if any found
		if (count($rows) > 0)
		{
			foreach ($rows as $row)
			{
				// Prepare the title
				$title = strip_tags(stripslashes($row['title']));
				$title = html_entity_decode($title);

				// Get URL
				if ($row['scope_id'] == 0)
				{
					$link = 'index.php?option=com_forum&section=' . $categories[$row['category_id']]->section . '&category=' . $categories[$row['category_id']]->alias . '&thread=' . ($row['parent'] ? $row['parent'] : $row['id']);
				}
				else
				{
					$group = \Hubzero\User\Group::getInstance($row['scope_id']);
					$link = 'index.php?option=com_groups&gid=' . $group->get('cn') . '&active=forum&scope=' .  $categories[$row['category_id']]->section . '/' . $categories[$row['category_id']]->alias . '/' . ($row['parent'] ? $row['parent'] : $row['id']);
				}
				$link = Route::url($link);
				$link = DS . ltrim($link, DS);

				// Get description
				$description = stripslashes($row['comment']);
				$description = String::truncate($description, 300, 0);

				// Get author
				$user = User::getInstance($row['created_by']);
				$author = stripslashes($user->get('name'));

				// Get date
				@$date = ($row->created ? date('r', strtotime($row->created)) : '');

				// Load individual item creator class
				$item = new \Hubzero\Document\Type\Feed\Item();
				$item->title       = $title;
				$item->link        = $link;
				$item->description = $description;
				$item->date        = $date;
				$item->category    = ($row['scope_id'] == 0) ? Lang::txt('COM_FORUM') : stripslashes($group->get('description'));
				$item->author      = $author;

				// Loads item info into rss array
				$doc->addItem($item);
			}
		}
	}*/

	/**
	 * Show a form for creating a new entry
	 *
	 * @return  void
	 */
	public function newTask()
	{
		$this->editTask();
	}

	/**
	 * Show a form for editing an entry
	 *
	 * @param   mixed  $post
	 * @return  void
	 */
	public function editTask($post=null)
	{
		$id       = Request::getInt('thread', 0);
		$category = Request::getCmd('category', '');
		$section  = Request::getVar('section', '');

		if (User::isGuest())
		{
			$return = Route::url('index.php?option=' . $this->_option . '&section=' . $section . '&category=' . $category . '&task=new');
			if ($id)
			{
				$return = Route::url('index.php?option=' . $this->_option . '&section=' . $section . '&category=' . $category . '&thread=' . $id . '&task=edit');
			}
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . base64_encode($return)) . Lang::txt('COM_FORUM_LOGIN_NOTICE'),
				'warning'
			);
			return;
		}

		// Section
		$section = Section::all()
			->whereEquals('alias', $section)
			->whereEquals('scope', $this->forum->get('scope'))
			->whereEquals('scope_id', $this->forum->get('scope_id'))
			->row();
		if (!$section->get('id'))
		{
			App::abort(404, Lang::txt('COM_FORUM_SECTION_NOT_FOUND'));
		}

		// Get the category
		$category = Category::all()
			->whereEquals('alias', $category)
			->whereEquals('scope', $this->forum->get('scope'))
			->whereEquals('scope_id', $this->forum->get('scope_id'))
			->row();
		if (!$category->get('id'))
		{
			App::abort(404, Lang::txt('COM_FORUM_CATEGORY_NOT_FOUND'));
		}

		// Incoming
		if (!is_object($post))
		{
			$post = Post::oneOrNew($id);
		}

		$this->_authorize('thread', $id);

		if ($post->isNew())
		{
			$post->set('scope', $this->forum->get('scope'));
			$post->set('created_by', User::get('id'));
		}
		elseif ($post->get('created_by') != User::get('id') && !$this->config->get('access-edit-thread'))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&section=' . $section . '&category=' . $category),
				Lang::txt('COM_FORUM_NOT_AUTHORIZED'),
				'warning'
			);
			return;
		}

		// Set the page title
		$this->buildTitle($section, $category, $post);

		// Set the pathway
		$this->buildPathway($section, $category, $post);

		$this->view
			->set('config', $this->config)
			->set('forum', $this->forum)
			->set('section', $section)
			->set('category', $category)
			->set('post', $post)
			->setErrors($this->getErrors())
			->setLayout('edit')
			->display();
	}

	/**
	 * Save an entry
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		if (User::isGuest())
		{
			$return = Route::url('index.php?option=' . $this->_option, false, true);
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . base64_encode($return))
			);
			return;
		}

		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$section = Request::getVar('section', '');
		$fields  = Request::getVar('fields', array(), 'post', 'none', 2);
		$fields  = array_map('trim', $fields);

		$fields['sticky']    = (isset($fields['sticky']))    ? $fields['sticky']    : 0;
		$fields['closed']    = (isset($fields['closed']))    ? $fields['closed']    : 0;
		$fields['anonymous'] = (isset($fields['anonymous'])) ? $fields['anonymous'] : 0;

		// Instantiate a Post record
		$post = Post::oneOrNew($fields['id']);

		// Set authorization if the current user is the creator
		// of an existing post.
		$assetType = $fields['parent'] ? 'post' : 'thread';

		if ($post->get('id'))
		{
			if ($post->get('created_by') == User::get('id'))
			{
				$this->config->set('access-edit-' . $assetType, true);
			}
		}

		// Authorization check
		$this->_authorize($assetType, intval($fields['id']));

		if (!$this->config->get('access-edit-' . $assetType)
		 && !$this->config->get('access-create-' . $assetType))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option)
			);
		}

		// Bind data
		$post->set($fields);

		// Make sure the thread exists and is accepting new posts
		if ($post->get('parent') && isset($fields['thread']))
		{
			$thread = Post::oneOrFail($fields['thread']);

			if (!$thread->get('id') || $thread->get('closed'))
			{
				Notify::error(Lang::txt('COM_FORUM_ERROR_THREAD_CLOSED'));
				return $this->editTask($post);
			}
		}

		// Make sure the category exists and is accepting new posts
		$category = Category::oneOrFail($post->get('category_id'));

		if ($category->get('closed'))
		{
			Notify::error(Lang::txt('COM_FORUM_ERROR_CATEGORY_CLOSED'));
			return $this->editTask($post);
		}

		// Store new content
		if (!$post->save())
		{
			Notify::error($post->getError());
			return $this->editTask($post);
		}

		// Upload files
		if (!$this->uploadTask($post->get('thread', $post->get('id')), $post->get('id')))
		{
			Notify::error($this->getError());
			return $this->editTask($post);
		}

		// Save tags
		$post->tag(Request::getVar('tags', '', 'post'), User::get('id'));

		// Determine message
		if (!$fields['id'])
		{
			$message = Lang::txt('COM_FORUM_POST_ADDED');

			if (!$fields['parent'])
			{
				$message = Lang::txt('COM_FORUM_THREAD_STARTED');
			}
		}
		else
		{
			$message = ($post->get('modified_by')) ? Lang::txt('COM_FORUM_POST_EDITED') : Lang::txt('COM_FORUM_POST_ADDED');
		}

		$url = 'index.php?option=' . $this->_option . '&section=' . $section . '&category=' . $category->get('alias') . '&thread=' . $post->get('thread') . '#c' . $post->get('id');

		// Record the activity
		$recipients = array(
			['forum.site', 1],
			['forum.section', $category->get('section_id')],
			['user', $post->get('created_by')]
		);
		$type = 'thread';
		$desc = Lang::txt(
			'COM_FORUM_ACTIVITY_' . strtoupper($type) . '_' . ($fields['id'] ? 'UPDATED' : 'CREATED'),
			'<a href="' . Route::url($url) . '">' . $post->get('title') . '</a>'
		);
		// If this is a post in a thread and not the thread starter...
		if ($post->get('parent'))
		{
			$thread = isset($thread) ? $thread : Post::oneOrFail($post->get('thread'));
			$thread->set('last_activity', ($fields['id'] ? $post->get('modified') : $post->get('created')));
			$thread->save();

			$type = 'post';
			$desc = Lang::txt(
				'COM_FORUM_ACTIVITY_' . strtoupper($type) . '_' . ($fields['id'] ? 'UPDATED' : 'CREATED'),
				$post->get('id'),
				'<a href="' . Route::url($url) . '">' . $thread->get('title') . '</a>'
			);

			// If the parent post is not the same as the
			// thread starter (i.e., this is a reply)
			if ($post->get('parent') != $post->get('thread'))
			{
				$parent = Post::oneOrFail($post->get('parent'));
				$recipients[] = ['user', $parent->get('created_by')];
			}
		}

		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => ($fields['id'] ? 'updated' : 'created'),
				'scope'       => 'forum.' . $type,
				'scope_id'    => $post->get('id'),
				'description' => $desc,
				'details'     => array(
					'thread' => $post->get('thread'),
					'url'    => Route::url($url)
				)
			],
			'recipients' => $recipients
		]);

		// Set the redirect
		App::redirect(
			Route::url($url),
			$message,
			'message'
		);
	}

	/**
	 * Delete an entry
	 *
	 * @return  void
	 */
	public function deleteTask()
	{
		$section  = Request::getVar('section', '');
		$category = Request::getVar('category', '');

		// Is the user logged in?
		if (User::isGuest())
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&section=' . $section . '&category=' . $category),
				Lang::txt('COM_FORUM_LOGIN_NOTICE'),
				'warning'
			);
			return;
		}

		// Incoming
		$id = Request::getInt('thread', 0);

		// Load the post
		$post = Post::oneOrFail($id);

		// Make the sure the category exist
		if (!$post->get('id'))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&section=' . $section . '&category=' . $category),
				Lang::txt('COM_FORUM_MISSING_ID'),
				'error'
			);
			return;
		}

		// Check if user is authorized to delete entries
		$this->_authorize('thread', $id);

		if (!$this->config->get('access-delete-thread'))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&section=' . $section . '&category=' . $category),
				Lang::txt('COM_FORUM_NOT_AUTHORIZED'),
				'warning'
			);
			return;
		}

		// Trash the post
		// Note: this will carry through to all replies
		//       and attachments
		$post->set('state', $post::STATE_DELETED);

		if (!$post->save())
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&section=' . $section . '&category=' . $category),
				$post->getError(),
				'error'
			);
			return;
		}

		// Record the activity
		$type = 'thread';
		$desc = Lang::txt(
			'COM_FORUM_ACTIVITY_' . strtoupper($type) . '_DELETED',
			'<a href="' . Route::url($url) . '">' . $post->get('title') . '</a>'
		);
		if ($post->get('parent'))
		{
			$thread = Post::oneOrFail($post->get('thread'));

			$type = 'post';
			$desc = Lang::txt(
				'COM_FORUM_ACTIVITY_' . strtoupper($type) . '_DELETED',
				$post->get('id'),
				'<a href="' . Route::url($url) . '">' . $thread->get('title') . '</a>'
			);
		}

		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => 'deleted',
				'scope'       => 'forum.' . $type,
				'scope_id'    => $post->get('id'),
				'description' => $desc,
				'details'     => array(
					'thread' => $post->get('thread'),
					'url'    => Route::url($url)
				)
			],
			'recipients' => array(
				['forum.site', 1],
				['user', $post->get('created_by')]
			)
		]);

		// Redirect to main listing
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&section=' . $section . '&category=' . $category),
			Lang::txt('COM_FORUM_THREAD_DELETED'),
			'message'
		);
	}

	/**
	 * Serves up files only after passing access checks
	 *
	 * @return  void
	 */
	public function downloadTask()
	{
		// Incoming
		$section   = Request::getVar('section', '');
		$category  = Request::getVar('category', '');
		$thread_id = Request::getInt('thread', 0);
		$post_id   = Request::getInt('post', 0);
		$file      = Request::getVar('file', '');

		// Instantiate an attachment object
		if (!$post_id)
		{
			$attach = Attachment::oneByThread($thread_id, $file);
		}
		else
		{
			$attach = Attachment::oneByPost($post_id);
		}

		if (!$attach->get('filename'))
		{
			App::abort(404, Lang::txt('COM_FORUM_FILE_NOT_FOUND'));
		}

		// Get the parent ticket the file is attached to
		$post = $attach->post();

		if (!$post->get('id') || $post->get('state') == $post::STATE_DELETED)
		{
			App::abort(404, ang::txt('COM_FORUM_POST_NOT_FOUND'));
		}

		// Check logged in status
		if (User::isGuest() && !in_array($post->get('access'), User::getAuthorisedViewLevels()))
		{
			$return = base64_encode(Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&section=' . $section . '&category=' . $category . '&thread=' . $thread_id . '&post=' . $post_id . '&file=' . $file));
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . $return)
			);
			return;
		}

		// Load ACL
		$this->_authorize('thread', $post->get('thread'));

		// Ensure the user is authorized to view this file
		if (!$this->config->get('access-view-thread'))
		{
			App::abort(403, Lang::txt('COM_FORUM_NOT_AUTH_FILE'));
		}

		// Get the configured upload path
		$filename = $attach->path();

		// Ensure the file exist
		if (!file_exists($filename))
		{
			App::abort(404, Lang::txt('COM_FORUM_FILE_NOT_FOUND') . ' ' . substr($filename, strlen(PATH_ROOT)));
		}

		// Initiate a new content server and serve up the file
		$server = new \Hubzero\Content\Server();
		$server->filename($filename);
		$server->disposition('inline');
		$server->acceptranges(false); // @TODO fix byte range support

		if (!$server->serve())
		{
			// Should only get here on error
			App::abort(500, Lang::txt('COM_FORUM_SERVER_ERROR'));
		}

		exit;
	}

	/**
	 * Uploads a file to a given directory and returns an attachment string
	 * that is appended to report/comment bodies
	 *
	 * @param   integer  $thread_id  Directory to upload files to
	 * @param   integer  $post_id    Post ID
	 * @return  boolean
	 */
	public function uploadTask($thread_id, $post_id)
	{
		// Check if they are logged in
		if (User::isGuest())
		{
			return false;
		}

		if (!$thread_id)
		{
			$this->setError(Lang::txt('COM_FORUM_NO_UPLOAD_DIRECTORY'));
			return false;
		}

		// Instantiate an attachment record
		$attachment = Attachment::oneOrNew(Request::getInt('attachment', 0));
		$attachment->set('description', trim(Request::getVar('description', '')));
		$attachment->set('parent', $thread_id);
		$attachment->set('post_id', $post_id);
		if ($attachment->isNew())
		{
			$attachment->set('state', Attachment::STATE_PUBLISHED);
		}

		// Incoming file
		$file = Request::getVar('upload', '', 'files', 'array');
		if (!$file || !isset($file['name']) || !$file['name'])
		{
			if ($attachment->get('id'))
			{
				// Only updating the description
				if (!$attachment->save())
				{
					$this->setError($attachment->getError());
					return false;
				}
			}
			return true;
		}

		// Upload file
		if (!$attachment->upload($file['name'], $file['tmp_name']))
		{
			$this->setError($attachment->getError());
		}

		// Save entry
		if (!$attachment->save())
		{
			$this->setError($attachment->getError());
		}

		return true;
	}

	/**
	 * Set access permissions for a user
	 *
	 * @param   string   $assetType
	 * @param   integer  $assetId
	 * @return  void
	 */
	protected function _authorize($assetType='component', $assetId=null)
	{
		$this->config->set('access-view-' . $assetType, true);
		if (!User::isGuest())
		{
			$asset  = $this->_option;
			if ($assetId)
			{
				$asset .= ($assetType != 'component') ? '.' . $assetType : '';
				$asset .= ($assetId) ? '.' . $assetId : '';
			}

			$at = '';
			if ($assetType != 'component')
			{
				$at .= '.' . $assetType;
			}

			// Admin
			$this->config->set('access-admin-' . $assetType, User::authorise('core.admin', $asset));
			$this->config->set('access-manage-' . $assetType, User::authorise('core.manage', $asset));
			// Permissions
			if ($assetType == 'post' || $assetType == 'thread')
			{
				$this->config->set('access-create-' . $assetType, true);
				$val = User::authorise('core.create' . $at, $asset);
				if ($val !== null)
				{
					$this->config->set('access-create-' . $assetType, $val);
				}

				$this->config->set('access-edit-' . $assetType, true);
				$val = User::authorise('core.edit' . $at, $asset);
				if ($val !== null)
				{
					$this->config->set('access-edit-' . $assetType, $val);
				}

				$this->config->set('access-edit-own-' . $assetType, true);
				$val = User::authorise('core.edit.own' . $at, $asset);
				if ($val !== null)
				{
					$this->config->set('access-edit-own-' . $assetType, $val);
				}

				$this->config->set('access-delete-' . $assetType, true);
				$val = User::authorise('core.delete' . $at, $asset);
				if ($val !== null)
				{
					$this->config->set('access-delete-' . $assetType, $val);
				}
			}
			else
			{
				$this->config->set('access-create-' . $assetType, User::authorise('core.create' . $at, $asset));
				$this->config->set('access-edit-' . $assetType, User::authorise('core.edit' . $at, $asset));
				$this->config->set('access-edit-own-' . $assetType, User::authorise('core.edit.own' . $at, $asset));
				$this->config->set('access-delete-' . $assetType, User::authorise('core.delete' . $at, $asset));
			}

			//$this->config->set('access-delete-' . $assetType, User::authorise('core.delete' . $at, $asset));
			$this->config->set('access-edit-state-' . $assetType, User::authorise('core.edit.state' . $at, $asset));
		}
	}
}
