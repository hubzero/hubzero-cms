<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Collections\Site\Controllers;

use Components\Collections\Models\Collection;
use Components\Collections\Models\Archive;
use Hubzero\Base\ItemList;
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
use Event;

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
		$return = base64_encode(Request::getString('REQUEST_URI', Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=' . $this->_task, false, true), 'server'));
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
			App::abort(404, Lang::txt('COM_COLLECTIONS_ERROR_MISSING_POST'));
		}

		$this->view->collection = $this->model->collection($this->view->post->get('collection_id'));

		if (!$this->view->collection->exists())
		{
			App::abort(404, Lang::txt('COM_COLLECTIONS_ERROR_MISSING_COLLECTION'));
		}

		if (!$this->view->collection->canAccess(User::get('id')))
		{
			App::abort(403, Lang::txt('COM_COLLECTIONS_ERROR_ACCESS_DENIED'));
		}

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
	 * The boards the current user may move a post onto, as the ItemList the edit
	 * form iterates: their own boards and their groups', which is what
	 * Archive::mine() returns for collectTask()'s repost form.
	 *
	 * mine('groups') leaves out a group board that only managers may post to when
	 * the caller is not a manager, while canBePostedToBy() -- the test saveTask()
	 * enforces -- admits any member. The post's current board is kept selectable
	 * in that gap, or an edit that never touched the select would relocate the
	 * post to whatever option came first.
	 *
	 * @param   object  $current  The post's current board, or null
	 * @return  \Hubzero\Base\ItemList
	 */
	protected function _postableBoards($current = null)
	{
		$boards = array();
		$seen   = array();

		foreach ((array) $this->model->mine() as $row)
		{
			$boards[] = new Collection($row);
			$seen[(int) $row->id] = true;
		}

		foreach ((array) $this->model->mine('groups') as $rows)
		{
			foreach ((array) $rows as $row)
			{
				if (!isset($seen[(int) $row->id]))
				{
					$boards[] = new Collection($row);
					$seen[(int) $row->id] = true;
				}
			}
		}

		if ($current && $current->exists()
		 && !isset($seen[(int) $current->get('id')])
		 && $current->canBePostedToBy())
		{
			$boards[] = $current;
		}

		return new ItemList($boards);
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

		$this->view->collection = $this->model->collection(Request::getString('board', 0));

		// Load the post
		$this->view->entry = $this->view->collection->post($id);
		if (!$this->view->collection->exists() && $this->view->entry->exists())
		{
			$this->view->collection = $this->model->collection($this->view->entry->get('collection_id'));
		}

		// ?post=<id> resolves any row on the hub through Collection::post(), and
		// this form then prints the item's title, description, url and its whole
		// asset list. displayTask() asks canAccess() and voteTask() asks
		// isReadableBy(); this was the one read of the same object with no test at
		// all, so a private board's post could be read here by any logged-in user.
		// saveTask() already refuses the write, so this closes the read.
		if ($this->view->entry->exists()
		 && !(new Collection($this->view->entry->get('collection_id')))->isReadableBy())
		{
			App::abort(403, Lang::txt('COM_COLLECTIONS_ERROR_ACCESS_DENIED'));
		}

		// The boards this post may be moved to. $this->model is the component's
		// UNSCOPED archive -- deliberately, so displayTask() can show any post --
		// and its collections() therefore listed every board on the hub, private
		// ones included, as <option>s for anyone who opened this form. Offer what
		// the caller may actually post to instead: their own boards and their
		// groups', the same two sources collectTask()'s repost form draws on.
		// saveTask() enforces canBePostedToBy() on whatever is submitted, so
		// this closes the listing, not the write.
		$this->view->collections = $this->_postableBoards($this->view->collection);
		if (!$this->view->collections->total())
		{
			$this->view->collection->setup(User::get('id'), 'member');
			$this->view->collections = $this->_postableBoards($this->view->collection);

			// A board named in the request only if the post did not already
			// resolve one -- re-resolving unconditionally would discard it.
			if (!$this->view->collection->exists())
			{
				$this->view->collection = $this->model->collection(Request::getString('board', 0));
			}
		}

		// Are we removing an asset?
		if ($remove = Request::getInt('remove', 0))
		{
			// The post is chosen by ?post=<id> and Collection::post() resolves any
			// row, so without this any logged-in user could delete an asset from
			// another member's post by naming it. Item::removeAsset() does confirm
			// the asset belongs to the item, which stops it reaching an unrelated
			// asset -- but the item itself was still the caller's to pick.
			//
			// The test is on the ITEM's owner, not the post's: the asset hangs off
			// the item, and collectTask() creates a repost whose created_by is the
			// reposter while its item_id stays the original author's. Accepting the
			// post's owner here would let anyone repost a victim's item and then
			// delete its assets -- from the original and from every other repost.
			// This refuses nothing the form offers: the delete link renders only
			// inside `if ($this->entry->get('original'))`, and both Item::check()
			// and Post::check() stamp created_by from the same user on create.
			$item = $this->view->entry->item();

			if ($item->get('created_by') != User::get('id'))
			{
				App::abort(403, Lang::txt('COM_COLLECTIONS_ERROR_ACCESS_DENIED'));
			}

			if (!$item->removeAsset($remove))
			{
				$this->view->setError($item->getError());
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
		$fields = Request::getArray('fields', array(), 'post');

		// fields[id] names an existing row and Item resolves any of them, so
		// without this a caller could overwrite another member's item -- title,
		// description, url and, because fields[created_by] is a hidden input on
		// the edit form, its owner. Handing yourself the ownership of someone
		// else's item is also enough to defeat every guard keyed on it,
		// including the item delete.
		$__iid = isset($fields['id']) ? (int) $fields['id'] : 0;

		if ($__iid)
		{
			$__existing = new Item($__iid);

			if ($__existing->exists() && $__existing->get('created_by') != User::get('id'))
			{
				App::abort(403, Lang::txt('COM_COLLECTIONS_ERROR_ACCESS_DENIED'));
			}
		}

		// Get model
		$row = new Item();

		// Bind content
		if (!$row->bind($fields))
		{
			$this->setError($row->getError());
			return $this->editTask($row);
		}

		// The owner is never the submitter's to set
		$row->set('created_by', $__iid ? $__existing->get('created_by') : User::get('id'));

		// Add some data
		//$row->set('_files', $files);
		$row->set('_assets', Request::getArray('assets', array(), 'post'));
		$row->set('_tags', trim(Request::getString('tags', '')));
		$row->set('state', 1);

		// Store new content
		if (!$row->store())
		{
			$this->setError($row->getError());
			return $this->editTask($row);
		}

		// Create a post entry linking the item to the board
		$p = Request::getArray('post', array(), 'post');

		// Load a post entry. post[id] resolves any row, so an existing one has
		// to be the caller's and has to carry the item just written -- otherwise
		// naming someone else's post re-homes it and rewrites its description.
		$post = new Post(isset($p['id']) ? (int) $p['id'] : 0);

		if ($post->exists()
		 && ($post->get('created_by') != User::get('id')
			|| (int) $post->get('item_id') !== (int) $row->get('id')))
		{
			App::abort(403, Lang::txt('COM_COLLECTIONS_ERROR_ACCESS_DENIED'));
		}

		if (!$post->exists())
		{
			// No post existed so set some values
			$post->set('item_id', $row->get('id'));
			$post->set('original', 1);
		}

		// Are we creating a new collection for it?
		$coltitle = Request::getString('collection_title', '', 'post');
		if (!isset($p['collection_id']))
		{
			$p['collection_id'] = 0;
		}
		if (!$p['collection_id'] && $coltitle)
		{
			$collection = new Collection();
			$collection->set('title', $coltitle);
			$collection->set('object_id', User::get('id'));
			$collection->set('object_type', 'member');
			$collection->store();

			$p['collection_id'] = $collection->get('id');
		}
		// ...and the board it lands on has to be one the caller may post to.
		//
		// Cast first. Request::getArray() filters nothing, so post[collection_id]
		// can itself be an array, and Models\Collection's constructor BINDS an
		// array as the model's own data -- exists(), object_id and object_type
		// would all be the caller's to state, and the predicate would agree.
		if ((int) $p['collection_id']
		 && !(new Collection((int) $p['collection_id']))->canBePostedToBy())
		{
			App::abort(403, Lang::txt('COM_COLLECTIONS_ERROR_ACCESS_DENIED'));
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
		// Error if comments are not allowed
		if (!$this->config->get('allow_comments'))
		{
			App::abort(404, Lang::txt('COM_COLLECTIONS_ERROR_PAGE_NOT_FOUND'));
		}

		// Check for request forgeries
		Request::checkToken();

		// Ensure the user is logged in
		if (User::isGuest())
		{
			return $this->loginTask();
		}

		// Incoming
		$comment = Request::getArray('comment', array(), 'post');

		// The post being commented on, which decides what the comment hangs off.
		// ?post= is what the form and the routes already carry.
		$__post = Post::getInstance(Request::getInt('post', 0));

		if (!$__post->get('id')
		 || !(new Collection($__post->get('collection_id')))->isReadableBy())
		{
			App::abort(404, Lang::txt('COM_COLLECTIONS_ERROR_ACCESS_DENIED'));
		}

		// Instantiate the comment object and pass it the data
		$row = Comment::oneOrNew(isset($comment['id']) ? (int) $comment['id'] : 0);

		// For an existing comment, require ownership
		if (!$row->isNew()
		 && $row->get('created_by') != User::get('id')
		 && !User::authorise('core.manage', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		$__isNew = $row->isNew();

		// What the comment hangs off is never the submitter's to set.
		// comment[item_type] and comment[item_id] are hidden inputs, so a new
		// comment could otherwise be attached to any commentable object on the
		// hub -- bypassing that object's own comment permissions and its
		// allow_comments setting -- and an edit could re-point someone's comment
		// at another object. Both plugins pin these; this controller did not.
		$__type  = $__isNew ? 'collection' : $row->get('item_type');
		$__item  = $__isNew ? (int) $__post->get('item_id') : (int) $row->get('item_id');
		$__owner = $__isNew ? (int) User::get('id') : (int) $row->get('created_by');

		$row->set($comment);
		$row->set('item_type', $__type);
		$row->set('item_id', $__item);

		// created_by on BOTH paths. It is not on any comment form, but $comment
		// is the whole request array and modify() writes every set attribute that
		// is a real column -- created_by is not in Comment::$always, so nothing
		// downstream restores it. Pinning only on create left an author free to
		// post comment[created_by]=<anyone> on an edit of their own comment and
		// have it render under that person's name and avatar. Both plugins unset
		// it; this controller pins it.
		$row->set('created_by', $__owner);

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
		$title = \Hubzero\Utility\Str::truncate(strip_tags($title), 70);

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
		// Error if comments are not allowed
		if (!$this->config->get('allow_comments'))
		{
			App::abort(404, Lang::txt('COM_COLLECTIONS_ERROR_PAGE_NOT_FOUND'));
		}

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

		// Only the author or a collections manager may delete a comment
		if ($comment->get('created_by') != User::get('id')
		 && !User::authorise('core.manage', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

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
		$title = \Hubzero\Utility\Str::truncate(strip_tags($title), 70);

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
				['user', $comment->get('created_by')],
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

		// The post id is the caller's to pick and getInstance() resolves any row,
		// so the board it sits on has to be one they can actually read. Both
		// plugins' _vote() was scoped this way; this one was not.
		if (!$post->get('id')
		 || !(new Collection($post->get('collection_id')))->isReadableBy())
		{
			App::abort(404, Lang::txt('COM_COLLECTIONS_ERROR_ACCESS_DENIED'));
		}

		// Record the vote
		if (!$post->item()->vote())
		{
			$this->setError($post->item()->getError());
		}

		// Log activity
		$title = $post->item()->get('title');
		$title = ($title ? $title : $post->item()->get('description', '#' . $post->get('id')));
		$title = \Hubzero\Utility\Str::truncate(strip_tags($title), 70);

		$url = 'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&post=' . $post->get('id');

		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => 'voted',
				'scope'       => 'collections.item',
				'scope_id'    => $post->get('item_id'),
				'description' => Lang::txt('COM_COLLECTIONS_ACTIVITY_VOTED', $post->get('item_id'), '<a href="' . Route::url($url) . '">' . htmlspecialchars((string) ($title), ENT_QUOTES, 'UTF-8') . '</a>'),
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
			$collection_id = Request::getInt('board', 0);

			if (!$post_id && $collection_id)
			{
				// Collecting a whole board. Resolve it unscoped -- $model is
				// this user's archive, which now confines a numeric board to
				// boards they own, and the board being collected is by
				// definition someone else's. Ask the readability predicate
				// instead, so this form cannot be used to read back the item id
				// of a board the caller is not allowed to see.
				$collection = new Collection($collection_id);

				if (!$collection->isReadableBy())
				{
					App::abort(403, Lang::txt('COM_COLLECTIONS_ERROR_ACCESS_DENIED'));
				}

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

		$collection_title = Request::getString('collection_title', '');
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

		// Both ids arrive from the request and nothing downstream re-checks
		// either: Tables\Post::check() only requires them to be non-zero, and it
		// stamps created_by from the session. So without these two tests a
		// caller can drop a post onto anyone's board, and can mint a post of
		// their own carrying any item on the hub -- which is enough to defeat
		// every guard keyed on "the item this post carries".
		$__target = new Collection($collection_id);

		if (!$__target->canBePostedToBy())
		{
			App::abort(403, Lang::txt('COM_COLLECTIONS_ERROR_ACCESS_DENIED'));
		}

		$__item = new Item($item_id);

		if (!$__item->isCollectableBy())
		{
			App::abort(403, Lang::txt('COM_COLLECTIONS_ERROR_ACCESS_DENIED'));
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
			$post->description   = Request::getString('description', '');
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
				['user', $collection->get('created_by')],
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
		$posts = Request::getArray('post', array());

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

				// The ids come from the request and Post resolves any row, so
				// only reorder posts on a board the caller may moderate --
				// otherwise this rewrites the ordering of anyone's board.
				if (!(new Collection($row->get('collection_id')))->canBeModeratedBy())
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
