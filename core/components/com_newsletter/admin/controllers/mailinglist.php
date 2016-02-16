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
use Components\Newsletter\Tables\MailinglistEmail;
use Components\Newsletter\Tables\Mailinglist as MailList;
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
class Mailinglist extends AdminController
{
	/**
	 * Override execute method
	 *
	 * @return 	void
	 */
	public function execute()
	{
		$this->disableDefaultTask();
		$this->registerTask('', 'display');

		parent::execute();
	}

	/**
	 * Display Mailing Lists Task
	 *
	 * @return 	void
	 */
	public function displayTask()
	{
		// instantiate mailing list object
		$newsletterMailinglist = new MailList($this->database);
		$this->view->lists = $newsletterMailinglist->getLists();

		// diplay list of mailing lists
		$this->view->setLayout('display')->display();
	}

	/**
	 * Add Mailing List Task
	 *
	 * @return 	void
	 */
	public function addTask()
	{
		$this->editTask();
	}

	/**
	 * Edit Mailing List Task
	 *
	 * @return 	void
	 */
	public function editTask()
	{
		Request::setVar('hidemainmenu', 1);

		// default object
		$this->view->list = new stdClass;
		$this->view->list->id          = null;
		$this->view->list->name        = null;
		$this->view->list->description = null;
		$this->view->list->private     = null;
		$this->view->list->deleted     = null;
		$this->view->list->email_count = null;

		// get request vars
		$ids = Request::getVar('id', array());
		$id = (isset($ids[0])) ? $ids[0] : null;

		// are we editing or adding a new list
		if ($id)
		{
			$newsletterMailinglist = new MailList($this->database);
			$this->view->list = $newsletterMailinglist->getLists($id);
		}

		// set errors if we have any
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		// ouput
		$this->view->setLayout('edit')->display();
	}

	/**
	 * Save Mailing List Task
	 *
	 * @return 	void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// get request vars
		$list = Request::getVar('list', array(), 'post');

		// instantiate mailing list object
		$newsletterMailinglist = new MailList($this->database);

		// save mailing list
		if ($newsletterMailinglist->save($list))
		{
			App::redirect(
				Route::url('index.php?option=com_newsletter&controller=mailinglist', false),
				Lang::txt('COM_NEWSLETTER_MAILINGLIST_SAVE_SUCCESS')
			);
		}
		else
		{
			$this->setError($newsletterMailinglist->getError());
			$this->editTask();
			return;
		}
	}

	/**
	 * Delete Mailing List Task
	 *
	 * @return 	void
	 */
	public function deleteTask()
	{
		// get the request vars
		$ids = Request::getVar("id", array());

		// make sure we have ids
		if (isset($ids) && count($ids) > 0)
		{
			// delete each newsletter
			foreach ($ids as $id)
			{
				// instantiate mailing list object
				$newsletterMailinglist = new MailList($this->database);
				$newsletterMailinglist->load($id);

				// mark as deleted
				$newsletterMailinglist->deleted = 1;

				// save campaign marking as deleted
				if (!$newsletterMailinglist->save($newsletterMailinglist))
				{
					$this->setError(Lang::txt('COM_NEWSLETTER_MAILINGLIST_DELETE_FAILED'));
					$this->displayTask();
					return;
				}
			}
		}

		// redirect back to campaigns list
		App::redirect(
			Route::url('index.php?option=com_newsletter&controller=mailinglist', false),
			Lang::txt('COM_NEWSLETTER_MAILINGLIST_DELETE_SUCCESS')
		);
	}

	/**
	 * Manage Mailing List Task
	 *
	 * @return 	void
	 */
	public function manageTask()
	{
		//get request vars
		$ids = Request::getVar('id', array());
		$id = (isset($ids[0])) ? $ids[0] : null;

		//get request vars
		$this->view->id = $id;
		$this->view->filters['status'] = Request::getWord('status', 'active');
		$this->view->filters['sort']   = Request::getVar('sort', 'email ASC');

		//instantiate mailing list object
		$newsletterMailinglist      = new MailList($this->database);
		$newsletterMailinglistEmail = new MailingListEmail($this->database);

		//load mailing list
		$this->view->list = $newsletterMailinglist->getLists($this->view->id);

		//load mailing list emails
		$this->view->list_emails = $newsletterMailinglist->getListEmails($this->view->id, null, $this->view->filters);

		//set errors if we have any
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		//diplay list of mailing lists
		$this->view->setLayout('manage')->display();
	}

