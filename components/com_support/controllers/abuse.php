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

/**
 * Report items as abusive
 */
class SupportControllerAbuse extends \Hubzero\Component\SiteController
{
	/**
	 * Method to set the document path
	 *
	 * @return     void
	 */
	protected function _buildPathway()
	{
		$pathway = JFactory::getApplication()->getPathway();

		if (count($pathway->getPathWay()) <= 0)
		{
			$pathway->addItem(
				JText::_(strtoupper($this->_option)),
				'index.php?option=' . $this->_option . '&controller=index'
			);
		}
		$pathway->addItem(
			JText::_(strtoupper('COM_SUPPORT_REPORT_ABUSE')),
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=reportabuse'
		);
	}

	/**
	 * Method to build and set the document title
	 *
	 * @return     void
	 */
	protected function _buildTitle()
	{
		$this->_title = JText::_(strtoupper($this->_option));
		$this->_title .= ': ' . JText::_(strtoupper('COM_SUPPORT_REPORT_ABUSE'));

		$document = JFactory::getDocument();
		$document->setTitle($this->_title);
	}

	/**
	 * Reports an item as abusive
	 *
	 * @return     void
	 */
	public function displayTask()
	{
		// Login required
		if ($this->juser->get('guest'))
		{
			$return = base64_encode(JRequest::getVar('REQUEST_URI', JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false, true), 'server'));
			$this->setRedirect(
				JRoute::_('index.php?option=com_users&view=login&return=' . $return, false)
			);
			return;
		}

		$this->view->setLayout('display');
		$this->view->juser = $this->juser;

		// Incoming
		$this->view->refid = JRequest::getInt('id', 0);
		$this->view->parentid = JRequest::getInt('parent', 0);
		$this->view->cat = JRequest::getVar('category', '');

		// Check for a reference ID
		if (!$this->view->refid)
		{
			JError::raiseError(404, JText::_('COM_SUPPORT_ERROR_REFERENCE_ID_NOT_FOUND'));
			return;
		}

		// Check for a category
		if (!$this->view->cat)
		{
			JError::raiseError(404, JText::_('COM_SUPPORT_ERROR_CATEGORY_NOT_FOUND'));
			return;
		}

		// Load plugins
		JPluginHelper::importPlugin('support');
		$dispatcher = JDispatcher::getInstance();

		// Get the search result totals
		$results = $dispatcher->trigger('getReportedItem', array(
			$this->view->refid,
			$this->view->cat,
			$this->view->parentid
		));

		// Check the results returned for a reported item
		$this->view->report = null;
		if ($results)
		{
			foreach ($results as $result)
			{
				if ($result)
				{
					$this->view->report = $result[0];
				}
			}
		}

		// Ensure we found a reported item
		if (!$this->view->report)
		{
			$this->setError(JText::_('COM_SUPPORT_ERROR_REPORTED_ITEM_NOT_FOUND'));
		}

		// Set the page title
		$this->_buildTitle();

		$this->view->title = $this->_title;

		// Set the pathway
		$this->_buildPathway();

