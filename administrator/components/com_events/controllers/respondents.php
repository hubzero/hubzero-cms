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

ximport('Hubzero_Controller');

/**
 * Events controller class for respondents
 */
class EventsControllerRespondents extends Hubzero_Controller
{
	/**
	 * View respondent details
	 * 
	 * @return     void
	 */
	public function respondentTask()
	{
		$this->view->resp = new EventsRespondent(array(
			'respondent_id' => JRequest::getInt('id', 0)
		));

		// Incoming
		$id = JRequest::getInt('event_id', 0);

		$this->view->event = new EventsEvent($this->database);
		$this->view->event->load($id);

		// Set any errors
		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Display a list of respondents
	 * 
	 * @return     void
	 */
	public function displayTask()
	{
		$this->view->resp = $this->getRespondents();

		// Incoming
		$ids = JRequest::getVar('id', array(0));
		$id = $ids[0];

		if (!$id) 
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option
			);
			return;
		}

		$this->view->event = new EventsEvent($this->database);
		$this->view->event->load($id);

		// Set any errors
		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Get respondents for an event
	 * 
	 * @return     object
	 */
	private function getRespondents()
	{
		$app =& JFactory::getApplication();
		$config = JFactory::getConfig();

		$sorting = JRequest::getVar('sortby', 'registered DESC');
		$filters = array(
			'search' => urldecode(JRequest::getString('search')),
			'id'     => JRequest::getVar('id', array()),
			'sortby' => $sorting == 'registerby DESC' ? 'registered DESC' : $sorting,
			'limit'  => $app->getUserStateFromRequest($this->_option . '.limit', 'limit', $config->getValue('config.list_limit'), 'int'),
			'offset' => JRequest::getInt('limitstart', 0)
		);
		if (!$filters['limit']){
			$filters['limit'] = 30;
		}
		return new EventsRespondent($filters);
	}

	/**
	 * Download a list of respondents
	 * 
	 * @return     void
	 */
	public function downloadTask()
	{
		EventsHtml::downloadlist($this->getRespondents(), $this->_option);
	}

	/**
	 * Remove one or more entries
	 * 
	 * @return     void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$workshop = JRequest::getInt('workshop', 0);
		$ids = JRequest::getVar('rid', array());

		// Get the single ID we're working with
		if (!is_array($ids)) 
		{
			$ids = array();
		}

		// Do we have any IDs?
		if (!empty($ids)) 
		{
			$r = new EventsRespondent(array());

			// Loop through each ID and delete the necessary items
			foreach ($ids as $id)
			{
				// Remove the profile
				$r->delete($id);
			}
		}

		// Output messsage and redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&id[]=' . $workshop,
			JText::_('COM_EVENTS_RESPONDENT_REMOVED')
		);
	}
}