	/**
	 * Add to Mailing List Task
	 *
	 * @return 	void
	 */
	public function addEmailTask()
	{
		// get request vars
		$mailinglistId = Request::getVar('id', 0);
		$mailinglistId = (isset($mailinglistId[0])) ? $mailinglistId[0] : null;

		if (!$mailinglistId)
		{
			$mailinglistId = $this->mid;
		}

		// load mailing list
		$this->view->list = new MailList($this->database);
		$this->view->list->load($mailinglistId);

		// get list of groups
		$filters = array(
			'fields' => array('gidNumber', 'description'),
			'type'   => array('hub', 'project', 'super', 'course')
		);
		$this->view->groups = \Hubzero\User\Group::find($filters);

		// getting vars from do import
		$this->view->emailBox = $this->emailBox;

		// set errors if we have any
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		// output
		$this->view->setLayout('addemail')->display();
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
			$this->setError(Lang::txt('COM_NEWSLETTER_MAILINGLIST_MANAGE_SPECIFY_CONFIRMATION'));
			$this->addEmailTask();
			return;
		}

		// instantiate newletter mailing email object
		$newsletterMailinglist = new MailList($this->database);
		$newsletterMailinglist->load($this->mid);

		// get current emails on list
		$filters = array('status' => 'all');
		$currentEmails = array_keys($newsletterMailinglist->getListEmails($this->mid, $key ='email', $filters));

		// get com_media params
		$config = Component::params('com_media');

