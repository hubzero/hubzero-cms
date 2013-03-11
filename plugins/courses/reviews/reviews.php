<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

include_once(JPATH_ROOT . DS . 'plugins' . DS . 'courses' . DS . 'reviews' . DS . 'tables' . DS . 'review.php');

/**
 * Resources Plugin class for review
 */
class plgCoursesReviews extends JPlugin
{
	private $_pushscripts = true;

	/**
	 * Constructor
	 * 
	 * @param      object &$subject Event observer
	 * @param      array  $config   Optional config values
	 * @return     void
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);

		$this->loadLanguage();
	}

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
	 * Rate a resource
	 * 
	 * @param      string $option Name of the component
	 * @return     array
	 */
	/*public function onCourseRateItem($option)
	{
		$id = JRequest::getInt('rid', 0);

		$arr = array(
			'area'     => 'reviews',
			'html'     => '',
			'metadata' => ''
		);

		ximport('Hubzero_View_Helper_Html');
		ximport('Hubzero_Plugin_View');

		$database =& JFactory::getDBO();
		$resource = new ResourcesResource($database);
		$resource->load($id);

		$h = new PlgResourcesReviewsHelper();
		$h->resource = $resource;
		$h->option   = $option;
		$h->_option  = $option;
		$h->execute();

		return $arr;
	}*/

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

		/*$ar = $this->onCourseOverviewAreas($model);
		if (empty($ar)) 
		{
			$rtrn = '';
		}

		ximport('Hubzero_View_Helper_Html');
		ximport('Hubzero_Plugin_View');
		ximport('Hubzero_Comment');
		ximport('Hubzero_User_Profile');

		// Instantiate a helper object and perform any needed actions
		$h = new PlgResourcesReviewsHelper();
		$h->resource = $model->resource;
		$h->option   = $option;
		$h->_option  = $option;
		$h->execute();*/

		// Get reviews for this resource
		$database =& JFactory::getDBO();

		ximport('Hubzero_Item_Comment');

		$tbl = new Hubzero_Item_Comment($database); //
		//$reviews = $r->getRatings($model->resource->id);

		// Are we returning any HTML?
		if ($rtrn == 'all' || $rtrn == 'html') 
		{
			ximport('Hubzero_Document');
			Hubzero_Document::addPluginStylesheet('courses', $this->_name);
			Hubzero_Document::addPluginScript('courses', $this->_name);

			
			ximport('Hubzero_Item_Vote');
			ximport('Hubzero_Plugin_View');

			$this->view = new Hubzero_Plugin_View(
				array(
					'folder'  => 'courses',
					'element' => $this->_name,
					'name'    => 'view',
					'layout'  => 'default'
				)
			);
			$this->view->database = $this->database = $database;
			$this->view->juser    = $this->juser    = JFactory::getUser();
			$this->view->option   = $this->option   = JRequest::getCmd('option', 'com_courses');
			//$this->view->course   = $this->course   = $course;
			$this->view->obj      = $this->obj      = $course;
			$this->view->obj_type = $this->obj_type = substr($this->option, 4);
			$this->view->url      = $this->url      = JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->obj->get('alias') . '&active=' . $this->_name, false, true);
			$this->view->depth    = 0;

			$this->_authorize();

			$this->view->params   = $this->params;

			$this->view->task     = $this->task    = JRequest::getVar('action', '');

