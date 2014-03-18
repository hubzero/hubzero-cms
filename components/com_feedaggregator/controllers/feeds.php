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
 * @author   Kevin Wojkovich <kevinw@purdue.edu>
 * @copyright Copyright 2005-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
require_once(JPATH_ROOT.DS."components".DS."com_feedaggregator".DS."models".DS."feeds.php");
require_once(JPATH_ROOT.DS."components".DS."com_feedaggregator".DS."models".DS."posts.php");

/**
 *  Feed Aggregator controller class
 */
class FeedaggregatorControllerFeeds extends \Hubzero\Component\SiteController
{
	public function displayTask()
	{

		$userId = $this->juser->id;
		$authlevel = JAccess::getAuthorisedViewLevels($userId);
		$access_level = 3; //author_level
		if(in_array($access_level,$authlevel) && JFactory::getUser()->id)
		{
			$model = new FeedAggregatorModelFeeds;
			$feeds = $model->loadAll();
			$this->view->feeds = $feeds;

			$this->view->title =  JText::_('Feed Aggregator');
			$this->view->display();
		}
		else if(JFactory::getUser()->id)
		{
			$this->setRedirect(
					JRoute::_('index.php?option=com_feedaggregator'),
					JText::_('You do not have permission to view.'),
					'warning'
			);
		}
		else if(JFactory::getUser()->id == FALSE) // have person login
		{
			$rtrn = JRequest::getVar('REQUEST_URI', JRoute::_('index.php?option=' . $this->_option . '&task=' . $this->_task), 'server');
			$this->setRedirect(
					JRoute::_('index.php?option=com_login&return=' . base64_encode($rtrn)),
					JText::_('COM_FEEDAGGREGATOR_LOGIN_NOTICE'),
					'warning'
			);
		}

	}

	public function editTask()
	{
		//isset ID kinda deal
		$id = JRequest::getVar('id');
		$model = new FeedAggregatorModelFeeds;
		$feed = $model->loadbyId($id);
		$this->view->feed = $feed;


		$this->view->user = $this->juser;

		//$this->view->setLayout('edit');
		$this->_getScripts('assets/js/feeds');
		$this->view->title = JText::_('Edit Feeds');
		$this->view->display();
	}

	public function newTask()
	{
		$this->_getScripts('assets/js/feeds');
		$this->view->setLayout('edit');
		$this->view->title = JText::_('Add Feed');
		$this->view->display();
	}

	public function statusTask()
	{
		$id = JRequest::getInt('id');
		$action = JRequest::getVar('action');
		$model = new FeedAggregatorModelFeeds();

		if($action == 'enable')
		{
			$model->updateActive($id, 1);
			// Output messsage and redirect
			$this->setRedirect(
					'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
					JText::_('Feed Enabled.')
			);
		}
		elseif($action == 'disable')
		{
			$model->updateActive($id, 0);
			// Output messsage and redirect
			$this->setRedirect(
					'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
					JText::_('Feed Disabled.')
			);
		}
		else
		{
			$this->setRedirect(
					'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
					JText::_('Feed Enable/Disable Failed.', 'error')
			);
		}
	}

	public function saveTask()
	{
		//do a JRequest instead of a bind()
		$db = JFactory::getDBO();
		$feed = new FeedAggregatorModelFeeds;

		//get the URL first in order to validate
		//$feed->url = JRequest::getVar('url');
		$feed->set('url', JRequest::getVar('url'));
		/*
		echo '<pre>';
		echo($feed);
		echo '</pre>';
		echo '<pre>';
		echo($feed->get('url'));
		echo '</pre>';
		die;
		*/
		$feed->set('name', JRequest::getVar('name'));
		$feed->set('id', JRequest::getVar('id'));
		$feed->set('enabled', JRequest::getVar('enabled'));
		$feed->set('description', JRequest::getVar('description'));

		//validate url
		if(!filter_var($feed->get('url'), FILTER_VALIDATE_URL))
		{
			$this->feed = $feed;
			//redirect
			$this->setRedirect(
						'/feedaggregator?controller=feeds&task=new',
						JText::_('Invalid URL. Please make sure it is correct.'), 'warning'
				);

		}
		else
		{

			if($feed->store())
			{
				// Output messsage and redirect
				$this->setRedirect(
						'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
						JText::_('Feed Information Updated.')
				);
			}
			else
			{
				$this->setRedirect(
						'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
						JText::_('Feed Information Update Failed.', 'warning')
				);
			}

		}


	}
}