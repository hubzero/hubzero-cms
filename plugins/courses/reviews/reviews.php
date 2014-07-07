<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

//include_once(JPATH_ROOT . DS . 'plugins' . DS . 'courses' . DS . 'reviews' . DS . 'tables' . DS . 'review.php');
include_once(JPATH_ROOT . DS . 'plugins' . DS . 'courses' . DS . 'reviews' . DS . 'models' . DS . 'comment.php');

/**
 * Resources Plugin class for review
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
	 * Push scripts to the document?
	 *
	 * @var    boolean
	 */
	private $_pushscripts = true;

	/**
	 * Return the alias and name for this category of content
	 *
	 * @param      object $resource Current resource
	 * @return     array
	 */
	public function &onCourseViewAreas($course)
	{
		$areas = array(
			'reviews' => JText::_('PLG_COURSES_REVIEWS')
		);
		return $areas;
	}

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
		$arr = array(
			'name'     => 'reviews',
			'html'     => '',
			'metadata' => ''
		);
		$rtrn = 'html';

		// Check if our area is in the array of areas we want to return results for
		if (is_array($active))
		{
			if (!in_array($arr['name'], $active))
			{
				$rtrn = 'metadata';
			}
		}
		else if ($active != $arr['name'])
		{
			$rtrn = 'metadata';
		}

		// Get reviews for this resource
		$database = JFactory::getDBO();

		$tbl = new \Hubzero\Item\Comment($database);

		$this->option     = JRequest::getCmd('option', 'com_courses');
		$this->controller = JRequest::getWord('controller', 'course');

		// Are we returning any HTML?
		if ($rtrn == 'all' || $rtrn == 'html')
		{
			$this->view = new \Hubzero\Plugin\View(
				array(
					'folder'  => 'courses',
					'element' => $this->_name,
					'name'    => 'view',
					'layout'  => 'default'
				)
			);
			$this->view->database = $this->database = $database;
			$this->view->juser    = $this->juser    = JFactory::getUser();
			$this->view->option   = $this->option; //   = JRequest::getCmd('option', 'com_courses');
			$this->view->controller = $this->controller;
			$this->view->obj      = $this->obj      = $course;
			$this->view->obj_type = $this->obj_type = substr($this->option, 4);
			$this->view->url      = $this->url      = JRoute::_($course->link() . '&active=' . $this->_name, false, true);
			$this->view->depth    = 0;
			$this->view->tbl      = $tbl;

			$this->_authorize();

			$this->view->params   = $this->params;

			$this->view->task     = $this->task    = JRequest::getVar('action', '');

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

			if ($this->getError())
			{
				foreach ($this->getErrors() as $error)
				{
					$this->view->setError($error);
				}
			}

			// Return the output
			$arr['html'] = $this->view->loadTemplate();
		}

		// Build the HTML meant for the "about" tab's metadata overview
		if ($rtrn == 'html' || $rtrn == 'metadata')
		{
			$view = new \Hubzero\Plugin\View(
				array(
					'folder'  => 'courses',
					'element' => $this->_name,
					'name'    => 'metadata'
				)
			);
			$view->option     = $this->option;
			$view->controller = $this->controller;
			$view->course     = $course;
			$view->tbl        = $tbl;

			$arr['metadata'] = $view->loadTemplate();
		}

		return $arr;
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
		if (!$this->juser->get('guest'))
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
			$today = JFactory::getDate();

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
		$url = ($url != '') ? $url : JRequest::getVar('REQUEST_URI', JRoute::_($this->obj->link() . '&active=reviews'), 'server');

		parent::redirect($url, $msg, $msgType);
	}

	/**
	 * Redirect to login page
	 *
	 * @return    void
	 */
	protected function _login()
	{
		$return = base64_encode(JRequest::getVar('REQUEST_URI', JRoute::_($this->obj->link() . '&active=reviews', false, true), 'server'));
		$this->redirect(
			JRoute::_('index.php?option=com_users&view=login&return=' . $return, false),
			JText::_('PLG_COURSES_REVIEWS_LOGIN_NOTICE'),
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
		if ($this->juser->get('guest'))
		{
			$this->setError(JText::_('PLG_COURSES_REVIEWS_LOGIN_NOTICE'));
			return $this->_login();
		}

		$no_html = JRequest::getInt('no_html', 0);

		// Get comments on this article
		$v = new \Hubzero\Item\Vote($this->database);
		$v->created_by = $this->juser->get('id');
		$v->item_type  = 'comment';

		if ($item_id = JRequest::getInt('voteup', 0))
		{
			$v->vote   = 1;
		}
		else if ($item_id = JRequest::getInt('votedown', 0))
		{
			$v->vote   = -1;
		}
		$v->item_id    = $item_id;

		// Check content
		if (!$v->check())
		{
			$this->setError($v->getError());
		}
		else
		{
			// Store new content
			if (!$v->store())
			{
				$this->setError($v->getError());
			}
		}

		if ($this->getError() && !$no_html)
		{
			$this->redirect(
				$this->url,
				$this->getError(),
				'error'
			);
			return;
		}

		$this->view->setLayout('vote');

		$this->view->item = new \Hubzero\Item\Comment($this->database);
		$this->view->item->load($v->item_id);
		if ($v->vote == 1)
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
		$this->view->item->vote = $v->vote;

		if (!$no_html)
		{
			$this->redirect(
				$this->url,
				JText::_('PLG_COURSES_REVIEWS_VOTE_SAVED'),
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
				$comments[$k] = new CoursesModelComment($comment);
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
		if ($this->juser->get('guest'))
		{
			return $this->_login();
		}

		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$comment = JRequest::getVar('comment', array(), 'post', 'none', 2);

		// Instantiate a new comment object and pass it the data
		$row = new \Hubzero\Item\Comment($this->database);
		if (!$row->bind($comment))
		{
			$this->redirect(
				$this->url,
				$row->getError(),
				'error'
			);
			return;
		}
		$row->setUploadDir($this->params->get('comments_uploadpath', '/site/comments'));

		if ($row->id && !$this->params->get('access-edit-comment'))
		{
			$this->redirect(
				$this->url,
				JText::_('PLG_COURSES_REVIEWS_NOTAUTH'),
				'warning'
			);
			return;
		}

		// Check content
		if (!$row->check())
		{
			$this->redirect(
				$this->url,
				$row->getError(),
				'error'
			);
			return;
		}

		// Store new content
		if (!$row->store())
		{
			$this->redirect(
				$this->url,
				$row->getError(),
				'error'
			);
			return;
		}

		$this->redirect(
			$this->url,
			JText::_('PLG_COURSES_REVIEWS_SAVED'),
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
		if ($this->juser->get('guest'))
		{
			$this->_login();
		}

		// Incoming
		$id = JRequest::getInt('comment', 0);
		if (!$id)
		{
			return $this->_redirect();
		}

		// Initiate a blog comment object
		$comment = new \Hubzero\Item\Comment($this->database);
		$comment->load($id);

		if ($this->juser->get('id') != $comment->created_by
		 && !$this->params->get('access-delete-comment'))
		{
			$this->redirect($this->url);
			return;
		}

		// Delete the entry itself
		if (!$comment->setState($id, 2))
		{
			$this->setError($comment->getError());
		}

		$this->redirect(
			$this->url,
			JText::_('PLG_COURSES_REVIEWS_REMOVED'),
			'message'
		);
	}
}
