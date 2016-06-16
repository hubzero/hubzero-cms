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

namespace Components\Forum\Site\Controllers;

use Hubzero\Component\SiteController;
use Hubzero\Utility\String;
use Components\Forum\Models\Manager;
use Components\Forum\Models\Category;
use Components\Forum\Models\Thread;
use Components\Forum\Models\Post;
use Components\Forum\Tables;
use Exception;
use Filesystem;
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
	 * @return	void
	 */
	public function execute()
	{
		$this->model = new Manager('site', 0);

		$this->registerTask('latest', 'feed');
		$this->registerTask('latest', 'feed.rss');
		$this->registerTask('latest', 'latest.rss');

		parent::execute();
	}

	/**
	 * Method to set the document path
	 *
	 * @return	void
	 */
	protected function _buildPathway()
	{
		if (Pathway::count() <= 0)
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_option)),
				'index.php?option=' . $this->_option
			);
		}
		if (isset($this->view->section))
		{
			Pathway::append(
				String::truncate(stripslashes($this->view->section->get('title')), 100, array('exact' => true)),
				'index.php?option=' . $this->_option . '&section=' . $this->view->section->get('alias')
			);
		}
		if (isset($this->view->category))
		{
			Pathway::append(
				String::truncate(stripslashes($this->view->category->get('title')), 100, array('exact' => true)),
				'index.php?option=' . $this->_option . '&section=' . $this->view->section->get('alias') . '&category=' . $this->view->category->get('alias')
			);
		}
		if (isset($this->view->thread) && $this->view->thread->exists())
		{
			Pathway::append(
				'#' . $this->view->thread->get('id') . ' - ' . String::truncate(stripslashes($this->view->thread->get('title')), 100, array('exact' => true)),
				'index.php?option=' . $this->_option . '&section=' . $this->view->section->get('alias') . '&category=' . $this->view->category->get('alias') . '&thread=' . $this->view->thread->get('id')
			);
		}
	}

	/**
	 * Method to build and set the document title
	 *
	 * @return	void
	 */
	protected function _buildTitle()
	{
		$this->_title = Lang::txt(strtoupper($this->_option));
		if (isset($this->view->section))
		{
			$this->_title .= ': ' . String::truncate(stripslashes($this->view->section->get('title')), 100, array('exact' => true));
		}
		if (isset($this->view->category))
		{
			$this->_title .= ': ' . String::truncate(stripslashes($this->view->category->get('title')), 100, array('exact' => true));
		}
		if (isset($this->view->thread) && $this->view->thread->exists())
		{
			$this->_title .= ': #' . $this->view->thread->get('id') . ' - ' . String::truncate(stripslashes($this->view->thread->get('title')), 100, array('exact' => true));
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
		$this->view->title = Lang::txt('COM_FORUM');

		// Incoming
		$this->view->filters = array(
			'limit'    => Request::getInt('limit', 25),
			'start'    => Request::getInt('limitstart', 0),
			'section'  => Request::getVar('section', ''),
			'category' => Request::getCmd('category', ''),
			'parent'   => Request::getInt('thread', 0),
			'state'    => 1
		);

		$this->view->section  = $this->model->section($this->view->filters['section'], $this->model->get('scope'), $this->model->get('scope_id'));
		if (!$this->view->section->exists())
		{
			throw new Exception(Lang::txt('COM_FORUM_SECTION_NOT_FOUND'), 404);
		}

		$this->view->category = $this->view->section->category($this->view->filters['category']);
		if (!$this->view->category->exists())
		{
			throw new Exception(Lang::txt('COM_FORUM_CATEGORY_NOT_FOUND'), 404);
		}

		$this->view->filters['category_id'] = $this->view->category->get('id');

		// Load the topic
		$this->view->thread = $this->view->category->thread($this->view->filters['parent']);

		// Check logged in status
		if ($this->view->thread->get('access') > 0 && User::isGuest())
		{
			$return = base64_encode(Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&section=' . $this->view->filters['section'] . '&category=' . $this->view->filters['category'] . '&thread=' . $this->view->filters['parent'], false, true));
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . $return)
			);
			return;
		}

		$this->view->filters['state'] = array(1, 3);

		// Get authorization
		$this->_authorize('category', $this->view->category->get('id'));
		$this->_authorize('thread', $this->view->thread->get('id'));
		$this->_authorize('post');

		$this->view->config = $this->config;
		$this->view->model  = $this->model;

		$this->view->notifications = \Notify::messages('forum');

		// Set the page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		$this->view->display();
	}

	/**
	 * Show a form for creating a new entry
	 *
	 * @return  void
	 */
	public function latestTask()
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
	}

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

		$this->view->section  = $this->model->section($section, $this->model->get('scope'), $this->model->get('scope_id'));
		if (!$this->view->section->exists())
		{
			throw new Exception(Lang::txt('COM_FORUM_SECTION_NOT_FOUND'), 404);
		}

		$this->view->category = $this->view->section->category($category);
		if (!$this->view->category->exists())
		{
			throw new Exception(Lang::txt('COM_FORUM_CATEGORY_NOT_FOUND'), 404);
		}

		// Incoming
		if (is_object($post))
		{
			$this->view->post = $post;
		}
		else
		{
			$this->view->post = new Thread($id);
		}

		$this->_authorize('thread', $id);

		if (!$id)
		{
			$this->view->post->set('scope', $this->model->get('scope'));
			$this->view->post->set('created_by', User::get('id'));
		}
		elseif ($this->view->post->get('created_by') != User::get('id') && !$this->config->get('access-edit-thread'))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&section=' . $section . '&category=' . $category),
				Lang::txt('COM_FORUM_NOT_AUTHORIZED'),
				'warning'
			);
			return;
		}

		// Set the page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		$this->view->config = $this->config;
		$this->view->model  = $this->model;

		$this->view->notifications = \Notify::messages('forum');

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		$this->view
			->setLayout('edit')
			->display();
	}

	/**
	 * Save an entry
	 *
	 * @return     void
	 */
	public function saveTask()
	{
		if (User::isGuest())
		{
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . base64_encode(Route::url('index.php?option=' . $this->_option)))
			);
			return;
		}

		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$section = Request::getVar('section', '');

		$fields = Request::getVar('fields', array(), 'post', 'none', 2);
		$fields = array_map('trim', $fields);

		$assetType = 'thread';
		if ($fields['parent'])
		{
			$assetType = 'post';
		}

		if ($fields['id'])
		{
			$old = new Post(intval($fields['id']));
			if ($old->get('created_by') == User::get('id'))
			{
				$this->config->set('access-edit-' . $assetType, true);
			}
		}

		$this->_authorize($assetType, intval($fields['id']));
		if (!$this->config->get('access-edit-' . $assetType) && !$this->config->get('access-create-' . $assetType))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option)
			);
			return;
		}

		$fields['sticky']    = (isset($fields['sticky']))    ? $fields['sticky']    : 0;
		$fields['closed']    = (isset($fields['closed']))    ? $fields['closed']    : 0;
		$fields['anonymous'] = (isset($fields['anonymous'])) ? $fields['anonymous'] : 0;

		// Bind data
		$model = new Post($fields['id']);
		if ($model->get('parent'))
		{
			$fields['thread'] = isset($fields['thread']) ? $fields['thread'] : $model->get('parent');
			$thread = new Thread($fields['thread']);
			if (!$thread->exists() || $thread->get('closed'))
			{
				Notify::error(Lang::txt('COM_FORUM_ERROR_THREAD_CLOSED'), 'forum');
				$this->editTask($model);
				return;
			}
		}
		if (!$model->bind($fields))
		{
			Notify::error($model->getError(), 'forum');
			$this->editTask($model);
			return;
		}

		// Store new content
		if (!$model->store(true))
		{
			Notify::error($model->getError(), 'forum');
			$this->editTask($model);
			return;
		}

		$parent = $model->get('thread', $model->get('id'));

		// Upload files
		$this->uploadTask($parent, $model->get('id'));

		// Save tags
		$model->tag(Request::getVar('tags', '', 'post'), User::get('id'));

		// Determine message
		if (!$fields['id'])
		{
			if (!$fields['parent'])
			{
				$message = Lang::txt('COM_FORUM_THREAD_STARTED');
			}
			else
			{
				$message = Lang::txt('COM_FORUM_POST_ADDED');
			}
		}
		else
		{
			$message = ($model->get('modified_by')) ? Lang::txt('COM_FORUM_POST_EDITED') : Lang::txt('COM_FORUM_POST_ADDED');
		}

		$category = new Category($model->get('category_id'));

		// Set the redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&section=' . $section . '&category=' . $category->get('alias') . '&thread=' . $parent . '#c' . $model->get('id')),
			$message,
			'message'
		);
	}

	/**
	 * Delete an entry
	 *
	 * @return     void
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
		$model = new Tables\Post($this->database);
		$model->load($id);

		// Make the sure the category exist
		if (!$model->id)
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

		// Update replies if this is a parent (thread starter)
		if (!$model->parent)
		{
			if (!$model->updateReplies(array('state' => 2), $model->id))  /* 0 = unpublished, 1 = published, 2 = deleted */
			{
				$this->setError($model->getError());
			}
		}

		// Delete the topic itself
		$model->state = 2;  /* 0 = unpublished, 1 = published, 2 = deleted */
		if (!$model->store())
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&section=' . $section . '&category=' . $category),
				$model->getError(),
				'error'
			);
			return;
		}

		// Delete the attachment associated with the post
		$this->markForDelete($id);

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
		$section  = Request::getVar('section', '');
		$category = Request::getVar('category', '');
		$thread   = Request::getInt('thread', 0);
		$post     = Request::getInt('post', 0);
		$file     = Request::getVar('file', '');

		// Ensure we have a database object
		if (!$this->database)
		{
			throw new Exception(Lang::txt('COM_FORUM_DATABASE_NOT_FOUND'), 500);
		}

		// Instantiate an attachment object
		$attach = new Tables\Attachment($this->database);
		if (!$post)
		{
			$attach->loadByThread($thread, $file);
		}
		else
		{
			$attach->loadByPost($post);
		}

		if (!$attach->filename)
		{
			throw new Exception(Lang::txt('COM_FORUM_FILE_NOT_FOUND'), 404);
		}
		$file = $attach->filename;

		// Get the parent ticket the file is attached to
		$row = new Tables\Post($this->database);
		$row->load($attach->post_id);

		if (!$row->id)
		{
			throw new Exception(Lang::txt('COM_FORUM_POST_NOT_FOUND'), 404);
		}

		// Check logged in status
		if ($row->access > 0 && User::isGuest())
		{
			$return = base64_encode(Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&section=' . $section . '&category=' . $category . '&thread=' . $thread . '&post=' . $post . '&file=' . $file));
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . $return)
			);
			return;
		}

		// Load ACL
		$this->_authorize('thread', $row->id);

		// Ensure the user is authorized to view this file
		if (!$this->config->get('access-view-thread'))
		{
			throw new Exception(Lang::txt('COM_FORUM_NOT_AUTH_FILE'), 403);
		}

		// Ensure we have a path
		if (empty($file))
		{
			throw new Exception(Lang::txt('COM_FORUM_FILE_NOT_FOUND'), 404);
		}

		// Get the configured upload path
		$basePath  = DS . trim($this->config->get('webpath', '/site/forum'), DS) . DS  . $attach->parent . DS . $attach->post_id;

		// Does the path start with a slash?
		if (substr($file, 0, 1) != DS)
		{
			$file = DS . $file;
			// Does the beginning of the $attachment->filename match the config path?
			if (substr($file, 0, strlen($basePath)) == $basePath)
			{
				// Yes - this means the full path got saved at some point
			}
			else
			{
				// No - append it
				$file = $basePath . $file;
			}
		}

		// Add PATH_CORE
		$filename = PATH_APP . $file;

		// Ensure the file exist
		if (!file_exists($filename))
		{
			throw new Exception(Lang::txt('COM_FORUM_FILE_NOT_FOUND') . ' ' . $filename, 404);
		}

		// Initiate a new content server and serve up the file
		$server = new \Hubzero\Content\Server();
		$server->filename($filename);
		$server->disposition('inline');
		$server->acceptranges(false); // @TODO fix byte range support

		if (!$server->serve())
		{
			// Should only get here on error
			throw new Exception(Lang::txt('COM_FORUM_SERVER_ERROR'), 500);
		}
		else
		{
			exit;
		}
		return;
	}

	/**
	 * Uploads a file to a given directory and returns an attachment string
	 * that is appended to report/comment bodies
	 *
	 * @param   string  $listdir  Directory to upload files to
	 * @return  string  A string that gets appended to messages
	 */
	public function uploadTask($listdir, $post_id)
	{
		// Check if they are logged in
		if (User::isGuest())
		{
			return;
		}

		if (!$listdir)
		{
			$this->setError(Lang::txt('COM_FORUM_NO_UPLOAD_DIRECTORY'));
			return;
		}

		$row = new Tables\Attachment($this->database);
		$row->load(Request::getInt('attachment', 0));
		$row->description = trim(Request::getVar('description', ''));
		$row->post_id = $post_id;
		$row->parent = $listdir;

		// Incoming file
		$file = Request::getVar('upload', '', 'files', 'array');
		if (!$file['name'])
		{
			// This means we're just updating the file description
			if ($row->id)
			{
				if (!$row->check())
				{
					$this->setError($row->getError());
				}
				if (!$row->store())
				{
					$this->setError($row->getError());
				}
			}
			return;
		}

		// Construct our file path
		$path = PATH_APP . DS . trim($this->config->get('webpath', '/site/forum'), DS) . DS . $listdir;
		if ($post_id)
		{
			$path .= DS . $post_id;
		}

		// Build the path if it doesn't exist
		if (!is_dir($path))
		{
			if (!Filesystem::makeDirectory($path))
			{
				$this->setError(Lang::txt('COM_FORUM_UNABLE_TO_CREATE_UPLOAD_PATH'));
				return;
			}
		}

		// Make the filename safe
		$file['name'] = Filesystem::clean($file['name']);
		$file['name'] = str_replace(' ', '_', $file['name']);
		$ext = strtolower(Filesystem::extension($file['name']));

		// Perform the upload
		if (!Filesystem::upload($file['tmp_name'], $path . DS . $file['name']))
		{
			$this->setError(Lang::txt('COM_FORUM_ERROR_UPLOADING'));
			return;
		}
		else
		{
			// Perform the upload
			if (!Filesystem::isSafe($path . DS . $file['name']))
			{
				$this->setError(Lang::txt('COM_FORUM_ERROR_UPLOADING'));
				return;
			}

			// Remove previous file
			if ($row->filename)
			{
				if (!Filesystem::delete($path . DS . $row->filename))
				{
					$this->setError(Lang::txt('PLG_GROUPS_FORUM_ERROR_UPLOADING'));
					return;
				}
			}

			// File was uploaded
			// Create database entry
			$row->filename = $file['name'];

			if (!$row->check())
			{
				$this->setError($row->getError());
			}
			if (!$row->store())
			{
				$this->setError($row->getError());
			}
		}
	}

	/**
	 * Marks a file for deletion
	 *
	 * @param   integer  $post_id  The ID of the post which is associated with the attachment
	 * @return  void
	 */
	public function markForDelete($post_id)
	{
		// Check if they are logged in
		if (User::isGuest())
		{
			return;
		}

		// Load attachment object
		$row = new Tables\Attachment($this->database);
		$row->loadByPost($post_id);

		//mark for deletion
		$row->set('status', 2);

		if (!$row->store())
		{
			$this->setError($row->getError());
		}
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