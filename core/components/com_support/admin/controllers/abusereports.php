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

namespace Components\Support\Admin\Controllers;

use Components\Support\Helpers\Utilities;
use Components\Support\Tables\ReportAbuse;
use Hubzero\Component\AdminController;
use Hubzero\Mail\Message;
use Hubzero\Mail\View;
use Exception;
use Component;
use Request;
use Config;
use Event;
use Route;
use Lang;
use User;
use Date;
use App;

include_once(dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'reportabuse.php');

/**
 * Support cotnroller for Abuse Reports
 */
class Abusereports extends AdminController
{
	/**
	 * Displays a list of records
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Incoming
		$this->view->filters = array(
			'limit' => Request::getState(
				$this->_option . '.' . $this->_controller . '.limit',
				'limit',
				Config::get('list_limit'),
				'int'
			),
			'start' => Request::getState(
				$this->_option . '.' . $this->_controller . '.limitstart',
				'limitstart',
				0,
				'int'
			),
			'state' => Request::getState(
				$this->_option . '.' . $this->_controller . '.state',
				'state',
				0,
				'int'
			),
			'sortby' => Request::getVar('sortby', 'a.created DESC')
		);

		$model = new ReportAbuse($this->database);

		// Get record count
		$this->view->total = $model->getCount($this->view->filters);

		// Get records
		$this->view->rows  = $model->getRecords($this->view->filters);

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Display a record
	 *
	 * @return  void
	 */
	public function viewTask()
	{
		Request::setVar('hidemainmenu', 1);

		// Incoming
		$id = Request::getInt('id', 0);
		$cat = Request::getVar('cat', '');

		// Ensure we have an ID to work with
		if (!$id)
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
			);
			return;
		}

		// Load the report
		$report = new ReportAbuse($this->database);
		$report->load($id);

		// Get the parent ID
		$results = Event::trigger('support.getParentId', array(
			$report->referenceid,
			$report->category
		));

		// Check the results returned for a parent ID
		$parentid = 0;
		if ($results)
		{
			foreach ($results as $result)
			{
				if ($result)
				{
					$parentid = $result;
				}
			}
		}

		// Get the reported item
		$results = Event::trigger('support.getReportedItem', array(
			$report->referenceid,
			$cat,
			$parentid
		));

		// Check the results returned for a reported item
		$reported = null;
		if ($results)
		{
			foreach ($results as $result)
			{
				if ($result)
				{
					$reported = $result[0];
				}
			}
		}

		// Get the title
		$titles = Event::trigger('support.getTitle', array(
			$report->category,
			$parentid
		));

		// Check the results returned for a title
		$title = null;
		if ($titles)
		{
			foreach ($titles as $t)
			{
				if ($t)
				{
					$title = $t;
				}
			}
		}

		$this->view->report = $report;
		$this->view->reported = $reported;
		$this->view->parentid = $parentid;
		$this->view->title = $title;

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Release a record from being marked as abusive
	 *
	 * @return  void
	 */
	public function releaseTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$id = Request::getInt('id', 0);
		$parentid = Request::getInt('parentid', 0);

		// Ensure we have an ID to work with
		if (!$id)
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
			);
			return;
		}

		// Load the report
		$report = new ReportAbuse($this->database);
		$report->load($id);
		$report->state = 1;
		$report->reviewed = Date::toSql();
		$report->reviewed_by = User::get('id');
		if (!$report->store())
		{
			throw new Exception($report->getError(), 500);
		}

		// Remove the reported item and any other related processes that need be performed
		$results = Event::trigger('support.releaseReportedItem', array(
			$report->referenceid,
			$parentid,
			$report->category
		));

		// Redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('COM_SUPPORT_REPORT_ITEM_RELEASED_SUCCESSFULLY')
		);
	}

	/**
	 * Mark record as spam
	 *
	 * @return  void
	 */
	public function spamTask()
	{
		$this->removeTask(true);
	}

	/**
	 * Delete a record
	 *
	 * @param   boolean  $isSpam
	 * @return  void
	 */
	public function removeTask($isSpam=false)
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$id = Request::getInt('id', 0);
		$parentid = Request::getInt('parentid', 0);

		// Ensure we have an ID to work with
		if (!$id)
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
			);
			return;
		}

		$email     = 1; // Turn off/on
		$gratitude = 1; // Turn off/on
		$message   = '';

		// Load the report
		$report = new ReportAbuse($this->database);
		$report->load($id);

		$report->reviewed = Date::toSql();
		$report->reviewed_by = User::get('id');
		$report->note = Request::getVar('note', '');

		// Get the reported item
		$results = Event::trigger('support.getReportedItem', array(
			$report->referenceid,
			$report->category,
			$parentid
		));

		// Check the results returned for a reported item
		$reported = null;
		if ($results)
		{
			foreach ($results as $result)
			{
				if ($result)
				{
					$reported = $result[0];
				}
			}
		}

		// Remove the reported item and any other related processes that need be performed
		$results = Event::trigger('support.deleteReportedItem', array(
			$report->referenceid,
			$parentid,
			$report->category,
			$message
		));

		if ($results)
		{
			foreach ($results as $result)
			{
				if ($result)
				{
					$message .= $result;
				}
			}
		}

		if ($isSpam)
		{
			$results = Event::trigger('antispam.onAntispamTrain', array(
				$reported->text,
				$isSpam
			));
		}

		// Mark abuse report as deleted
		$report->state = 2;
		if (!$report->store())
		{
			throw new Exception($report->getError(), 500);
		}

		// Notify item owner
		if ($email)
		{
			$user = User::getInstance($reported->author);

			// Email "from" info
			$from = array(
				'name'  => Config::get('sitename') . ' ' . Lang::txt('COM_SUPPORT'),
				'email' => Config::get('mailfrom'),
				'multipart' => md5(date('U'))
			);

			// Email subject
			$subject = Lang::txt('COM_SUPPORT_REPORT_ABUSE_EMAIL_SUBJECT', Config::get('sitename'));

			// Plain text
			$eview = new View(array(
				'base_path' => PATH_CORE . DS . 'components' . DS . 'com_support' . DS . 'site',
				'name'      => 'emails',
				'layout'    => 'abuse_plain'
			));
			$eview->option     = $this->_option;
			$eview->controller = $this->_controller;
			$eview->reported   = $reported;
			$eview->report     = $report;
			$eview->author     = $user;

			$plain = $eview->loadTemplate(false);
			$plain = str_replace("\n", "\r\n", $plain);

			// HTML
			$eview->setLayout('abuse_html');

			$html = $eview->loadTemplate();
			$html = str_replace("\n", "\r\n", $html);

			// Build message
			$message = new Message();
			$message->setSubject($subject)
			        ->addFrom($from['email'], $from['name'])
			        ->addTo($user->get('email'), $user->get('name'))
			        ->addHeader('X-Component', 'com_support')
			        ->addHeader('X-Component-Object', 'abuse_item_removal');

			$message->addPart($plain, 'text/plain');

			$message->addPart($html, 'text/html');

			// Send the email
			if (Utilities::checkValidEmail($user->get('email')))
			{
				$message->send();
			}
		}

		// Check the HUB configuration to see if banking is turned on
		$upconfig = Component::params('com_members');
		$banking = $upconfig->get('bankAccounts');

		// Give some points to whoever reported abuse
		if ($banking && $gratitude)
		{
			$BC = \Hubzero\Bank\Config::values();
			$ar = $BC->get('abusereport');  // How many points?
			if ($ar)
			{
				$ruser = User::getInstance($report->created_by);
				if (is_object($ruser) && $ruser->get('id'))
				{
					$BTL = new \Hubzero\Bank\Teller($ruser->get('id'));
					$BTL->deposit($ar, Lang::txt('COM_SUPPORT_ACKNOWLEDGMENT_FOR_VALID_REPORT'), 'abusereport', $id);
				}
			}
		}

		// Redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('COM_SUPPORT_REPORT_ITEM_TAKEN_DOWN')
		);
	}

	/**
	 * Displays a list of records
	 *
	 * @return  void
	 */
	public function checkTask()
	{
		$results = null;
		$sample  = '';

		if ($sample = Request::getVar('sample', '', 'post', 'none', 2))
		{
			$service = new \Hubzero\Spam\Checker();

			foreach (Event::trigger('antispam.onAntispamDetector') as $detector)
			{
				if (!$detector)
				{
					continue;
				}

				$service->registerDetector($detector);
			}

			$service->check($sample);

			$results = $service->getReport();
		}

		// Output the HTML
		$this->view
			->set('sample', $sample)
			->set('results', $results)
			->setErrors($this->getErrors())
			->display();
	}
}
