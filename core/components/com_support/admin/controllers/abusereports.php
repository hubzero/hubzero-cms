<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Support\Admin\Controllers;

use Components\Support\Helpers\Utilities;
use Components\Support\Models\Report;
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

include_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'report.php';

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
		$filters = array(
			'state' => Request::getState(
				$this->_option . '.' . $this->_controller . '.state',
				'state',
				0,
				'int'
			),
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'created'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'ASC'
			)
		);

		// Fetch results
		$query = Report::all()
			->whereEquals('state', $filters['state']);

		$rows = $query
			->order($filters['sort'], $filters['sort_Dir'])
			->paginated('limitstart', 'limit')
			->rows();

		// Output the HTML
		$this->view
			->set('filters', $filters)
			->set('rows', $rows)
			->display();
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
		$id   = Request::getInt('id', 0);
		$cat = Request::getString('cat', '');

		// Ensure we have an ID to work with
		if (!$id)
		{
			return $this->cancelTask();
		}

		// Load the report
		$report =  Report::oneOrFail($id);

		// Get the parent ID
		$results = Event::trigger('support.getParentId', array(
			$report->get('referenceid'),
			$report->get('category')
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
			$report->get('referenceid'),
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
			$report->get('category'),
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

		// Output the HTML
		$this->view
			->set('report', $report)
			->set('title', $title)
			->set('reported', $reported)
			->set('parentid', $parentid)
			->display();
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
			return $this->cancelTask();
		}

		// Load the report
		$report = Report::oneOrFail($id);
		$report->set('state', 1);
		$report->set('reviewed', Date::toSql());
		$report->set('reviewed_by', User::get('id'));

		if (!$report->save())
		{
			Notify::error($report->getError());
			return $this->cancelTask();
		}

		// Remove the reported item and any other related processes that need be performed
		$results = Event::trigger('support.releaseReportedItem', array(
			$report->get('referenceid'),
			$parentid,
			$report->get('category')
		));

		Notify::success(Lang::txt('COM_SUPPORT_REPORT_ITEM_RELEASED_SUCCESSFULLY'));

		$this->cancelTask();
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
			return $this->cancelTask();
		}

		$email     = 1; // Turn off/on
		$gratitude = 1; // Turn off/on
		$message   = '';

		// Load the report
		$report = Report::oneOrFail($id);

		$report->set('reviewed', Date::toSql());
		$report->set('reviewed_by', User::get('id'));
		$report->set('note', Request::getString('note', ''));

		// Get the reported item
		$results = Event::trigger('support.getReportedItem', array(
			$report->get('referenceid'),
			$report->get('category'),
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
			$report->get('referenceid'),
			$parentid,
			$report->get('category'),
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
		$report->set('state', Report::STATE_DELETED);

		if (!$report->save())
		{
			Notify::error($report->getError());
			return $this->cancelTask();
		}

		// Notify item owner
		if ($email)
		{
			Lang::load($this->_option . '.abuse', dirname(dirname(__DIR__)) . '/site');

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
				'base_path' => dirname(dirname(__DIR__)) . DS . 'site',
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

			try {
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
				if (Utilities::checkValidEmail($user->get('email'))) {
					$message->send();
				}
			}
			catch (Exception $e) {
				// just ignore mailing if there is an exception or bad email address
			}
		}

		// Check the HUB configuration to see if banking is turned on
		$banking = \Component::params('com_members')->get('bankAccounts');

		// Give some points to whoever reported abuse
		if ($banking && $gratitude)
		{
			$BC = \Hubzero\Bank\Config::values();
			$ar = $BC->get('abusereport');  // How many points?
			if ($ar)
			{
				$ruser = User::getInstance($report->get('created_by'));

				if (is_object($ruser) && $ruser->get('id'))
				{
					$BTL = new \Hubzero\Bank\Teller($ruser->get('id'));
					$BTL->deposit($ar, Lang::txt('COM_SUPPORT_ACKNOWLEDGMENT_FOR_VALID_REPORT'), 'abusereport', $id);
				}
			}
		}

		Notify::success(Lang::txt('COM_SUPPORT_REPORT_ITEM_TAKEN_DOWN'));

		$this->cancelTask();
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

		if ($sample = Request::getString('sample', '', 'post'))
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
