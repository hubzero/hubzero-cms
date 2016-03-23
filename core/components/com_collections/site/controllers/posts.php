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

namespace Components\Collections\Site\Controllers;

use Components\Collections\Models\Collection;
use Components\Collections\Models\Archive;
use Components\Collections\Models\Post;
use Components\Collections\Models\Item;
use Components\Collections\Tables;
use Hubzero\Component\SiteController;
use Hubzero\Item\Comment;
use Pathway;
use Request;
use Route;
use Lang;
use User;
use App;

/**
 * Controller class for collection posts
 */
class Posts extends SiteController
{
	/**
	 * Determines task being called and attempts to execute it
	 *
	 * @return	void
	 */
	public function execute()
	{
		$this->model = Archive::getInstance();

		$this->registerTask('comment', 'post');

		parent::execute();
	}

	/**
	 * Redirect to login page
	 *
	 * @return  void
	 */
	public function loginTask()
	{
		$return = base64_encode(Request::getVar('REQUEST_URI', Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=' . $this->_task, false, true), 'server'));
		App::redirect(
			Route::url('index.php?option=com_users&view=login&return=' . $return, false)
		);
	}

	/**
	 * Display a post
	 *
	 * @return  string
	 */
	public function displayTask()
	{
		$this->view->config  = $this->config;
		$this->view->model   = $this->model;
		$this->view->no_html = Request::getInt('no_html', 0);

		$post_id = Request::getInt('post', 0);

		$this->view->post = Post::getInstance($post_id);

		if (!$this->view->post->exists())
		{
			App::redirect(
				Route::url('index.php?option=' . $this->option . '&controller=collections&task=posts')
			);
			return;
		}

		$this->view->collection = $this->model->collection($this->view->post->get('collection_id'));

		// Push error messages ot the view
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		$this->view
			->setLayout('display')
			->display();
	}

	/**
	 * Display a form for editing an entry
	 *
	 * @return  string
	 */
	public function editTask()
	{
		// Login is required
		if (User::isGuest())
		{
			return $this->loginTask();
		}

		$this->view->config = $this->config;

		// Incoming
		$this->view->no_html = Request::getInt('no_html', 0);

		$id = Request::getInt('post', 0);

		$this->view->collection = $this->model->collection(Request::getVar('board', 0));

		// Get all collections for a user
		$this->view->collections = $this->model->collections();
		if (!$this->view->collections->total())
		{
			$this->view->collection->setup(User::get('id'), 'member');
			$this->view->collections = $this->model->collections();
			$this->view->collection  = $this->model->collection(Request::getVar('board', 0));
		}

		// Load the post
		$this->view->entry = $this->view->collection->post($id);
		if (!$this->view->collection->exists() && $this->view->entry->exists())
		{
			$this->view->collection = $this->model->collection($this->view->entry->get('collection_id'));
		}

		// Are we removing an asset?
		if ($remove = Request::getInt('remove', 0))
		{
			if (!$this->view->entry->item()->removeAsset($remove))
			{
				$this->view->setError($this->view->entry->item()->getError());
			}
		}

		// If not being called through AJAX
		// push scripts and styles to document
		if (!$this->view->no_html)
		{
			$filters = array(
				'count'   => true,
				'access'  => 0,
				'state'   => 1,
				'user_id' => User::get('id')
			);
			$this->view->counts['collections'] = $this->model->collections($filters);
			$this->view->counts['posts'] = $this->model->posts($filters);
		}

		// Push error messages ot the view
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Display
		$this->view
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
		// Check for request forgeries
		Request::checkToken();

		// Login is required
		if (User::isGuest())
		{
			return $this->loginTask();
		}

		// Incoming
		$fields = Request::getVar('fields', array(), 'post', 'none', 2);

		// Get model
		$row = new Item();

		// Bind content
		if (!$row->bind($fields))
		{
			$this->setError($row->getError());
			return $this->editTask($row);
		}

		// Add some data
		//$row->set('_files', $files);
		$row->set('_assets', Request::getVar('assets', array(), 'post'));
		$row->set('_tags', trim(Request::getVar('tags', '')));
		$row->set('state', 1);

		// Store new content
		if (!$row->store())
		{
			$this->setError($row->getError());
			return $this->editTask($row);
		}

		// Create a post entry linking the item to the board
		$p = Request::getVar('post', array(), 'post');

		// Load a post entry
		$post = new Post($p['id']);
		if (!$post->exists())
		{
			// No post existed so set some values
			$post->set('item_id', $row->get('id'));
			$post->set('original', 1);
		}

		// Are we creating a new collection for it?
		$coltitle = Request::getVar('collection_title', '', 'post');
		if (!$p['collection_id'] && $coltitle)
		{
			$collection = new Collection();
			$collection->set('title', $coltitle);
			$collection->set('object_id', User::get('id'));
			$collection->set('object_type', 'member');
			$collection->store();

			$p['collection_id'] = $collection->get('id');
		}
		$post->set('collection_id', $p['collection_id']);

		// Set the description
		if (isset($p['description']))
		{
			$post->set('description', $p['description']);
		}

		// Store record
		if (!$post->store())
		{
			$this->setError($post->getError());
		}

		// Check for any errors
		if ($this->getError())
		{
			return $this->editTask($row);
		}

		// Redirect to main listing
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=collections')
		);
	}

	/**
	 * Save a comment
	 *
	 * @return  string
	 */
	public function savecommentTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Ensure the user is logged in
		if (User::isGuest())
		{
			return $this->loginTask();
		}

		// Incoming
		$comment = Request::getVar('comment', array(), 'post', 'none', 2);

		// Instantiate a new comment object and pass it the data
		$row = Comment::blank()->set($comment);

		// Store new content
		if (!$row->save())
		{
			$this->setError($row->getError());
			return $this->displayTask();
		}

		// Log activity
		$post = new Post(Request::getInt('post', 0));

		$title = $post->item()->get('title');
		$title = ($title ? $title : $post->item()->get('description', '#' . $post->get('id')));
		$title = \Hubzero\Utility\String::truncate(strip_tags($title), 70);

		$url = 'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&post=' . $post->get('id');

		$item = '<a href="' . Route::url($url) . '">' . $title . '</a>';

		$recipients = array(
			['collection', $post->get('collection_id')],
			['user', $row->get('created_by')],
			['user', $post->item()->get('created_by')]
		);

		if ($row->get('parent'))
		{
			$parent = Comment::oneOrFail($row->get('parent'));
			$recipients[] = ['user', $parent->get('created_by')];
		}

		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => ($comment['id'] ? 'updated' : 'created'),
				'scope'       => 'collections.comment',
				'scope_id'    => $row->get('id'),
				'description' => Lang::txt('COM_COLLECTIONS_ACTIVITY_COMMENT_' . ($comment['id'] ? 'UPDATED' : 'CREATED'), $row->get('id'), $item),
				'details'     => array(
					'collection_id' => $post->get('collection_id'),
					'post_id' => $post->get('id'),
					'item_id' => $row->get('item_id'),
					'url'     => Route::url($url)
				)
			],
			'recipients' => $recipients
		]);

		$this->displayTask();
	}

	/**
	 * Delete a comment
	 *
	 * @return  string
	 */
	public function deletecommentTask()
	{
		// Ensure the user is logged in
		if (User::isGuest())
		{
			return $this->loginTask();
		}

		// Incoming
		$id = Request::getInt('comment', 0);
		if (!$id)
		{
			return $this->displayTask();
		}

		// Initiate a whiteboard comment object
		$comment = Comment::oneOrFail($id);
		$comment->set('state', $comment::STATE_DELETED);

		// Delete the entry itself
		if (!$comment->save())
		{
			$this->setError($comment->getError());
		}

		// Log activity
		$post = new Post(Request::getInt('post', 0));

		$title = $post->item()->get('title');
		$title = ($title ? $title : $post->item()->get('description', '#' . $post->get('id')));
		$title = \Hubzero\Utility\String::truncate(strip_tags($title), 70);

		$url = 'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&post=' . $post->get('id');
		$item = '<a href="' . Route::url($url) . '">' . $title . '</a>';

		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => 'deleted',
				'scope'       => 'collections.comment',
				'scope_id'    => $comment->get('id'),
				'description' => Lang::txt('COM_COLLECTIONS_ACTIVITY_COMMENT_DELETED', $comment->get('id'), $item),
				'details'     => array(
					'collection_id' => $post->get('collection_id'),
					'post_id' => $post->get('id'),
					'item_id' => $comment->get('item_id'),
					'url'     => Route::url($url)
				)
			],
			'recipients' => array(
				['collection', $post->get('collection_id')],
				['user', $row->get('created_by')],
				['user', $post->item()->get('created_by')]
			)
		]);

		// Return the topics list
		return $this->displayTask();
	}

	/**
	 * Vote for an item
	 *
	 * @return  void
	 */
	public function voteTask()
	{
		if (User::isGuest())
		{
			return $this->loginTask();
		}

		// Incoming
		$id = Request::getInt('post', 0);

		// Get the post model
		$post = Post::getInstance($id);

		// Record the vote
		if (!$post->item()->vote())
		{
			$this->setError($post->item()->getError());
		}

		// Log activity
		$title = $post->item()->get('title');
		$title = ($title ? $title : $post->item()->get('description', '#' . $post->get('id')));
		$title = \Hubzero\Utility\String::truncate(strip_tags($title), 70);

		$url = 'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&post=' . $post->get('id');

		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => 'voted',
				'scope'       => 'collections.item',
				'scope_id'    => $post->get('item_id'),
				'description' => Lang::txt('COM_COLLECTIONS_ACTIVITY_VOTED', $post->get('item_id'), '<a href="' . Route::url($url) . '">' . $title . '</a>'),
				'details'     => array(
					'collection_id' => $post->get('collection_id'),
					'post_id' => $post->get('id'),
					'item_id' => $post->get('item_id'),
					'url'     => Route::url($url)
				)
			],
			'recipients' => array(
				['collection', $post->get('collection_id')],
				['user', $post->item()->get('created_by')]
			)
		]);

		// Display updated item stats if called via AJAX
		$no_html = Request::getInt('no_html', 0);
		if ($no_html)
		{
			echo Lang::txt('COM_COLLECTIONS_NUM_LIKES', $post->item()->get('positive'));
			exit;
		}

		// Display the main listing
		App::redirect(
			Route::url('index.php?option=' . $this->option . '&controller=collections&task=posts')
		);
	}

	/**
	 * Repost an entry
	 *
	 * @return  string
	 */
	public function collectTask()
	{
		if (User::isGuest())
		{
			return $this->loginTask();
		}

		$model = new Archive('member', User::get('id'));

		$no_html = Request::getInt('no_html', 0);

		// No collection ID selected so present repost form
		$repost = Request::getInt('repost', 0);
		if (!$repost)
		{
			// Incoming
			$post_id       = Request::getInt('post', 0);
			$collection_id = Request::getVar('board', 0);

			if (!$post_id && $collection_id)
			{
				$collection = $model->collection($collection_id);

				$item_id       = $collection->item()->get('id');
				$collection_id = $collection->item()->get('object_id');
			}
			else
			{
				$post = Post::getInstance($post_id);

				$item_id = $post->get('item_id');
			}

			$this->view->myboards      = $model->mine();
			$this->view->groupboards   = $model->mine('groups');

			//$this->view->name          = $this->_name;
			$this->view->option        = $this->_option;
			$this->view->no_html       = $no_html;
			$this->view->post_id       = $post_id;
			$this->view->collection_id = $collection_id;
			$this->view->item_id       = $item_id;

			$this->view->display();
			return;
		}

		Request::checkToken();

		$collection_title = Request::getVar('collection_title', '');
		$collection_id = Request::getInt('collection_id', 0);
		$item_id       = Request::getInt('item_id', 0);

		if ($collection_title)
		{
			$collection = new Collection();
			$collection->set('title', $collection_title);
			$collection->set('object_id', User::get('id'));
			$collection->set('object_type', 'member');
			if (!$collection->store())
			{
				$this->setError($collection->getError());
			}
			$collection_id = $collection->get('id');
		}

		// Try loading the current collection/post to see
		// if this has already been posted to the collection (i.e., no duplicates)
		$post = new Tables\Post($this->database);
		$post->loadByBoard($collection_id, $item_id);
		if (!$post->get('id'))
		{
			// No record found -- we're OK to add one
			$post = new Tables\Post($this->database);
			$post->item_id       = $item_id;
			$post->collection_id = $collection_id;
			$post->description   = Request::getVar('description', '');
			if (!$post->check())
			{
				$this->setError($post->getError());
			}
			else
			{
				// Store new content
				if (!$post->store())
				{
					$this->setError($post->getError());
				}
			}
		}
		if ($this->getError())
		{
			return $this->getError();
		}

		// Log activity
		$collection = new Collection($collection_id);

		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => 'created',
				'scope'       => 'collections.post',
				'scope_id'    => $post->id,
				'description' => Lang::txt(
					'COM_COLLECTIONS_ACTIVITY_COLLECTED',
					'<a href="' . Route::url($collection->link()) . '">' . $collection->get('title') . '</a>'
				),
				'details'     => array(
					'collection_id' => $post->collection_id,
					'post_id' => $post->id,
					'item_id' => $post->item_id
				)
			],
			'recipients' => array(
				['collection', $post->collection_id],
				['user', $collection->created_by],
				['user', $post->created_by]
			)
		]);

		// Display updated item stats if called via AJAX
		if ($no_html)
		{
			echo Lang::txt('COM_COLLECTIONS_NUM_REPOSTS', $post->getCount(array('item_id' => $post->get('item_id'), 'original' => 0)));
			exit;
		}

		// Display the main listing
		App::redirect(
			Route::url('index.php?option=' . $this->option . '&controller=collections&task=posts')
		);
	}

	/**
	 * Save post reordering
	 *
	 * @return  void
	 */
	public function reorderTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		// Incoming
		$posts = Request::getVar('post', array());

		if (is_array($posts))
		{
			$folder = null;
			$i = 0;

			foreach ($posts as $post)
			{
				$post = intval($post);
				if (!$post)
				{
					continue;
				}

				$row = new Post($post);
				if (!$row->exists())
				{
					continue;
				}
				$row->set('ordering', $i + 1);
				$row->store(false);

				$i++;
			}
		}

		if (!Request::getInt('no_html', 0))
		{
			// Output messsage and redirect
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller),
				Lang::txt('COM_COLLECTIONS_POSTS_REORDERED')
			);
			return;
		}

		$response = new \stdClass;
		$response->success = 1;
		$response->message = Lang::txt('COM_COLLECTIONS_POSTS_REORDERED');

		echo json_encode($response);
	}

	/**
	 * Get basic metadata for a post
	 *
	 * @return  void
	 */
	public function metadataTask()
	{
		$id = Request::getInt('post', 0);

		$post = new Post($id);

		if (!Request::getInt('no_html', 0))
		{
			// Output messsage and redirect
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller)
			);
			return;
		}

		$response = new \stdClass;
		$response->id       = $id;
		$response->reposts  = Lang::txt('COM_COLLECTIONS_NUM_REPOSTS', $post->item()->get('reposts', 0));
		$response->comments = Lang::txt('COM_COLLECTIONS_NUM_COMMENTS', $post->item()->get('comments', 0));
		$response->likes    = Lang::txt('COM_COLLECTIONS_NUM_LIKES', $post->item()->get('positive', 0));

		echo json_encode($response);
	}
}
