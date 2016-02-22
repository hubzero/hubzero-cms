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

include_once(__DIR__ . DS . 'models' . DS . 'comment.php');

/**
 * Courses Plugin class for review
 */
class plgCoursesReviews extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Return data on a resource view (this will be some form of HTML)
	 *
	 * @param      object  $resource Current resource
	 * @param      string  $option    Name of the component
	 * @param      array   $areas     Active area(s)
	 * @param      string  $rtrn      Data to be returned
	 * @return     array
	 */
	public function onCourseView($course, $active=null)
	{
		// Prepare the response
		$response = with(new \Hubzero\Base\Object)
			->set('name', $this->_name)
			->set('title', Lang::txt('PLG_COURSES_' . strtoupper($this->_name)));

		$this->option     = Request::getCmd('option', 'com_courses');
		$this->controller = Request::getWord('controller', 'course');

		$database = App::get('db');
		$tbl = new \Hubzero\Item\Comment($database);

		// Build the HTML meant for the tab's metadata overview
		$view = $this->view('default', 'metadata');
		$view->set('option', $this->option)
		     ->set('controller', $this->controller)
		     ->set('course', $course)
		     ->set('tbl', $tbl);

		$response->set('metadata', $view->loadTemplate());

		// Check if our area is in the array of areas we want to return results for
		if ($response->get('name') == $active)
		{
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

			$this->view->task     = $this->task    = Request::getVar('action', '');

			switch ($this->task)
			{
				// Entries
				case 'save':     $this->_save();   break;
				case 'new':      $this->_view();   break;
				case 'edit':     $this->_view();   break;
				case 'delete':   $this->_delete(); break;
				case 'view':     $this->_view();   break;
				case 'vote':     $this->_vote();   break;

				default:         $this->_view();   break;
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
	 * @param      string  $assetType Type of asset to set permissions for (component, section, category, thread, post)
	 * @param      integer $assetId   Specific object to check permissions for
	 * @return     void
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
	 * @param   string $message The message to add
	 * @return  void
	 */
	public function redirect($url, $msg='', $msgType='')
	{
		$url = ($url != '') ? $url : Request::getVar('REQUEST_URI', Route::url($this->obj->link() . '&active=reviews'), 'server');

		parent::redirect($url, $msg, $msgType);
	}

	/**
	 * Redirect to login page
	 *
	 * @return    void
	 */
	protected function _login()
	{
		$return = base64_encode(Request::getVar('REQUEST_URI', Route::url($this->obj->link() . '&active=reviews', false, true), 'server'));
		App::redirect(
			Route::url('index.php?option=com_users&view=login&return=' . $return, false),
			Lang::txt('PLG_COURSES_REVIEWS_LOGIN_NOTICE'),
			'warning'
		);
	}

	/**
	 * Vote on a comment
	 *
	 * @return    void
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

		// Record the vote
		if ($item_id = Request::getInt('voteup', 0))
		{
			$how = 1;
		}
		else if ($item_id = Request::getInt('votedown', 0))
		{
			$how = -1;
		}

		$v = \Hubzero\Item\Vote::blank();
		$v->set(array(
			'created_by' => User::get('id'),
			'item_type'  => 'comment',
			'vote'       => $how,
			'item_id'    => $item_id
		));

		// Store new content
		if (!$v->store())
		{
			$this->setError($v->getError());
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

		$this->view->setLayout('vote');

		$this->view->item = new \Hubzero\Item\Comment($this->database);
		$this->view->item->load($v->item_id);
		if ($v->get('vote') == 1)
		{
			$this->view->item->positive++;
		}
		else
		{
			$this->view->item->negative++;
		}
		if (!$this->view->item->store())
		{
			$this->setError($this->view->item->getError());
		}
		$this->view->item->vote = $v->get('vote');

		if (!$no_html)
		{
			App::redirect(
				$this->url,
				Lang::txt('PLG_COURSES_REVIEWS_VOTE_SAVED'),
				'message'
			);
			return;
		}

		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		// Ugly brute force method of cleaning output
		ob_clean();
		echo $this->view->loadTemplate();
		exit();
	}

	/**
	 * Show a list of comments
	 *
	 * @return    void
	 */
	protected function _view()
	{
		// Get comments on this article
		$comments = $this->view->tbl->find(array(
			'item_type' => $this->obj_type,
			'item_id'   => $this->obj->get('id'),
			'parent'    => 0,
			'state'     => 1,
			'limit'     => $this->params->get('comments_limit', 25)
		));
		if ($comments)
		{
			foreach ($comments as $k => $comment)
			{
				$comments[$k] = new \Components\Courses\Models\Comment($comment);
			}
		}
		$this->view->comments = new \Hubzero\Base\ItemList($comments);

		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}
	}

	/**
	 * Save an entry
	 *
	 * @return    void
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
		$comment = Request::getVar('comment', array(), 'post', 'none', 2);

		// Instantiate a new comment object and pass it the data
		$row = new \Hubzero\Item\Comment($this->database);
		if (!$row->bind($comment))
		{
			App::redirect(
				$this->url,
				$row->getError(),
				'error'
			);
			return;
		}
		$row->setUploadDir($this->params->get('comments_uploadpath', '/site/comments'));

		if ($row->id && !$this->params->get('access-edit-comment'))
		{
			App::redirect(
				$this->url,
				Lang::txt('PLG_COURSES_REVIEWS_NOTAUTH'),
				'warning'
			);
			return;
		}

		// Check content
		if (!$row->check())
		{
			App::redirect(
				$this->url,
				$row->getError(),
				'error'
			);
			return;
		}

		// Store new content
		if (!$row->store())
		{
			App::redirect(
				$this->url,
				$row->getError(),
				'error'
			);
			return;
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
	 * @return    void
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
			return $this->_redirect();
		}

		// Initiate a blog comment object
		$comment = new \Hubzero\Item\Comment($this->database);
		$comment->load($id);

		if (User::get('id') != $comment->created_by && !$this->params->get('access-delete-comment'))
		{
			App::redirect($this->url);
			return;
		}

		// Delete the entry itself
		if (!$comment->setState($id, 2))
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
