<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Support\Site\Controllers;

use Components\Support\Tables\ReportAbuse;
use Hubzero\Component\SiteController;
use Hubzero\Utility\Sanitize;
use Hubzero\Utility\Validate;
use Request;
use Pathway;
use Config;
use Event;
use Route;
use Lang;
use User;
use Date;

/**
 * Report items as abusive
 */
class Abuse extends SiteController
{
	/**
	 * Method to set the document path
	 *
	 * @return  void
	 */
	protected function _buildPathway()
	{
		if (Pathway::count() <= 0)
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_option)),
				'index.php?option=' . $this->_option . '&controller=index'
			);
		}
		Pathway::append(
			Lang::txt(strtoupper('COM_SUPPORT_REPORT_ABUSE')),
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=reportabuse'
		);
	}

	/**
	 * Method to build and set the document title
	 *
	 * @return  void
	 */
	protected function _buildTitle()
	{
		$this->_title  = Lang::txt(strtoupper($this->_option));
		$this->_title .= ': ' . Lang::txt(strtoupper('COM_SUPPORT_REPORT_ABUSE'));

		\Document::setTitle($this->_title);
	}

	/**
	 * Reports an item as abusive
	 *
	 * @return     void
	 */
	public function displayTask()
	{
		// Login required
		if (User::isGuest())
		{
			$return = base64_encode(Request::getVar('REQUEST_URI', Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false, true), 'server'));
			$this->setRedirect(
				Route::url('index.php?option=com_users&view=login&return=' . $return, false)
			);
			return;
		}

		// Incoming
		$this->view->refid    = Request::getInt('id', 0);
		$this->view->parentid = Request::getInt('parent', 0);
		$this->view->cat      = Request::getVar('category', '');

		// Check for a reference ID
		if (!$this->view->refid)
		{
			throw new Exception(Lang::txt('COM_SUPPORT_ERROR_REFERENCE_ID_NOT_FOUND'), 404);
		}

		// Check for a category
		if (!$this->view->cat)
		{
			throw new Exception(Lang::txt('COM_SUPPORT_ERROR_CATEGORY_NOT_FOUND'), 404);
		}

		// Get the search result totals
		$results = Event::trigger('support.getReportedItem', array(
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
			$this->setError(Lang::txt('COM_SUPPORT_ERROR_REPORTED_ITEM_NOT_FOUND'));
		}

		// Set the page title
		$this->_buildTitle();

		$this->view->title = $this->_title;

		// Set the pathway
		$this->_buildPathway();

		// Output HTML
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		$this->view
			->setLayout('display')
			->display();
	}

	/**
	 * Save an abuse report and displays a "Thank you" message
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken() or jexit('Invalid Token');

		// Incoming
		$this->view->cat = Request::getVar('category', '');
		$this->view->refid = Request::getInt('referenceid', 0);
		$this->view->returnlink = Request::getVar('link', '');
		$no_html = Request::getInt('no_html', 0);

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
			Request::setVar('id', $this->view->refid);
			$this->setError($row->getError());
			$this->displayTask();
			return;
		}

		$row->report     = Sanitize::clean($row->report);
		$row->report     = nl2br($row->report);
		$row->created_by = User::get('id');
		$row->created    = Date::toSql();
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
			Request::setVar('id', $this->view->refid);
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
			Request::setVar('id', $this->view->refid);
			$this->setError($row->getError());
			$this->displayTask();
			return;
		}

		// Get the search result totals
		$results = Event::trigger('support.onReportItem', array(
			$this->view->refid,
			$this->view->cat
		));

		// Send notification email
		if ($this->config->get('abuse_notify', 1))
		{
			$reported = new \stdClass;
			$reported->author = 0;

			// Get the search result totals
			$results = Event::trigger('support.getReportedItem', array(
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
			$defs = str_replace('\n', "\n", $defs);
			$defs = explode("\n", $defs);
			$defs = array_map('trim', $defs);

			$message = new \Hubzero\Mail\Message();
			$message->setSubject(Config::get('sitename') . ' ' . Lang::txt('COM_SUPPORT_ABUSE_REPORT'))
					->addFrom(
						Config::get('mailfrom'),
						Config::get('sitename') . ' ' . Lang::txt(strtoupper($this->_option))
					)
					->addHeader('X-Component', 'com_support')
					->addHeader('X-Component-Object', 'abuse_item_report');

			// Plain text email
			$eview = new \Hubzero\Mail\View(array(
				'name'   => 'emails',
				'layout' => 'abuse_plain'
			));
			$eview->option     = $this->_option;
			$eview->controller = $this->_controller;
			$eview->report     = $row;
			$eview->reported   = $reported;
			$eview->author     = null;

			$plain = $eview->loadTemplate(false);
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
				// Check if the address should come from Joomla config
				if ($def == '{config.mailfrom}')
				{
					$def = Config::get('mailfrom');
				}

				// Check for a valid address
				if (Validate::email($def))
				{
					$message->addTo($def);
				}
			}

			// Send e-mail
			if (!$message->send())
			{
				$this->setError(Lang::txt('Uh-oh'));
			}
		}

		if ($no_html)
		{
			echo json_encode(array(
				'success'   => true,
				'report_id' => $row->id,
				'message'   => Lang::txt('COM_SUPPORT_REPORT_NUMBER_REFERENCE', $row->id),
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
