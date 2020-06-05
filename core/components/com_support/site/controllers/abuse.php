<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Support\Site\Controllers;

use Components\Support\Models\Report;
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
use App;

include_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'report.php';

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
			$return = base64_encode(Request::getString('REQUEST_URI', Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false, true), 'server'));
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . $return, false)
			);
			return;
		}

		// Incoming
		$refid    = Request::getInt('id', 0);
		$parentid = Request::getInt('parent', 0);
		$cat      = Request::getString('category', '');

		// Check for a reference ID
		if (!$refid)
		{
			App::abort(404, Lang::txt('COM_SUPPORT_ERROR_REFERENCE_ID_NOT_FOUND'));
		}

		// Check for a category
		if (!$cat)
		{
			App::abort(404, Lang::txt('COM_SUPPORT_ERROR_CATEGORY_NOT_FOUND'));
		}

		// Get the search result totals
		$results = Event::trigger('support.getReportedItem', array(
			$refid,
			$cat,
			$parentid
		));

		// Check the results returned for a reported item
		$report = null;

		if ($results)
		{
			foreach ($results as $result)
			{
				if ($result)
				{
					$report = $result[0];
				}
			}
		}

		// Ensure we found a reported item
		if (!$report)
		{
			$this->setError(Lang::txt('COM_SUPPORT_ERROR_REPORTED_ITEM_NOT_FOUND'));
		}

		// Set the page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		// Output HTML
		$this->view
			->set('title', $this->_title)
			->set('refid', $refid)
			->set('cat', $cat)
			->set('parentid', $parentid)
			->set('report', $report)
			->setErrors($this->getErrors())
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
		Request::checkToken();

		// Incoming
		$cat = Request::getString('category', '');
		$refid = Request::getInt('referenceid', 0);
		$returnlink = Request::getString('link', '');
		$no_html = Request::getInt('no_html', 0);

		// Trim and addslashes all posted items
		$incoming = array_map('trim', $_POST);

		// Initiate class and bind posted items to database fields
		$row = Report::blank()->set(array(
			'report'      => (isset($incoming['report']) ? $incoming['report'] : ''),
			'category'    => (isset($incoming['category']) ? $incoming['category'] : ''),
			'referenceid' => (isset($incoming['referenceid']) ? $incoming['referenceid'] : 0),
			'subject'     => (isset($incoming['subject']) ? $incoming['subject'] : 'Other')
		));

		$row->set('report', Sanitize::clean($row->get('report')));
		$row->set('report', nl2br($row->get('report')));
		$row->set('created_by', User::get('id'));
		$row->set('created', Date::toSql());
		$row->set('state', 0);

		// Store new content
		if (!$row->save())
		{
			if ($no_html)
			{
				echo json_encode(array(
					'success'  => false,
					'message'  => $row->getError(),
					'id'       => $refid,
					'category' => $cat
				));
				return;
			}
			Request::setVar('id', $refid);

			$this->setError($row->getError());
			return $this->displayTask();
		}

		// Get the search result totals
		$results = Event::trigger('support.onReportItem', array(
			$refid,
			$cat
		));

		// Send notification email
		if ($this->config->get('abuse_notify', 1))
		{
			Lang::load($this->_option . '.abuse', dirname(__DIR__));

			$reported = new \stdClass;
			$reported->author = 0;

			// Get the search result totals
			$results = Event::trigger('support.getReportedItem', array(
				$refid,
				$cat,
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
			foreach ($defs as $def)
			{
				// Check if the address should come from site config
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
				'report_id' => $row->get('id'),
				'message'   => Lang::txt('COM_SUPPORT_REPORT_NUMBER_REFERENCE', $row->id),
				'id'        => $refid,
				'category'  => $cat
			));
			return;
		}

		// Set the page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		// Output HTML
		$this->view
			->set('title', $this->_title)
			->set('report', $row)
			->set('refid', $refid)
			->set('cat', $cat)
			->set('returnlink', $returnlink)
			->display();
	}
}
