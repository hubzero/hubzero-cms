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

namespace Components\Blog\Site\Controllers;

use Components\Blog\Models\Archive;
use Components\Blog\Models\Comment;
use Components\Blog\Models\Entry;
use Hubzero\Component\SiteController;
use Hubzero\Utility\String;
use Hubzero\Utility\Sanitize;
use Exception;
use Document;
use Request;
use Pathway;
use Lang;
use Route;
use User;
use Date;

/**
 * Blog controller class for entries
 */
class Entries extends SiteController
{
	/**
	 * Determines task being called and attempts to execute it
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->model = new Archive('site', 0);

		$this->_authorize();
		$this->_authorize('entry');
		$this->_authorize('comment');

		$this->registerTask('comments.rss', 'comments');
		$this->registerTask('commentsrss', 'comments');

		$this->registerTask('feed.rss', 'feed');
		$this->registerTask('feedrss', 'feed');

		$this->registerTask('archive', 'display');
		$this->registerTask('new', 'edit');

		parent::execute();
	}

	/**
	 * Display a list of entries
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Filters for returning results
		$filters = array(
			'year'       => Request::getInt('year', 0),
			'month'      => Request::getInt('month', 0),
			'scope'      => $this->config->get('show_from', 'site'),
			'scope_id'   => 0,
			'search'     => Request::getVar('search', ''),
			'authorized' => false,
			'state'      => 1,
			'access'     => User::getAuthorisedViewLevels()
		);

		if ($filters['year'] > date("Y"))
		{
			$filters['year'] = 0;
		}
		if ($filters['month'] > 12)
		{
			$filters['month'] = 0;
		}
		if ($filters['scope'] == 'both')
		{
			$filters['scope'] = '';
		}

		if (!User::isGuest())
		{
			if ($this->config->get('access-manage-component'))
			{
				//$filters['state'] = null;
				$filters['authorized'] = true;
				array_push($filters['access'], 5);
			}
		}

		// Output HTML
		$this->view
			->set('archive', $this->model)
			->set('config', $this->config)
			->set('filters', $filters)
			->display();
	}

	/**
	 * Display an entry
	 *
	 * @return  void
	 */
	public function entryTask()
	{
		$alias = Request::getVar('alias', '');

		if (!$alias)
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller)
			);
			return;
		}

		// Load entry
		$row = Entry::oneByScope(
			$alias,
			$this->model->get('scope'),
			$this->model->get('scope_id')
		);

		if (!$row->get('id'))
		{
			throw new Exception(Lang::txt('COM_BLOG_NOT_FOUND'), 404);
		}

		// Check authorization
		if (!$row->access('view'))
		{
			throw new Exception(Lang::txt('COM_BLOG_NOT_AUTH'), 403);
		}

		// Filters for returning results
		$filters = array(
			'limit'      => 10,
			'start'      => 0,
			'scope'      => 'site',
			'scope_id'   => 0,
			'authorized' => false,
			'state'      => Entry::STATE_PUBLISHED,
			'access'     => User::getAuthorisedViewLevels()
		);

		if (!User::isGuest())
		{
			if ($this->config->get('access-manage-component'))
			{
				$filters['authorized'] = true;
			}
		}

		// Output HTML
		$this->view
			->set('archive', $this->model)
			->set('config', $this->config)
			->set('row', $row)
			->set('filters', $filters)
			->setLayout('entry')
			->display();
	}

	/**
	 * Show a form for editing an entry
	 *
	 * @return  void
	 */
	public function editTask($entry = null)
	{
		if (User::isGuest())
		{
			$rtrn = Request::getVar('REQUEST_URI', Route::url('index.php?option=' . $this->_option . '&task=' . $this->_task, false, true), 'server');
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . base64_encode($rtrn)),
				Lang::txt('COM_BLOG_LOGIN_NOTICE'),
				'warning'
			);
			return;
		}

		if (!is_object($entry))
		{
			$entry = Entry::oneOrNew(Request::getInt('entry', 0));
		}

		if ($entry->isNew())
		{
			$entry->set('allow_comments', 1);
			$entry->set('state', Entry::STATE_PUBLISHED);
			$entry->set('scope', 'site');
			$entry->set('created_by', User::get('id'));
		}

		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		$this->view
			->set('archive', $this->model)
			->set('config', $this->config)
			->set('entry', $entry)
			->setLayout('edit')
			->display();
	}

	/**
	 * Save entry
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		if (User::isGuest())
		{
			$rtrn = Request::getVar('REQUEST_URI', Route::url('index.php?option=' . $this->_option . '&task=' . $this->_task), 'server');
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . base64_encode($rtrn)),
				Lang::txt('COM_BLOG_LOGIN_NOTICE'),
				'warning'
			);
			return;
		}

		// Check for request forgeries
		Request::checkToken();

		$fields = Request::getVar('entry', array(), 'post', 'none', 2);

		// Make sure we don't want to turn off comments
		//$fields['allow_comments'] = (isset($fields['allow_comments'])) ? 1 : 0;

		if (isset($fields['publish_up']) && $fields['publish_up'] != '')
		{
			$fields['publish_up']   = Date::of($fields['publish_up'], Config::get('offset'))->toSql();
		}
		if (isset($fields['publish_down']) && $fields['publish_down'] != '')
		{
			$fields['publish_down'] = Date::of($fields['publish_down'], Config::get('offset'))->toSql();
		}

		$row = Entry::oneOrNew($fields['id'])->set($fields);

		// Store new content
		if (!$row->save())
		{
			Notify::error($row->getError());
			return $this->editTask($row);
		}

		// Process tags
		if (!$row->tag(Request::getVar('tags', '')))
		{
			Notify::error($row->getError());
			return $this->editTask($row);
		}

		// Log activity
		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => ($fields['id'] ? 'updated' : 'created'),
				'scope'       => 'blog.entry',
				'scope_id'    => $row->get('id'),
				'description' => Lang::txt('COM_BLOG_ACTIVITY_ENTRY_' . ($fields['id'] ? 'UPDATED' : 'CREATED'), '<a href="' . Route::url($row->link()) . '">' . $row->get('title') . '</a>'),
				'details'     => array(
					'title' => $row->get('title'),
					'url'   => Route::url($row->link())
				)
			],
			'recipients' => [
				$row->get('created_by')
			]
		]);

		// Redirect to the entry
		App::redirect(
			Route::url($row->link())
		);
	}

	/**
	 * Mark an entry as deleted
	 *
	 * @return  void
	 */
	public function deleteTask()
	{
		if (User::isGuest())
		{
			$rtrn = Request::getVar('REQUEST_URI', Route::url('index.php?option=' . $this->_option, false, true), 'server');
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . base64_encode($rtrn)),
				Lang::txt('COM_BLOG_LOGIN_NOTICE'),
				'warning'
			);
			return;
		}

		if (!$this->config->get('access-delete-entry'))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option),
				Lang::txt('COM_BLOG_NOT_AUTHORIZED'),
				'error'
			);
			return;
		}

		// Incoming
		$id = Request::getInt('entry', 0);

		if (!$id)
		{
			return $this->displayTask();
		}

		$process    = Request::getVar('process', '');
		$confirmdel = Request::getVar('confirmdel', '');

		// Initiate a blog entry object
		$entry = Entry::oneOrFail($id);

		// Did they confirm delete?
		if (!$process || !$confirmdel)
		{
			if ($process && !$confirmdel)
			{
				$this->setError(Lang::txt('COM_BLOG_ERROR_CONFIRM_DELETION'));
			}

			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}

			$this->view
				->set('archive', $this->model)
				->set('config', $this->config)
				->set('entry', $entry)
				->display();
			return;
		}

		// Check for request forgeries
		Request::checkToken();

		// Delete the entry itself
		$entry->set('state', 2);

		if (!$entry->save())
		{
			Notify::error($entry->getError());
		}

		// Log the activity
		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => 'deleted',
				'scope'       => 'blog.entry',
				'scope_id'    => $id,
				'description' => Lang::txt('COM_BLOG_ACTIVITY_ENTRY_DELETED', '<a href="' . Route::url($entry->link()) . '">' . $entry->get('title') . '</a>'),
				'details'     => array(
					'title' => $entry->get('title'),
					'url'   => Route::url($entry->link())
				)
			],
			'recipients' => [
				$entry->get('created_by')
			]
		]);

		// Return the entries lsit
		App::redirect(
			Route::url('index.php?option=' . $this->_option)
		);
	}

	/**
	 * Generate an RSS feed of entries
	 *
	 * @return  void
	 */
	public function feedTask()
	{
		if (!$this->config->get('feeds_enabled'))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option)
			);
			return;
		}

		// Set the mime encoding for the document
		Document::setType('feed');

		// Start a new feed object
		$doc = Document::instance();
		$doc->link = Route::url('index.php?option=' . $this->_option);

		// Incoming
		$filters = array(
			'year'       => Request::getInt('year', 0),
			'month'      => Request::getInt('month', 0),
			'scope'      => $this->config->get('show_from', 'site'),
			'scope_id'   => 0,
			'search'     => Request::getVar('search', ''),
			'authorized' => false,
			'state'      => 1,
			'access'     => User::getAuthorisedViewLevels()
		);

		if ($filters['year'] > date("Y"))
		{
			$filters['year'] = 0;
		}
		if ($filters['month'] > 12)
		{
			$filters['month'] = 0;
		}
		if ($filters['scope'] == 'both')
		{
			$filters['scope'] = '';
		}

		if (!User::isGuest())
		{
			if ($this->config->get('access-manage-component'))
			{
				//$filters['state'] = null;
				$filters['authorized'] = true;
				array_push($filters['access'], 5);
			}
		}

		// Build some basic RSS document information
		$doc->title  = Config::get('sitename') . ' - ' . Lang::txt(strtoupper($this->_option));
		$doc->title .= ($filters['year'])  ? ': ' . $filters['year'] : '';
		$doc->title .= ($filters['month']) ? ': ' . sprintf("%02d", $filters['month']) : '';

		$doc->description = Lang::txt('COM_BLOG_RSS_DESCRIPTION', Config::get('sitename'));
		$doc->copyright   = Lang::txt('COM_BLOG_RSS_COPYRIGHT', date("Y"), Config::get('sitename'));
		$doc->category    = Lang::txt('COM_BLOG_RSS_CATEGORY');

		// Get the records
		$rows = $this->model->entries($filters)
			->ordered()
			->paginated()
			->rows();

		// Start outputing results if any found
		if ($rows->count() > 0)
		{
			foreach ($rows as $row)
			{
				$item = new \Hubzero\Document\Type\Feed\Item();

				// Strip html from feed item description text
				$item->description = $row->content();
				$item->description = html_entity_decode(Sanitize::stripAll($item->description));
				if ($this->config->get('feed_entries') == 'partial')
				{
					$item->description = String::truncate($item->description, 300);
				}
				$item->description = '<![CDATA[' . $item->description . ']]>';

				// Load individual item creator class
				$item->title       = html_entity_decode(strip_tags($row->get('title')));
				$item->link        = Route::url($row->link());
				$item->date        = date('r', strtotime($row->published()));
				$item->category    = '';
				$item->author      = $row->creator()->get('email') . ' (' . $row->creator()->get('name') . ')';

				// Loads item info into rss array
				$doc->addItem($item);
			}
		}
	}

	/**
	 * Save a comment
	 *
	 * @return  void
	 */
	public function savecommentTask()
	{
		// Ensure the user is logged in
		if (User::isGuest())
		{
			$rtrn = Request::getVar('REQUEST_URI', Route::url('index.php?option=' . $this->_option), 'server');
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . base64_encode($rtrn)),
				Lang::txt('COM_BLOG_LOGIN_NOTICE'),
				'warning'
			);
			return;
		}

		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$data = Request::getVar('comment', array(), 'post', 'none', 2);

		// Instantiate a new comment object and pass it the data
		$comment = Comment::oneOrNew($data['id'])->set($data);

		// Store new content
		if (!$comment->save())
		{
			$this->setError($comment->getError());
			return $this->entryTask();
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
				'description' => Lang::txt('COM_BLOG_ACTIVITY_COMMENT_' . ($data['id'] ? 'UPDATED' : 'CREATED'), $comment->get('id'), '<a href="' . Route::url($entry->link() . '#c' . $comment->get('id')) . '">' . $entry->get('title') . '</a>'),
				'details'     => array(
					'title'    => $entry->get('title'),
					'entry_id' => $entry->get('id'),
					'url'      => $entry->link()
				)
			],
			'recipients' => $recipients
		]);

		return $this->entryTask();
	}

	/**
	 * Delete a comment
	 *
	 * @return  void
	 */
	public function deletecommentTask()
	{
		// Ensure the user is logged in
		if (User::isGuest())
		{
			$this->setError(Lang::txt('COM_BLOG_LOGIN_NOTICE'));
			return $this->entryTask();
		}

		// Incoming
		$id    = Request::getInt('comment', 0);
		$year  = Request::getVar('year', '');
		$month = Request::getVar('month', '');
		$alias = Request::getVar('alias', '');

		if (!$id)
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&year=' . $year . '&month=' . $month . '&alias=' . $alias, false)
			);
			return;
		}

		// Initiate a blog comment object
		$comment = Comment::oneOrFail($id);

		if (User::get('id') != $comment->get('created_by') && !$this->config->get('access-delete-comment'))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&year=' . $year . '&month=' . $month . '&alias=' . $alias, false)
			);
			return;
		}

		// Mark all comments as deleted
		$comment->set('state', Comment::STATE_DELETED);
		$comment->save();

		// Log the activity
		$entry = \Components\Blog\Models\Entry::oneOrFail($comment->get('entry_id'));

		$recipients = array($comment->get('created_by'));
		if ($comment->get('created_by') != $entry->get('created_by'))
		{
			$recipients[] = $entry->get('created_by');
		}

		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => 'deleted',
				'scope'       => 'blog.entry.comment',
				'scope_id'    => $comment->get('id'),
				'description' => Lang::txt('COM_BLOG_ACTIVITY_COMMENT_DELETED', $comment->get('id'), '<a href="' . Route::url($entry->link()) . '">' . $entry->get('title') . '</a>'),
				'details'     => array(
					'title'    => $entry->get('title'),
					'entry_id' => $entry->get('id'),
					'url'      => $entry->link()
				)
			],
			'recipients' => $recipients
		]);

		// Return the topics list
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&year=' . $year . '&month=' . $month . '&alias=' . $alias),
			($this->getError() ? $this->getError() : null),
			($this->getError() ? 'error' : null)
		);
	}

	/**
	 * Display an RSS feed of comments
	 *
	 * @return  string  RSS
	 */
	public function commentsTask()
	{
		if (!$this->config->get('feeds_enabled'))
		{
			throw new Exception(Lang::txt('Feed not found.'), 404);
		}

		// Set the mime encoding for the document
		Document::setType('feed');

		// Start a new feed object
		$doc = Document::instance();
		$doc->link = Route::url('index.php?option=' . $this->_option);

		// Incoming
		$alias = Request::getVar('alias', '');
		if (!$alias)
		{
			throw new Exception(Lang::txt('Feed not found.'), 404);
		}

		$this->entry = Entry::oneByScope($alias, 'site', 0);

		if (!$this->entry->isAvailable())
		{
			throw new Exception(Lang::txt('Feed not found.'), 404);
		}

		$year  = Request::getInt('year', date("Y"));
		$month = Request::getInt('month', 0);

		// Build some basic RSS document information
		$doc->title  = Config::get('sitename') . ' - ' . Lang::txt(strtoupper($this->_option));
		$doc->title .= ($year) ? ': ' . $year : '';
		$doc->title .= ($month) ? ': ' . sprintf("%02d", $month) : '';
		$doc->title .= stripslashes($this->entry->get('title', ''));
		$doc->title .= ': ' . Lang::txt('Comments');

		$doc->description = Lang::txt('COM_BLOG_COMMENTS_RSS_DESCRIPTION', Config::get('sitename'), stripslashes($this->entry->get('title')));
		$doc->copyright   = Lang::txt('COM_BLOG_RSS_COPYRIGHT', date("Y"), Config::get('sitename'));

		$rows = $this->entry->comments()
			->whereIn('state', array(1, 3))
			->ordered()
			->rows();

		// Start outputing results if any found
		if ($rows->count() <= 0)
		{
			return;
		}

		foreach ($rows as $row)
		{
			$this->_comment($doc, $row);
		}
	}

	/**
	 * Recursive method to add comments to a flat RSS feed
	 *
	 * @param   object $doc JDocumentFeed
	 * @param   object $row BlogModelComment
	 * @return	void
	 */
	private function _comment(&$doc, $row)
	{
		// Load individual item creator class
		$item = new \Hubzero\Document\Type\Feed\Item();
		$item->title = Lang::txt('Comment #%s', $row->get('id')) . ' @ ' . $row->created('time') . ' on ' . $row->created('date');
		$item->link  = Route::url($this->entry->link()  . '#c' . $row->get('id'));

		if ($row->isReported())
		{
			$item->description = Lang::txt('COM_BLOG_COMMENT_REPORTED_AS_ABUSIVE');
		}
		else
		{
			$item->description = html_entity_decode(Sanitize::stripAll($row->content()));
		}
		$item->description = '<![CDATA[' . $item->description . ']]>';

		if ($row->get('anonymous'))
		{
			//$item->author = Lang::txt('COM_BLOG_ANONYMOUS');
		}
		else
		{
			$item->author = $row->creator()->get('email') . ' (' . $row->creator()->get('name') . ')';
		}
		$item->date     = $row->created();
		$item->category = '';

		$doc->addItem($item);

		$replies = $row->replies()->whereIn('state', array(1, 3));

		if ($replies->count() > 0)
		{
			foreach ($replies as $reply)
			{
				$this->_comment($doc, $reply);
			}
		}
	}

	/**
	 * Method to check admin access permission
	 *
	 * @return  boolean  True on success
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
			$this->config->set('access-create-' . $assetType, User::authorise('core.create' . $at, $asset));
			$this->config->set('access-delete-' . $assetType, User::authorise('core.delete' . $at, $asset));
			$this->config->set('access-edit-' . $assetType, User::authorise('core.edit' . $at, $asset));
			$this->config->set('access-edit-state-' . $assetType, User::authorise('core.edit.state' . $at, $asset));
			$this->config->set('access-edit-own-' . $assetType, User::authorise('core.edit.own' . $at, $asset));
		}
	}
}
