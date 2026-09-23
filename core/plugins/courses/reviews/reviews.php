<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

include_once __DIR__ . DS . 'models' . DS . 'comment.php';

/**
 * Courses Plugin class for review
 */
class plgCoursesReviews extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Return data on a resource view (this will be some form of HTML)
	 *
	 * @param   object  $course
	 * @param   string  $active
	 * @return  array
	 */
	public function onCourseView($course, $active=null)
	{
		// Prepare the response
		$response = with(new \Hubzero\Base\Obj)
			->set('name', $this->_name)
			->set('title', Lang::txt('PLG_COURSES_' . strtoupper($this->_name)));

		$this->option     = Request::getCmd('option', 'com_courses');
		$this->controller = Request::getWord('controller', 'course');

		$tbl = new \Components\Courses\Models\Comment();

		// Build the HTML meant for the tab's metadata overview
		$view = $this->view('default', 'metadata')
			->set('option', $this->option)
			->set('controller', $this->controller)
			->set('course', $course)
			->set('tbl', $tbl);

		$response->set('metadata', $view->loadTemplate());

		// Check if our area is in the array of areas we want to return results for
		if ($response->get('name') == $active)
		{
			$database = App::get('db');

			$this->view = $this->view('default', 'view');
			$this->view->database = $this->database = $database;
			$this->view->option   = $this->option;
			$this->view->controller = $this->controller;
			$this->view->obj      = $this->obj      = $course;
			$this->view->obj_type = $this->obj_type = substr($this->option, 4);
			$this->view->url      = $this->url      = Route::url($course->link() . '&active=' . $this->_name, false, true);
			$this->view->depth    = 0;
			$this->view->tbl      = $tbl;

			$this->_authorize();

			$this->view->params   = $this->params;
			$this->view->task     = $this->task    = Request::getString('action', '');

			switch ($this->task)
			{
				// Entries
				case 'save':
					$this->_save();
					break;
				case 'new':
					$this->_view();
					break;
				case 'edit':
					$this->_view();
					break;
				case 'delete':
					$this->_delete();
					break;
				case 'view':
					$this->_view();
					break;
				case 'vote':
					$this->_vote();
					break;

				default:
					$this->_view();
					break;
			}

			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}

			// Return the output
			$response->set('html', $this->view->loadTemplate());
		}

		return $response;
	}

	/**
	 * Set permissions
	 *
	 * @param   string   $assetType  Type of asset to set permissions for (component, section, category, thread, post)
	 * @param   integer  $assetId    Specific object to check permissions for
	 * @return  void
	 */
	protected function _authorize($assetType='comment', $assetId=null)
	{
		// Are comments public or registered members only?
		if ($this->params->get('comments_viewable', 0) <= 0)
		{
			// Public
			$this->params->set('access-view-' . $assetType, true);
		}

		// Logged in?
		if (!User::isGuest())
		{
			// Set comments to viewable
			$this->params->set('access-view-' . $assetType, true);

			$actions = array(
				'admin', 'manage', 'edit', 'edit-own', 'create', 'delete'
			);

			$yearFormat  = "Y";
			$monthFormat = "m";
			$dayFormat   = "d";

			if ($this->obj->isManager())
			{
				foreach ($actions as $action)
				{
					$this->params->set('access-' . $action . '-' . $assetType, true);
				}
			}

			if (!$this->obj->isStudent())
			{
				return;
			}

			$d = $this->obj->get('created');

			$year  = intval(substr($d, 0, 4));
			$month = intval(substr($d, 5, 2));
			$day   = intval(substr($d, 8, 2));

			switch ($this->params->get('comments_close', 'never'))
			{
				case 'day':
					$dt = mktime(0, 0, 0, $month, ($day+1), $year);
				break;
				case 'week':
					$dt = mktime(0, 0, 0, $month, ($day+7), $year);
				break;
				case 'month':
					$dt = mktime(0, 0, 0, ($month+1), $day, $year);
				break;
				case '6months':
					$dt = mktime(0, 0, 0, ($month+6), $day, $year);
				break;
				case 'year':
					$dt = mktime(0, 0, 0, $month, $day, ($year+1));
				break;
				case 'never':
				default:
					$dt =mktime(0, 0, 0, $month, $day, $year);
				break;
			}

			$pdt = strftime($yearFormat, $dt) . '-' . strftime($monthFormat, $dt) . '-' . strftime($dayFormat, $dt) . ' 00:00:00';
			$today = Date::toSql();

			// Can users create comments?
			if ($this->params->get('comments_close', 'never') == 'never'
			 || ($this->params->get('comments_close', 'never') != 'now' && $today < $pdt))
			{
				$this->params->set('access-create-' . $assetType, true);
				$this->params->set('access-review-' . $assetType, true);
			}
			// Can users edit comments?
			if ($this->params->get('comments_editable', 1))
			{
				$this->params->set('access-edit-' . $assetType, true);
			}
			// Can users delete comments?
			if ($this->params->get('comments_deletable', 0))
			{
				$this->params->set('access-delete-' . $assetType, true);
			}
		}
	}

	/**
	 * Method to add a message to the component message que
	 *
	 * @param   string  $url      URL
	 * @param   string  $msg      The message to add
	 * @param   string  $msgType  Type of message
	 * @return  void
	 */
	public function redirect($url, $msg='', $msgType='')
	{
		$url = ($url != '') ? $url : Request::getString('REQUEST_URI', Route::url($this->obj->link() . '&active=reviews'), 'server');

		App::redirect($url, $msg, $msgType);
	}

	/**
	 * Redirect to login page
	 *
	 * @return  void
	 */
	protected function _login()
	{
		$return = base64_encode(Request::getString('REQUEST_URI', Route::url($this->obj->link() . '&active=reviews', false, true), 'server'));

		App::redirect(
			Route::url('index.php?option=com_users&view=login&return=' . $return, false),
			Lang::txt('PLG_COURSES_REVIEWS_LOGIN_NOTICE'),
			'warning'
		);
	}

	/**
	 * Vote on a comment
	 *
	 * @return  void
	 */
	protected function _vote()
	{
		// Ensure the user is logged in
		if (User::isGuest())
		{
			$this->setError(Lang::txt('PLG_COURSES_REVIEWS_LOGIN_NOTICE'));
			return $this->_login();
		}

		$no_html = Request::getInt('no_html', 0);

		// Record the vote. Both start assigned: neither branch runs when the
		// request carries no voteup or votedown, and $how and $item_id were then
		// read undefined -- a warning this hub turns into a 500.
		$item_id = 0;
		$how     = 0;

		if ($item_id = Request::getInt('voteup', 0))
		{
			$how = 1;
		}
		else if ($item_id = Request::getInt('votedown', 0))
		{
			$how = -1;
		}

		if (!$item_id || !$how)
		{
			App::redirect($this->url);
			return;
		}

		$item = \Components\Courses\Models\Comment::oneOrFail($item_id);

		// ...and it has to be a review on this course. oneOrFail() resolves any
		// comment row on the hub, and nothing else here looks at which one.
		if ((string) $item->get('item_type') !== (string) $this->obj_type
		 || (int) $item->get('item_id') !== (int) $this->obj->get('id'))
		{
			App::redirect($this->url);
			return;
		}

		if (!$item->vote($how, User::get('id')))
		{
			$this->setError($item->getError());
		}

		if ($this->getError() && !$no_html)
		{
			App::redirect(
				$this->url,
				$this->getError(),
				'error'
			);
			return;
		}

		$item->set('vote', $how);

		$this->view->setLayout('vote');
		$this->view->set('item', $item);

		if (!$no_html)
		{
			App::redirect(
				$this->url,
				Lang::txt('PLG_COURSES_REVIEWS_VOTE_SAVED'),
				'message'
			);
			return;
		}

		$this->view->setErrors($this->getErrors());

		// Ugly brute force method of cleaning output
		ob_clean();
		echo $this->view->loadTemplate();
		exit();
	}

	/**
	 * Show a list of comments
	 *
	 * @return  void
	 */
	protected function _view()
	{
		// Get comments on this article
		$comments = $this->view->tbl
			->whereEquals('item_type', $this->obj_type)
			->whereEquals('item_id', $this->obj->get('id'))
			->whereEquals('parent', 0)
			->whereIn('state', array(
				Components\Courses\Models\Comment::STATE_PUBLISHED,
				Components\Courses\Models\Comment::STATE_FLAGGED
			))
			->limit($this->params->get('display_limit', 25))
			->ordered()
			->rows();

		$this->view->set('comments', $comments);

		$this->view->setErrors($this->getErrors());
	}

	/**
	 * Save an entry
	 *
	 * @return  void
	 */
	protected function _save()
	{
		// Ensure the user is logged in
		if (User::isGuest())
		{
			return $this->_login();
		}

		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$comment = Request::getArray('comment', array(), 'post');

		// Resolve the row before anything is bound onto it.
		//
		// This used to be Comment::blank()->set($comment): set() copies id, and
		// a Relational with an id is not new, so save() issues an UPDATE. A
		// posted comment[id] therefore rewrote ANY course review on the hub --
		// its content, its item_id and its state -- and the only thing in the
		// way was access-edit-comment, which _authorize() grants on being a
		// manager of the course being VIEWED while ignoring the $assetId it is
		// handed.
		$__cid = isset($comment['id']) ? (int) $comment['id'] : 0;
		$row   = \Components\Courses\Models\Comment::oneOrNew($__cid);

		// An existing review has to be on this course, and the caller has to own
		// it or be able to moderate here.
		if (!$row->isNew())
		{
			if ((string) $row->get('item_type') !== (string) $this->obj_type
			 || (int) $row->get('item_id') !== (int) $this->obj->get('id'))
			{
				App::redirect(
					$this->url,
					Lang::txt('PLG_COURSES_REVIEWS_NOTAUTH'),
					'warning'
				);
				return;
			}

			// item_id and item_type decide which course a review appears under.
			// They are hidden inputs on the form, so keep the stored ones rather
			// than letting the post move the review to another course.
			unset($comment['item_id']);
			unset($comment['item_type']);
			unset($comment['created']);
			unset($comment['created_by']);
			unset($comment['state']);
		}

		$row->set($comment);

		// On create, pin the review to the course being viewed. created and
		// created_by are filled by the model's $initiate list.
		if ($row->isNew())
		{
			$row->set('item_id', $this->obj->get('id'));
			$row->set('item_type', $this->obj_type);
		}

		// access-edit-comment is every enrolled student (comments_editable),
		// so testing it alone let any student edit any other's review. The
		// rule the view renders Edit on: the author, where reviews are
		// editable, or a manager. A new review needs access-create-comment,
		// which comments_close withdraws.
		if ($row->get('id'))
		{
			if (!$this->params->get('access-manage-comment')
			 && !$this->params->get('access-admin-comment')
			 && !($this->params->get('access-edit-comment') && $row->get('created_by') == User::get('id')))
			{
				App::redirect(
					$this->url,
					Lang::txt('PLG_COURSES_REVIEWS_NOTAUTH'),
					'warning'
				);
			}
		}
		elseif (!$this->params->get('access-create-comment'))
		{
			App::redirect(
				$this->url,
				Lang::txt('PLG_COURSES_REVIEWS_NOTAUTH'),
				'warning'
			);
		}

		// Store new content
		if (!$row->save())
		{
			App::redirect(
				$this->url,
				$row->getError(),
				'error'
			);
		}

		App::redirect(
			$this->url,
			Lang::txt('PLG_COURSES_REVIEWS_SAVED'),
			'message'
		);
	}

	/**
	 * Mark a comment as deleted
	 * NOTE: Does not actually delete data. Simply marks record.
	 *
	 * @return  void
	 */
	protected function _delete()
	{
		// Ensure the user is logged in
		if (User::isGuest())
		{
			$this->_login();
		}

		// Incoming
		$id = Request::getInt('comment', 0);
		if (!$id)
		{
			// _redirect() is defined neither here nor on Hubzero\Plugin\Plugin,
			// so reaching this with no comment id was a fatal.
			App::redirect($this->url);
			return;
		}

		// Initiate a comment object
		$comment = \Components\Courses\Models\Comment::oneOrFail($id);

		// oneOrFail() resolves any comment row on the hub, and the moderator arm
		// below is access-delete-comment, which _authorize() grants on being a
		// manager of the course being VIEWED and which ignores the $assetId it
		// is handed. So the review has to be on this course first, or a manager
		// of their own course could soft-delete every review on the site.
		if ((string) $comment->get('item_type') !== (string) $this->obj_type
		 || (int) $comment->get('item_id') !== (int) $this->obj->get('id'))
		{
			App::redirect($this->url);
			return;
		}

		if (User::get('id') != $comment->get('created_by') && !$this->params->get('access-delete-comment'))
		{
			App::redirect($this->url);
		}

		$comment->set('state', $comment::STATE_DELETED);

		// Delete the entry itself
		if (!$comment->save())
		{
			$this->setError($comment->getError());
		}

		App::redirect(
			$this->url,
			Lang::txt('PLG_COURSES_REVIEWS_REMOVED'),
			'message'
		);
	}
}