		// Output HTML
		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}
		$this->view->display();
	}

	/**
	 * Save an abuse report and displays a "Thank you" message
	 *
	 * @return     void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$this->view->cat = JRequest::getVar('category', '');
		$this->view->refid = JRequest::getInt('referenceid', 0);
		$this->view->returnlink = JRequest::getVar('link', '');
		$no_html = JRequest::getInt('no_html', 0);

		// Trim and addslashes all posted items
		$incoming = array_map('trim', $_POST);

		// Initiate class and bind posted items to database fields
		$row = new ReportAbuse($this->database);
		if (!$row->bind($incoming))
		{
			if ($no_html)
			{
				echo json_encode(array(
					'success'  => false,
					'message'  => $row->getError(),
					'id'       => $this->view->refid,
					'category' => $this->view->cat
				));
				return;
			}
			JRequest::setVar('id', $this->view->refid);
			$this->setError($row->getError());
			$this->displayTask();
			return;
		}

		$row->report     = \Hubzero\Utility\Sanitize::clean($row->report);
		$row->report     = nl2br($row->report);
		$row->created_by = $this->juser->get('id');
		$row->created    = JFactory::getDate()->toSql();
		$row->state      = 0;

		// Check content
		if (!$row->check())
		{
			if ($no_html)
			{
				echo json_encode(array(
					'success'  => false,
					'message'  => $row->getError(),
					'id'       => $this->view->refid,
					'category' => $this->view->cat
				));
				return;
			}
			JRequest::setVar('id', $this->view->refid);
			$this->setError($row->getError());
			$this->displayTask();
			return;
		}

		// Store new content
		if (!$row->store())
		{
			if ($no_html)
			{
				echo json_encode(array(
					'success'  => false,
					'message'  => $row->getError(),
					'id'       => $this->view->refid,
					'category' => $this->view->cat
				));
				return;
			}
			JRequest::setVar('id', $this->view->refid);
			$this->setError($row->getError());
			$this->displayTask();
			return;
		}

		// Get the search result totals
		JPluginHelper::importPlugin('support');
		$dispatcher = JDispatcher::getInstance();
		$results = $dispatcher->trigger('onReportItem', array(
			$this->view->refid,
			$this->view->cat
		));

		// Send notification email
		if ($this->config->get('abuse_notify', 1))
		{
			$reported = new stdClass;
			$reported->author = 0;

			// Get the search result totals
			$results = $dispatcher->trigger('getReportedItem', array(
				$this->view->refid,
				$this->view->cat,
				0
			));

			// Check the results returned for a reported item
			if ($results)
			{
				foreach ($results as $result)
				{
					if ($result)
					{
						$reported = $result[0];
						break;
					}
				}
			}

			// Get any set emails that should be notified of ticket submission
			$defs = str_replace("\r", '', $this->config->get('abuse_emails', '{config.mailfrom}'));
			$defs = explode("\n", $defs);

			$jconfig = JFactory::getConfig();

			$message = new \Hubzero\Mail\Message();
			$message->setSubject($jconfig->getValue('config.sitename') . ' ' . JText::_('COM_SUPPORT_REPORT_ABUSE'))
					->addFrom(
						$jconfig->getValue('config.mailfrom'),
						$jconfig->getValue('config.sitename') . ' ' . JText::_(strtoupper($this->_option))
					)
					->addHeader('X-Component', 'com_support')
					->addHeader('X-Component-Object', 'abuse_item_report');

			// Plain text email
			$eview = new \Hubzero\Component\View(array(
				'name'   => 'emails',
				'layout' => 'abuse_plain'
			));
			$eview->option     = $this->_option;
			$eview->controller = $this->_controller;
			$eview->report     = $row;
			$eview->reported   = $reported;

			$plain = $eview->loadTemplate();
			$plain = str_replace("\n", "\r\n", $plain);

			$message->addPart($plain, 'text/plain');

			// HTML email
			$eview->setLayout('abuse_html');

			$html = $eview->loadTemplate();
			$html = str_replace("\n", "\r\n", $html);

			$message->addPart($html, 'text/html');

			// Loop through the addresses
			foreach ($defs As $def)
			{
				$def = trim($def);

				// Check if the address should come from Joomla config
				if ($def == '{config.mailfrom}')
				{
					$def = $jconfig->getValue('config.mailfrom');
				}

				// Check for a valid address
				if (\Hubzero\Utility\Validate::email($def))
				{
					$message->addTo($def);
				}
			}

			// Send e-mail
			if (!$message->send())
			{
				$this->setError(JText::_('Uh-oh'));
			}
		}

		if ($no_html)
		{
			echo json_encode(array(
				'success'   => true,
				'report_id' => $row->id,
				'message'   => JText::sprintf('COM_SUPPORT_REPORT_NUMBER_REFERENCE', $row->id),
				'id'        => $this->view->refid,
				'category'  => $this->view->cat
			));
			return;
		}

		// Set the page title
		$this->_buildTitle();

		$this->view->title = $this->_title;
		$this->view->report = $row;

		// Set the pathway
		$this->_buildPathway();

		// Output HTML
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		$this->view->display();
	}
}
