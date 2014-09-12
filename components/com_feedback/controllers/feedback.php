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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

ximport('Hubzero_Controller');

/**
 * Feedback controller class
 */
class FeedbackControllerFeedback extends Hubzero_Controller
{
	/**
	 * Determine task and execute it
	 * 
	 * @return     void
	 */
	public function execute()
	{
		$this->registerTask('success_story', 'story');

		parent::execute();
	}

	/**
	 * Set the pathway (breadcrumbs)
	 * 
	 * @return     void
	 */
	protected function _buildPathway()
	{
		$pathway =& JFactory::getApplication()->getPathway();

		if (count($pathway->getPathWay()) <= 0) 
		{
			$pathway->addItem(
				JText::_(strtoupper($this->_option)),
				'index.php?option=' . $this->_option
			);
		}
		if ($this->_task && in_array($this->_task, array('story', 'poll', 'sendstory', 'suggestions')))
		{
			$pathway->addItem(
				JText::_(strtoupper($this->_option) . '_' . strtoupper($this->_task)),
				'index.php?option=' . $this->_option . '&task=' . $this->_task
			);
		}
	}

	/**
	 * Set the page title
	 * 
	 * @return     void
	 */
	protected function _buildTitle()
	{
		$this->_title = JText::_(strtoupper($this->_option));
		if ($this->_task && in_array($this->_task, array('story', 'poll', 'sendstory', 'suggestions'))) 
		{
			$this->_title .= ': ' . JText::_(strtoupper($this->_option) . '_' . strtoupper($this->_task));
		}
		$document =& JFactory::getDocument();
		$document->setTitle($this->_title);
	}

	/**
	 * Display the main page
	 * 
	 * @return     void
	 */
	public function displayTask()
	{
		// Check if wishlistcomponent entry is there
		$this->view->wishlist = JComponentHelper::isEnabled('com_wishlist', true);

		// Check if poll component entry is there
		$this->view->poll = JComponentHelper::isEnabled('com_poll', true);

		// Set page title
		$this->_buildTitle();
		$this->view->title = $this->_title;

		// Set the pathway
		$this->_buildPathway();

		// Push some styles to the template
		$this->_getStyles('', 'introduction.css', true); // component, stylesheet name, look in media system dir
		$this->_getStyles();

		// Set any messages
		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}
		
		// Output HTML
		$this->view->display();
	}

	/**
	 * Show a form for sending a success story
	 * 
	 * @return     void
	 */
	public function storyTask($row=null)
	{
		if ($this->juser->get('guest')) 
		{
			$here = JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=' . $this->_task);
			$this->setRedirect(
				JRoute::_('index.php?option=com_login&return=' . base64_encode($here)),
				JText::_('To submit a success story, you need to be logged in. Please login using the form below:'),
				'warning'
			);
			return;
		}

		$this->view->setLayout('story');

		// Incoming
		$this->view->quote = array(
			'long'  => JRequest::getVar('quote', '', 'post'),
			'short' => JRequest::getVar('short_quote', '', 'post')
		);

		// Set page title
		$this->_buildTitle();
		$this->view->title = $this->_title;

		// Set the pathway
		$this->_buildPathway();

		// Push some styles to the template
		$this->_getStyles();

		ximport('Hubzero_User_Profile');
		$this->view->user = Hubzero_User_Profile::getInstance($this->juser->get('id'));

		if (!is_object($row))
		{
			$row = new FeedbackQuotes($this->database);
			$row->org = $this->view->user->get('organization');
			$row->fullname = $this->view->user->get('name');
		}
		$row->userid = $this->view->user->get('uidNumber');
		$row->useremail = $this->view->user->get('email');

		$this->view->row = $row;

		// Set error messages
		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		// Output HTML
		$this->view->display();
	}

	/**
	 * Show the latest poll
	 * 
	 * @return     void
	 */
	public function pollTask()
	{
		// Set page title
		$this->_buildTitle();
		$this->view->title = $this->_title;

		// Set the pathway
		$this->_buildPathway();

		// Push some styles to the template
		$this->_getStyles();

		// Set error messages
		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		// Output HTML
		$this->view->display();
	}

	/**
	 * Save a success story and show a thank you message
	 * 
	 * @return     void
	 */
	public function sendstoryTask()
	{
		if ($this->juser->get('guest')) 
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=' . $this->_task)
			);
			return;
		}

		$fields = JRequest::getVar('fields', array(), 'post');
		$fields = array_map('trim', $fields);

		// Initiate class and bind posted items to database fields
		$row = new FeedbackQuotes($this->database);
		if (!$row->bind($fields)) 
		{
			$this->setError($row->getError());
			$this->storyTask($row);
			return;
		}

		// Check that a story was entered
		if (!$row->quote) 
		{
			$this->setError(JText::_('COM_FEEDBACK_ERROR_MISSING_STORY'));
			$this->storyTask($row);
			return;
		}

		// Code cleaner for xhtml transitional compliance
		$row->quote = Hubzero_View_Helper_Html::purifyText($row->quote);
		$row->quote = str_replace('<br>', '<br />', $row->quote);
		$row->date  = date('Y-m-d H:i:s', time());
		$row->picture = basename($row->picture);

		// Check content
		if (!$row->check()) 
		{
			$this->setError($row->getError());
			$this->storyTask($row);
			return;
		}

		// Store new content
		if (!$row->store()) 
		{
			$this->setError($row->getError());
			$this->storyTask($row);
			return;
		}

		// Output HTML
		$this->view->setLayout('thanks');

		$this->view->user = $this->juser;
		$this->view->row = $row;
		$this->view->config = $this->config;

		// Set page title
		$this->_buildTitle();
		$this->view->title = $this->_title;

		// Set the pathway
		$this->_buildPathway();

		// Push some styles to the template
		$this->_getStyles();

		// Set error messages
		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		// Output HTML
		$this->view->display();
	}

	/**
	 * Show a form for submitting suggestions
	 * 
	 * @return     void
	 */
	public function suggestionsTask()
	{
		$this->setRedirect(
			JRoute::_('index.php?option=com_wishlist')
		);
	}
}

