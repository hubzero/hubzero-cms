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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Newsletter\Admin\Controllers;

use Components\Newsletter\Helpers\Helper;
use Components\Newsletter\Models\Mailinglist\Email;
use Components\Newsletter\Models\Mailinglist;
use Hubzero\Component\AdminController;
use stdClass;
use Request;
use Notify;
use Route;
use Date;
use Lang;
use App;

/**
 * Newsletter Mailing List Controller
 */
class Mailinglists extends AdminController
{
	/**
	 * Override execute method
	 *
	 * @return 	void
	 */
	public function execute()
	{
		$this->registerTask('add', 'edit');
		$this->registerTask('apply', 'save');
		//$this->disableDefaultTask();
		//$this->registerTask('', 'display');

		parent::execute();
	}

	/**
	 * Display Mailing Lists Task
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// instantiate mailing list object
		$lists = Mailinglist::all()
			->ordered()
			->rows();

		// diplay list of mailing lists
		$this->view
			->setLayout('display')
			->set('lists', $lists)
			->display();
	}

	/**
	 * Edit Mailing List Task
	 *
	 * @param   object  $row
	 * @return  void
	 */
	public function editTask($row = null)
	{
		if (!User::authorise('core.edit', $this->_option)
		 && !User::authorise('core.create', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		Request::setVar('hidemainmenu', 1);

		// Load object
		if (!is_object($row))
		{
			// Incoming
			$id = Request::getVar('id', array(0));
			$id = is_array($id) ? $id[0] : $id;

			$row = Mailinglist::oneOrNew($id);
		}

		// ouput
		$this->view
			->setLayout('edit')
			->set('row', $row)
			->display();
	}

	/**
	 * Save Mailing List Task
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken();

		if (!User::authorise('core.edit', $this->_option)
		 && !User::authorise('core.create', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Incoming data
		$fields = Request::getVar('newsletter', array(), 'post', 'array', 2);

		// Initiate model
		$row = Mailinglist::oneOrNew($fields['id'])->set($fields);

		// save mailing list
		if (!$row->save())
		{
			Notify::error($row->getError());
			return $this->editTask($row);
		}

		// Set success message
		Notify::success(Lang::txt('COM_NEWSLETTER_MAILINGLIST_SAVE_SUCCESS'));

		if ($this->getTask() == 'apply')
		{
			return $this->editTask($row);
		}

		// Redirect back to list
		$this->cancelTask();
	}

	/**
	 * Delete Mailing List Task
	 *
	 * @return  void
	 */
	public function deleteTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		if (!User::authorise('core.delete', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// get the request vars
		$ids = Request::getVar('id', array());

		// make sure we have ids
		$success = 0;

		if (isset($ids) && count($ids) > 0)
		{
			// delete each newsletter
			foreach ($ids as $id)
			{
				// instantiate mailing list object
				$row = Mailinglist::oneOrFail($id);

				// mark as deleted
				$row->set('deleted', 1);

				// save campaign marking as deleted
				if (!$row->save())
				{
					Notify::error(Lang::txt('COM_NEWSLETTER_MAILINGLIST_DELETE_FAILED'));
					continue;
				}

				$success++;
			}
		}

		if ($success)
		{
			Notify::success(Lang::txt('COM_NEWSLETTER_MAILINGLIST_DELETE_SUCCESS'));
		}

		// Redirect back to campaigns list
		$this->cancelTask();
	}

	/**
	 * Manage Mailing List Task
	 *
	 * @return  void
	 */
	public function manageTask()
	{
		if (!User::authorise('core.edit', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		//get request vars
		$ids = Request::getVar('id', array());
		$id = (isset($ids[0])) ? $ids[0] : 0;

		if (!$id)
		{
			return $this->cancelTask();
		}

		//get request vars
		$filters = array(
			'status' => Request::getWord('status', 'active'),
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'id'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'DESC'
			)
		);

		// Load mailing list
		$list = Mailinglist::oneOrFail($id);

		//load mailing list emails
		$model = $list->emails();

		if ($filters['status'] && $filters['status'] != 'all')
		{
			$model->whereEquals('status', $filters['status']);
		}

		$list_emails = $model
			->order($filters['sort'], $filters['sort_Dir'])
			->rows();

		//diplay list of mailing lists
		$this->view
			->setLayout('manage')
			->set('filters', $filters)
			->set('list', $list)
			->set('list_emails', $list_emails)
			->display();
	}

	/**
	 * Add to Mailing List Task
	 *
	 * @return 	void
	 */
	public function addEmailTask()
	{
		if (!User::authorise('core.edit', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		Request::setVar('hidemainmenu', 1);

		// get request vars
		$mailinglistId = Request::getVar('id', 0);
		$mailinglistId = (isset($mailinglistId[0])) ? $mailinglistId[0] : 0;

		// load mailing list
		$list = Mailinglist::oneOrFail($mailinglistId);

		// get list of groups
		$filters = array(
			'fields' => array('gidNumber', 'description'),
			'type'   => array('hub', 'project', 'super', 'course')
		);
		$groups = \Hubzero\User\Group::find($filters);

		// output
		$this->view
			->setLayout('addemail')
			->set('groups', $groups)
			->set('list', $list)
			->set('emailBox', (isset($this->emailBox) ? $this->emailBox : ''))
			->display();
	}

	/**
	 * Import to Mailing List Task
	 *
	 * @return 	void
	 */
	public function doAddEmailTask()
	{
		// array to hold emails
		$emails           = array();
		$duplicateEmails  = array();
		$badEmails        = array();
		$emailFileEmails  = array();
		$emailGroupEmails = array();
		$emailBoxEmails   = array();

		// get request vars
		$this->mid               = Request::getInt('mid', 0);
		$this->emailFile         = Request::getVar('email_file', array(), 'files');
		$this->emailGroup        = Request::getInt('email_group', 0);
		$this->emailBox          = Request::getVar('email_box', '');
		$this->emailConfirmation = Request::getVar('email_confirmation', '-1');

		// make sure we have selected whether or not to send confirmation emails
		if ($this->emailConfirmation == '-1')
		{
			Notify::warning(Lang::txt('COM_NEWSLETTER_MAILINGLIST_MANAGE_SPECIFY_CONFIRMATION'));
			return $this->addEmailTask();
		}

		// instantiate newletter mailing email object
		$newsletterMailinglist = Mailinglist::oneOrFail($this->mid);

		// get current emails on list
		$currentEmails = array();
		foreach ($newsletterMailinglist->emails as $email)
		{
			$currentEmails[] = $email->email;
		}

		// get com_media params
		$config = Component::params('com_media');

		// array of allowed extensions
		$allowedExtensions = array('txt', 'csv', 'xls', 'xlsx');

		// max file size
		$maxFileSize = (int) $config->get('upload_maxsize');
		$maxFileSize = $maxFileSize * 1024 * 1024;

		// if we have a file
		if (isset($this->emailFile['size']) && $this->emailFile['size'] > 0)
		{
			// make sure its an allowed file
			$pathInfo = pathinfo($this->emailFile['name']);

			if (!in_array(strtolower($pathInfo['extension']), $allowedExtensions))
			{
				Notify::error(Lang::txt('COM_NEWSLETTER_MAILINGLIST_MANAGE_FILE_TYPE_NOT_ALLOWED'));
				return $this->addEmailTask();
			}

			// make sure were within file limits
			if ($this->emailFile['size'] > $maxFileSize)
			{
				Notify::error(Lang::txt('COM_NEWSLETTER_MAILINGLIST_MANAGE_FILE_TOO_BIG'));
				return $this->addEmailTask();
			}

			// get contents of file
			$emailFileContents = file_get_contents($this->emailFile['tmp_name']);

			// parse emails
			$emailFileEmails = $this->_parseEmails($emailFileContents);
		}

		// do we have an email group
		if ($this->emailGroup != '' || $this->emailGroup != 0)
		{
			$hg = \Hubzero\User\Group::getInstance($this->emailGroup);
			$emailGroupEmails = $hg->getEmails('members');
		}

		// do we have a emails in the textarea
		if ($this->emailBox != '')
		{
			// parse emails
			$emailBoxEmails = $this->_parseEmails($this->emailBox);

			if ($emailBoxEmails === false)
			{
				return $this->addEmailTask();
			}
		}

		// merge emails from file and box
		$emails = array_merge($emailFileEmails, $emailGroupEmails, $emailBoxEmails);

		// make sure we have distinct emails
		$emails = array_unique($emails);

		// check that they are valid emails
		$inserts = 0;
		foreach ($emails as $k => $email)
		{
			if (!filter_var($email, FILTER_VALIDATE_EMAIL))
			{
				$badEmails[] = $email;
				unset($emails[$k]);
			}
			else if (in_array($email, $currentEmails))
			{
				$duplicateEmails[] = $email;
				unset($emails[$k]);
			}
			else
			{
				$insert = Email::blank();
				$insert->set(array(
					'mid'        => $this->mid,
					'email'      => $email,
					'status'     => 'active',
					'confirmed'  => 0,
					'date_added' => \Date::toSql()
				));
				if ($insert->save())
				{
					$inserts++;
				}
			}
		}

		// do we have something to add
		if ($inserts > 0)
		{
			// inform user of successfully addes
			Notify::success(Lang::txt('COM_NEWSLETTER_MAILINGLSIT_MANAGE_ADD_SUCCESS', count($emails), implode('<br />&mdash; ', $emails)));
		}

		// if we had an duplicate emails
		if (count($duplicateEmails) > 0)
		{
			Notify::warning(Lang::txt('COM_NEWSLETTER_MAILINGLSIT_MANAGE_ADD_DUPLICATE', count($duplicateEmails), implode('<br />&mdash; ', $duplicateEmails)));
		}

		// if we had an issue with emails
		if (count($badEmails) > 0)
		{
			Notify::error(Lang::txt('COM_NEWSLETTER_MAILINGLSIT_MANAGE_ADD_FAILED', count($badEmails), implode('<br />&mdash; ', $badEmails)));
		}

		// send confirmation emails
		if ($this->emailConfirmation)
		{
			// send confirmation emails to emails added
			foreach ($emails as $email)
			{
				// send confirmation email from helper
				Helper::sendMailinglistConfirmationEmail($email, $newsletterMailinglist, $addedByAdmin = true);
			}
		}

		// redirect back to mailing list manage page
		$this->cancelemailTask();
	}

	/**
	 * Edit Email On Mailing List Task
	 *
	 * @param   object  $row
	 * @return 	void
	 */
	public function editemailTask($row = null)
	{
		// get request vars
		$mid = Request::getInt('mid', 0);

		// load mailing list
		$list = Mailinglist::oneOrFail($mid);

		// load email
		if (!is_object($row))
		{
			$id  = Request::getInt('id', 0);
			$row = Email::oneOrFail($id);
		}

		// output
		$this->view
			->setLayout('editemail')
			->set('list', $list)
			->set('email', $row)
			->display();
	}

	/**
	 * Save Email On Mailing List Task
	 *
	 * @return 	void
	 */
	public function saveemailTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming data
		$fields = Request::getVar('fields', array(), 'post', 'array', 2);

		// Initiate model
		$row = Email::oneOrNew($fields['id'])->set($fields);

		// save mailing list
		if (!$row->save())
		{
			Notify::error($row->getError());
			return $this->editTask($row);
		}

		// Set success message
		Notify::success(Lang::txt('COM_NEWSLETTER_MAILINGLIST_SAVE_EMAIL_SUCCESS'));

		if ($this->getTask() == 'apply')
		{
			return $this->editemailTask($row);
		}

		// Redirect back to list
		$this->cancelemailTask();
	}

	/**
	 * Remove Email From Mailing List Task
	 *
	 * @return 	void
	 */
	public function deleteemailTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// get request vars
		$ids = Request::getVar('email_id', array());
		$mailinglistId = Request::getVar('id', array());
		$mailinglistId = (isset($mailinglistId[0])) ? $mailinglistId[0] : 0;

		// make sure we have ids
		$success = 0;

		if (isset($ids) && count($ids) > 0)
		{
			// delete each newsletter
			foreach ($ids as $id)
			{
				// instantiate mailing list object
				$email = Email::oneOrFail($id);

				// mark as removed
				$email->set('status', 'removed');

				// delete mailing list email
				if (!$email->save())
				{
					Notify::error($email->getError());
					continue;
				}

				$success++;
			}
		}

		if ($success)
		{
			Notify::success(Lang::txt('COM_NEWSLETTER_MAILINGLIST_DELETE_EMAIL_SUCCESS'));
		}

		// inform and redirect
		$this->cancelemailTask();
	}

	/**
	 * Add Email Back To Mailing List Task
	 *
	 * @return 	void
	 */
	public function subscribeEmailTask()
	{
		// get request vars
		$id = Request::getInt('id', 0);
		$mid = Request::getInt('mid', 0);

		// instantiate mailing list object
		$newsletterMailinglistEmail = new MailingListEmail($this->database);

		// load email
		$newsletterMailinglistEmail->load($id);

		// mark as removed
		$newsletterMailinglistEmail->status = 'active';

		// delete mailing list email
		if ($newsletterMailinglistEmail->save($newsletterMailinglistEmail))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=manage&id=' . $mid, false),
				Lang::txt('COM_NEWSLETTER_MAILINGLIST_SUBSCRIBED_EMAIL_SUCCESS')
			);
		}
		else
		{
			$this->setError($newsletterMailinglistEmail->getError());
			$this->manageTask();
			return;
		}
	}

	/**
	 * Send confirmation email task
	 * 
	 * @return  void
	 */
	public function sendConfirmationTask()
	{
		// get request vars
		$id  = Request::getInt('id', 0);
		$mid = Request::getInt('mid', 0);

		// instantiate mailing list object
		$mailinglist = Mailinglist::oneOrFail($mid);

		// instantiate mailing list email object
		$email = Email::oneOrFail($id);

		// send confirmation email
		Helper::sendMailinglistConfirmationEmail($email->email, $mailinglist, false);

		Notify::success(Lang::txt('COM_NEWSLETTER_MAILINGLIST_CONFIRMATION_SENT', $email->email));

		// inform user and redirect
		$this->cancelemailTask();
	}

	/**
	 * Export Mailing List Task
	 *
	 * @return 	void
	 */
	public function exportTask()
	{
		// get request vars
		$ids = Request::getVar('id', array());
		$id = (isset($ids[0])) ? $ids[0] : null;

		// instantiate mailing list object
		$mailinglist = Mailinglist::oneOrFail($id);

		// get list of emails
		$emails = $mailinglist
			->emails()
			->whereEquals('status', 'active')
			->rows();

		// file name
		$filename  = Lang::txt('COM_NEWSLETTER_MAILINGLIST_EXPORT_FILENAME', $mailinglist->name, Date::of('now')->format('m-d-Y'));
		$filename .= '.csv';

		// file contents
		$content = 'Email, Status' . PHP_EOL;
		foreach ($emails as $email)
		{
			$content .= $email->email . ", " . $email->status . PHP_EOL;
		}

		// set the headers for output
		header("Content-type: text/csv");
		header("Content-Disposition: attachment; filename={$filename}");
		header("Pragma: no-cache");
		header("Expires: 0");
		echo $content;
		exit();
	}

	/**
	 * Cancel Email Task
	 *
	 * @return  void
	 */
	public function cancelemailTask()
	{
		$email = Request::getVar('fields', array(), 'post');

		$mid = (isset($email['mid']) ? $email['mid'] : Request::getInt('mid', 0));

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=manage&id=' . $mid, false)
		);
	}

	/**
	 * Mailing List Email Count Task
	 *
	 * @return  void
	 */
	public function emailCountTask()
	{
		// get the mailing list
		$id = Request::getInt('mailinglistid', '-1');

		// instantiate mailing list object
		$mailinglist = Mailinglist::oneOrFail($id);

		// get list of emails
		$emails = $mailinglist
			->emails()
			->whereEquals('status', 'active')
			->rows();

		$items = array();
		foreach ($emails as $email)
		{
			$items[] = $email->email;
		}

		// echo count of emails
		echo json_encode($items);
	}

	/**
	 * Parse Email Content
	 *
	 * @param   $emails     Email Content
	 * @param   $separator  Email Address Separator
	 * @return  void
	 */
	private function _parseEmails($emails)
	{
		// array to hold parsed emails
		$parsedEmails = array();

		// split file by line break
		$parsedEmailLines = explode(PHP_EOL, $emails);

		// loop through each line and parse
		foreach ($parsedEmailLines as $emailLine)
		{
			// check to see if emails are in format: "Persons Name" <email@domain>
			if (preg_match_all('/"[^"]*"[^<]*<([^>]*)>/', $emailLine, $matches, PREG_SET_ORDER))
			{
				// loop through all matches and add captured address
				foreach ($matches as $match)
				{
					$parsedEmails[] = $match[1];
				}
			}
			// check to see if line contains comma
			else if (strstr($emailLine, ','))
			{
				$parsedEmails = array_merge($parsedEmails, explode(',', $emailLine));
			}
			// or contains a tab
			else if (strstr($emailLine, "\t"))
			{
				$parsedEmails = array_merge($parsedEmails, explode("\t", $emailLine));
			}
			else
			{
				$parsedEmails[] = $emailLine;
			}
		}

		// trim results
		$parsedEmails = array_map("trim", $parsedEmails);

		// strtolower results
		$parsedEmails = array_map("strtolower", $parsedEmails);

		// remove empty values
		$parsedEmails = array_filter($parsedEmails);

		// reset array keys
		$parsedEmails = array_values($parsedEmails);

		// remove duplicates
		$parsedEmails = array_unique($parsedEmails);

		// return parse emails
		return $parsedEmails;
	}
}
