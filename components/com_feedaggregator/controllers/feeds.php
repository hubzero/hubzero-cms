<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2014 Purdue University. All rights reserved.
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
 * @author   Kevin Wojkovich <kevinw@purdue.edu>
 * @copyright Copyright 2005-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_ROOT . DS . 'components' . DS . 'com_feedaggregator' . DS . 'models' . DS . 'feeds.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_feedaggregator' . DS . 'models' . DS . 'posts.php');

/**
 *  Feed Aggregator controller class
 */
class FeedaggregatorControllerFeeds extends \Hubzero\Component\SiteController
{
	/**
	 * Default component view
	 *
	 * @return     void
	 */
	public function displayTask()
	{
		$authlevel = JAccess::getAuthorisedViewLevels($this->juser->get('id'));
		$access_level = 3; //author_level

		if (in_array($access_level, $authlevel) && $this->juser->get('id'))
		{
			$model = new FeedAggregatorModelFeeds;

			$this->view->feeds = $model->loadAll();
			$this->view->title = JText::_('COM_FEEDAGGREGATOR');
			$this->view->display();
		}
		else if ($this->juser->get('id'))
		{
			$this->setRedirect(
				JRoute::_('index.php?option=com_feedaggregator'),
				JText::_('COM_FEEDAGGREGATOR_NOT_AUTH'),
				'warning'
			);
		}
		else if ($this->juser->get('guest')) // have person login
		{
			$rtrn = JRequest::getVar('REQUEST_URI', JRoute::_('index.php?option=' . $this->_option . '&task=' . $this->_task), 'server');
			$this->setRedirect(
				JRoute::_('index.php?option=com_users&view=login&return=' . base64_encode($rtrn)),
				JText::_('COM_FEEDAGGREGATOR_LOGIN_NOTICE'),
				'warning'
			);
		}
	}

	/**
	 * Edit source feed form, load appropriate record
	 *
	 * @return     void
	 */
	public function editTask()
	{
		//isset ID kinda deal
		$model = new FeedAggregatorModelFeeds;

		$this->view->feed  = $model->loadbyId(JRequest::getInt('id', 0));
		$this->view->user  = $this->juser;
		$this->view->title = JText::_('COM_FEEDAGGREGATOR_EDIT_FEEDS');
		$this->view->display();
	}

	/**
	 * Displays empty form for adding source feed
	 *
	 * @return     void
	 */
	public function newTask()
	{
		$this->view->setLayout('edit');
		$this->view->title = JText::_('COM_FEEDAGGREGATOR_ADD_FEED');
		$this->view->display();
	}

	/**
	 * Enables or disables a source feed
	 *
	 * @return     void
	 */
	public function statusTask()
	{
		$id = JRequest::getInt('id');
		$action = JRequest::getVar('action');
		$model = new FeedAggregatorModelFeeds();

		if ($action == 'enable')
		{
			$model->updateActive($id, 1);
			// Output messsage and redirect
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller),
				JText::_('COM_FEEDAGGREGATOR_FEED_ENABLED')
			);
		}
		elseif ($action == 'disable')
		{
			$model->updateActive($id, 0);

			// Output messsage and redirect
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller),
				JText::_('COM_FEEDAGGREGATOR_FEED_DISABLED')
			);
		}
		else
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller),
				JText::_('COM_FEEDAGGREGATOR_ERROR_ENABLE_DISABLE_FAILED'),
				'error'
			);
		}
	}

	/**
	 * Save Source Feed form
	 *
	 * @return     void
	 */
	public function saveTask()
	{
		//do a JRequest instead of a bind()
		$feed = new FeedAggregatorModelFeeds;

		//get the URL first in order to validate
		$feed->set('url', JRequest::getVar('url'));
		$feed->set('name', JRequest::getVar('name'));
		$feed->set('id', JRequest::getVar('id'));
		$feed->set('enabled', JRequest::getVar('enabled'));
		$feed->set('description', JRequest::getVar('description'));

		//validate url
		if (!filter_var($feed->get('url'), FILTER_VALIDATE_URL))
		{
			$this->feed = $feed;

			//redirect
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option . '&controller=feeds&task=new'),
				JText::_('COM_FEEDAGGREGATOR_ERROR_INVALID_URL'),
				'warning'
			);
		}
		else
		{
			if ($feed->store())
			{
				// Output messsage and redirect
				$this->setRedirect(
					JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller),
					JText::_('COM_FEEDAGGREGATOR_INFORMATION_UPDATED')
				);
			}
			else
			{
				$this->setRedirect(
					JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller),
					JText::_('COM_FEEDAGGREGATOR_ERROR_UPDATE_FAILED'),
					'warning'
				);
			}
		}
	}
}