		// array of allowed extensions
		$allowedExtensions = array('txt','csv','xls','xlsx');

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
				$this->setError(Lang::txt('COM_NEWSLETTER_MAILINGLIST_MANAGE_FILE_TYPE_NOT_ALLOWED'));
				$this->addEmailTask();
				return;
			}

			// make sure were within file limits
			if ($this->emailFile['size'] > $maxFileSize)
			{
				$this->setError(Lang::txt('COM_NEWSLETTER_MAILINGLIST_MANAGE_FILE_TOO_BIG'));
				$this->addEmailTask();
				return;
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
				$this->addEmailTask();
				return;
			}
		}

		// merge emails from file and box
		$emails = array_merge($emailFileEmails, $emailGroupEmails, $emailBoxEmails);

		// make sure we have distinct emails
		$emails = array_unique($emails);

		// check that they are valid emails
		$inserts = array();
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
				$inserts[] = "(" . $this->mid . ", '" . $email . "', 'active', 0, '" . \Date::toSql() . "')";
			}
		}

		// do we have something to add
		if (count($inserts) > 0)
		{
			// add emails to mailing list
			$sql  = "INSERT INTO `#__newsletter_mailinglist_emails` (`mid`,`email`,`status`,`confirmed`,`date_added`) VALUES";
			$sql .= implode(", ", $inserts);
			$this->database->setQuery($sql);
			$this->database->query();

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
		App::redirect(
			Route::url('index.php?option=com_newsletter&controller=mailinglist&task=manage&id=' . $this->mid, false)
		);
	}

	/**
	 * Edit Email On Mailing List Task
	 *
	 * @return 	void
	 */
	public function editEmailTask()
	{
		// get request vars
		$id  = Request::getInt('id', 0);
		$mid = Request::getInt('mid', 0);

		// load mailing list
		$this->view->list = new MailList($this->database);
		$this->view->list->load($mid);

		// load email
		$this->view->email = new MailingListEmail($this->database);
		$this->view->email->load($id);

		// are we passing back an email
		if ($this->email)
		{
			$this->view->email->email = $this->email;
		}

		//set errors if we have any
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		// output
		$this->view->setLayout('editemail')->display();
	}

	/**
	 * Save Email On Mailing List Task
	 *
	 * @return 	void
	 */
	public function saveEmailTask()
	{
		// get request vars
		$email = Request::getVar('email', array(), 'post');

		// instantiate mailing list object
		$newsletterMailinglistEmail = new MailingListEmail($this->database);

		// save email
		if ($newsletterMailinglistEmail->save($email))
		{
			App::redirect(
				Route::url('index.php?option=com_newsletter&controller=mailinglist&task=manage&id=' . $email['mid'], false),
				Lang::txt('COM_NEWSLETTER_MAILINGLIST_SAVE_EMAIL_SUCCESS')
			);
		}
		else
		{
			Request::setVar('id', $email['id']);
			Request::setVar('mid', $email['mid']);
			$this->email = $email['email'];
			$this->setError(Lang::txt('COM_NEWSLETTER_MAILINGLIST_SAVE_EMAIL_FAILED'));
			$this->editEmailTask();
			return;
		}
	}

	/**
	 * Remove Email From Mailing List Task
	 *
	 * @return 	void
	 */
	public function deleteEmailTask()
	{
		// get request vars
		$ids = Request::getVar('email_id', array());
		$mailinglistId = Request::getVar('id', array());
		$mailinglistId = (isset($mailinglistId[0])) ? $mailinglistId[0] : null;

		// make sure we have ids
		if (isset($ids) && count($ids) > 0)
		{
			// delete each newsletter
			foreach ($ids as $id)
			{
				// instantiate mailing list object
				$newsletterMailinglistEmail = new MailingListEmail($this->database);
				$newsletterMailinglistEmail->load($id);

				// mark as removed
				$newsletterMailinglistEmail->status = 'removed';

				// delete mailing list email
				if (!$newsletterMailinglistEmail->save($newsletterMailinglistEmail))
				{
					$this->setError($newsletterMailinglistEmail->getError());
					$this->editEmailTask();
					return;
				}
			}
		}

		// inform and redirect
		App::redirect(
			Route::url('index.php?option=com_newsletter&controller=mailinglist&task=manage&id=' . $mailinglistId, false),
			Lang::txt('COM_NEWSLETTER_MAILINGLIST_DELETE_EMAIL_SUCCESS')
		);
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
				Route::url('index.php?option=com_newsletter&controller=mailinglist&task=manage&id=' . $mid, false),
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
	 * @return void
	 */
	public function sendConfirmationTask()
	{
		// get request vars
		$id = Request::getInt('id', 0);
		$mid = Request::getInt('mid', 0);

		// instantiate mailing list object
		$newsletterMailinglist = new MailList($this->database);
		$newsletterMailinglist->load($mid);

		// instantiate mailing list email object
		$newsletterMailinglistEmail = new MailingListEmail($this->database);
		$newsletterMailinglistEmail->load($id);

		// send confirmation email
		Helper::sendMailinglistConfirmationEmail($newsletterMailinglistEmail->email, $newsletterMailinglist, false);

		// inform user and redirect
		App::redirect(
			Route::url('index.php?option=com_newsletter&controller=mailinglist&task=manage&id=' . $mid, false),
			Lang::txt('COM_NEWSLETTER_MAILINGLIST_CONFIRMATION_SENT', $newsletterMailinglistEmail->email)
		);
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
		$newsletterMailinglist = new MailList($this->database);
		$newsletterMailinglist->load($id);

		// get list of emails
		$emails = $newsletterMailinglist->getListEmails($id, null, array('status' => 'all'));

		// file name
		$filename  = Lang::txt('COM_NEWSLETTER_MAILINGLIST_EXPORT_FILENAME', $newsletterMailinglist->name, Date::of('now')->format('m-d-Y'));
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
	public function cancelEmailTask()
	{
		$email = Request::getVar('email', array(), 'post');

		$mid = ($email['mid']) ? $email['mid'] : Request::getInt('mid');

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
		$mailinglistId = Request::getInt('mailinglistid', '-1');

		// get list of emails
		$newsletterMailinglist = new MailList($this->database);
		$filters = array('status' => 'active');
		$emails = array_keys($newsletterMailinglist->getListEmails($mailinglistId, 'email', $filters));

		// echo count of emails
		echo json_encode($emails);
	}

	/**
	 * Parse Email Content
	 *
	 * @param   $emails    Email Content
	 * @param   $separator Email Address Separator
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