			switch ($this->task) 
			{
				// Feeds
				//case 'feed.rss': $this->_feed();   break;
				//case 'feed':     $this->_feed();   break;

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
			ximport('Hubzero_Plugin_View');
			$view = new Hubzero_Plugin_View(
				array(
					'folder'  => 'courses',
					'element' => $this->_name,
					'name'    => 'metadata'
				)
			);
			$view->option = JRequest::getCmd('option', 'com_courses');
			$view->controller = JRequest::getWord('controller', 'course');
			$view->course = $course;
			$view->tbl = $tbl;

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

			// Joomla 1.6+
			if (version_compare(JVERSION, '1.6', 'ge'))
			{
				$yearFormat  = "Y";
				$monthFormat = "m";
				$dayFormat   = "d";
				/*$asset  = $this->option;
				if ($assetId)
				{
					$asset .= ($assetType != 'comment') ? '.' . $assetType : '';
					$asset .= ($assetId) ? '.' . $assetId : '';
				}

				// Are they an admin?
				$this->params->set('access-admin-' . $assetType, $this->juser->authorise('core.admin', $asset));
				$this->params->set('access-manage-' . $assetType, $this->juser->authorise('core.manage', $asset));
				if ($this->params->set('access-admin-' . $assetType) 
				 || $this->params->set('access-manage-' . $assetType))
				{
					$this->params->set('access-create-' . $assetType, true);
					$this->params->set('access-delete-' . $assetType, true);
					$this->params->set('access-edit-' . $assetType, true);
					return;
				}*/
			}
			else 
			{
				// Joomla 1.5

				$yearFormat  = "%Y";
				$monthFormat = "%m";
				$dayFormat   = "%d";

				// Are they an admin?
				/*if ($this->juser->authorize($this->option, 'manage'))
				{
					$this->params->set('access-manage-' . $assetType, true);
					$this->params->set('access-admin-' . $assetType, true);
					$this->params->set('access-create-' . $assetType, true);
					$this->params->set('access-delete-' . $assetType, true);
					$this->params->set('access-edit-' . $assetType, true);
					return;
				}*/
			}
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

			/*if (isset($this->obj->publish_up) && $this->obj->publish_up) 
			{
				$d = $this->obj->publish_up;
			}
			else if (isset($this->obj->modified) && $this->obj->modified) 
			{
				$d = $this->obj->modified;
			}
			else 
			{*/
				$d = $this->obj->get('created');
			//}
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
			$today = date('Y-m-d H:i:s', time());

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
	 * @param	string	$message	The message to add
	 * @return	void
	 */
	public function redirect($url, $msg='', $msgType='')
	{
		$url = ($url != '') ? $url : JRequest::getVar('REQUEST_URI', JRoute::_('index.php?option=' . $this->option . '&id=' . $this->obj->get('id') . '&active=reviews'), 'server');
		$url = str_replace('&amp;', '&', $url);

		$msg = ($msg) ? $msg : '';
		$msgType = ($msgType) ? $msgType : 'message';

		if ($url) 
		{
			$app =& JFactory::getApplication();
			$app->redirect($url, $msg, $msgType);
		}
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
		$v = new Hubzero_Item_Vote($this->database);
		$v->created_by = $this->juser->get('id');
		$v->item_type  = 'comment';
		//$v->item_id    = JRequest::getInt('comment', 0);
		//$v->vote       = JRequest::getVar('vote', 'up');
		if ($item_id = JRequest::getInt('voteup', 0))
		{
			$v->vote    = 1;
		} 
		else if ($item_id = JRequest::getInt('votedown', 0))
		{
			$v->vote    = -1;
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

		$this->view->item = new Hubzero_Item_Comment($this->database);
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
		// Push some needed scripts and stylings to the template but ensure we do it only once
		/*if ($this->_pushscripts) 
		{
			ximport('Hubzero_Document');
			Hubzero_Document::addPluginStyleSheet('hubzero', 'comments');
			Hubzero_Document::addPluginScript('hubzero', 'comments');

			$this->_pushscripts = false;
		}*/

		// Get comments on this article
		$hc = new Hubzero_Item_Comment($this->database);

		$this->view->comments = $hc->getComments(
			$this->obj_type, 
			$this->obj->get('id'),
			0,
			$this->params->get('comments_limit', 25)
		);
		
		//print_r($this->view->comments); die;

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
			$this->redirect(
				JRoute::_('index.php?option=com_login&return=' . base64_encode($this->url)), 
				JText::_('PLG_COURSES_REVIEWS_LOGIN_NOTICE'),
				'warning'
			);
			return;
		}

		// Incoming
		$comment = JRequest::getVar('comment', array(), 'post');

		// Instantiate a new comment object and pass it the data
		$row = new Hubzero_Item_Comment($this->database);
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
				JRoute::_('index.php?option=com_login&return=' . base64_encode($this->url)), 
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
			$this->redirect(
				JRoute::_('index.php?option=com_login&return=' . base64_encode($this->url)), 
				JText::_('PLG_COURSES_REVIEWS_LOGIN_NOTICE'),
				'warning'
			);
			return;
		}

		// Incoming
		$id = JRequest::getInt('comment', 0);
		if (!$id) 
		{
			return $this->_redirect();
		}

		// Initiate a blog comment object
		$comment = new Hubzero_Item_Comment($this->database);
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
