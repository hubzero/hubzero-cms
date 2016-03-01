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

use Components\Newsletter\Tables\Newsletter as Letter;
use Components\Newsletter\Tables\Template;
use Components\Newsletter\Tables\MailingList;
use Components\Newsletter\Tables\Mailing;
use Components\Newsletter\Tables\PrimaryStory;
use Components\Newsletter\Tables\SecondaryStory;
use Hubzero\Component\AdminController;
use Hubzero\Config\Registry;
use stdClass;
use Request;
use Config;
use Notify;
use Route;
use Lang;
use User;
use Date;
use App;

/**
 * Newsletters controller
 */
class Newsletter extends AdminController
{
	/**
	 * Dependency check
	 *
	 * @return  void
	 */
	private function dependencyCheck()
	{
		$sql = "SELECT * FROM `#__cron_jobs` WHERE `plugin`=" . $this->database->quote('newsletter') . " AND `event`=" . $this->database->quote('processMailings');
		$this->database->setQuery($sql);
		$sendMailingsCronJob = $this->database->loadObject();

		//if we dont have an object create new cron job
		if (!is_object($sendMailingsCronJob))
		{
			return false;
		}

		//is the cron job turned off
		if (is_object($sendMailingsCronJob) && $sendMailingsCronJob->state == 0)
		{
			return false;
		}

		return true;
	}

	/**
	 * Display all newsletters task
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// dependency check
		if (!$this->dependencyCheck())
		{
			// show missing dependency layout
			$this->view->setLayout('dependency')->display();
			return;
		}

		// instantiate newsletter campaign object
		$newsletterNewsletter = new Letter($this->database);

		// get list of all newsletter campaigns
		$this->view->newsletters = $newsletterNewsletter->getNewsletters();

		// get any templates that exist
		$newsletterTemplate = new Template($this->database);
		$this->view->templates = $newsletterTemplate->getTemplates();

		// Set any errors if we have any
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		// Output the HTML
		$this->view->setLayout('display')->display();
	}

	/**
	 * Add newsletter task - directs to editTask()
	 *
	 * @return 	void
	 */
	public function addTask()
	{
		$this->editTask('add');
	}

	/**
	 * Edit newsletter task
	 *
	 * @return 	void
	 */
	public function editTask($task = 'edit')
	{
		// instantiate newsletter object
		$this->view->newsletter = new stdClass;
		$this->view->newsletter->id          = null;
		$this->view->newsletter->alias       = null;
		$this->view->newsletter->name        = null;
		$this->view->newsletter->template    = null;
		$this->view->newsletter->issue       = null;
		$this->view->newsletter->date        = null;
		$this->view->newsletter->sent        = null;
		$this->view->newsletter->type        = null;
		$this->view->newsletter->tracking    = 1;
		$this->view->newsletter->published   = null;
		$this->view->newsletter->created     = null;
		$this->view->newsletter->created_by  = null;
		$this->view->newsletter->modified    = null;
		$this->view->newsletter->modified_by = null;
		$this->view->newsletter->params      = null;

		/** This is used to determine frequency
		 * 0 - regular/disabled
		 * 1 - daily
		 * 2 - weekly
		 * 3 - monthly
		 **/
		$this->view->newsletter->autogen		 = 0;

		// default primary and secondary stories to null
		$this->view->newsletter_primary    = null;
		$this->view->newsletter_secondary  = null;

		// get any templates that exist
		$newsletterTemplate = new Template($this->database);
		$this->view->templates = $newsletterTemplate->getTemplates();

		// get the request vars
		$id = Request::getVar('id', array(0));
		if (is_array($id))
		{
			$id = (isset($id[0]) ? $id[0] : null);
		}

		if ($task == 'add')
		{
			$id = null;
		}

		// are we editing
		if ($id)
		{
			$newsletterNewsletter = new Letter($this->database);
			if ($letter = $newsletterNewsletter->getNewsletters($id))
			{
				$this->view->newsletter = $letter;
			}

			// get primary stories
			$newsletterPrimaryStory = new PrimaryStory($this->database);
			$this->view->newsletter_primary = $newsletterPrimaryStory->getStories($id);
			$this->view->newsletter_primary_highest_order = $newsletterPrimaryStory->_getCurrentHighestOrder($id);

			// get secondary stories
			$newsletterSecondaryStory = new SecondaryStory($this->database);
			$this->view->newsletter_secondary = $newsletterSecondaryStory->getStories($id);
			$this->view->newsletter_secondary_highest_order = $newsletterSecondaryStory->_getCurrentHighestOrder($id);

			// get mailing lists
			$newsletterMailinglist = new Mailinglist($this->database);
			$this->view->mailingLists = $newsletterMailinglist->getLists();
		}

		// are we passing newsletter object from saveTask()?
		if ($this->newsletter)
		{
			$this->view->newsletter = $this->newsletter;
		}

		// check if we have any errors
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		// set vars for view
		$this->view->config = $this->config;

		// Output the HTML
		$this->view->setLayout('edit')->display();
	}

	/**
	 * Apply changes to newsletter
	 * 
	 * @return void
	 */
	public function applyTask()
	{
		$this->saveTask($apply = true);
	}

	/**
	 * Save campaign task
	 *
	 * @return 	void
	 */
	public function saveTask($apply = false)
	{
		//get post
		$newsletter = Request::getVar("newsletter", array(), 'post', 'ARRAY', JREQUEST_ALLOWHTML);

		//make sure we have valid alias
		if ($newsletter['alias'])
		{
			$newsletter['alias'] = str_replace(" ", "", strtolower($newsletter['alias']));
		}
		else
		{
			$newsletter['alias'] = str_replace(" ", "", strtolower($newsletter['name']));
		}

		//get unique newsletter name
		$newsletterId        = (isset($newsletter['id'])) ? $newsletter['id'] : null;
		$newsletter['alias'] = $this->_getUniqueNewsletterAlias($newsletter['alias'], $newsletterId);

		//instantiate campaign object
		$newsletterNewsletter = new Letter($this->database);

		//do we need to set the created and created_by
		if (!isset($newsletter['id']))
		{
			//update the modified info
			$newsletter['created']    = Date::toSql();
			$newsletter['created_by'] = User::get('id');
		}
		else
		{
			$newsletterNewsletter->load($newsletter['id']);
		}

		//did we have params
		if (isset($newsletter['params']))
		{
			//load previous params
			$params = new Registry($newsletterNewsletter->params);

			//set from name
			if (isset($newsletter['params']['from_name']))
			{
				$params->set('from_name', $newsletter['params']['from_name']);
			}

			//set from address
			if (isset($newsletter['params']['from_address']))
			{
				$params->set('from_address', $newsletter['params']['from_address']);
			}

			//set reply-to name
			if (isset($newsletter['params']['replyto_name']))
			{
				$params->set('replyto_name', $newsletter['params']['replyto_name']);
			}

			//set reply-to address
			if (isset($newsletter['params']['replyto_address']))
			{
				$params->set('replyto_address', $newsletter['params']['replyto_address']);
			}

			//newsletter params to string
			$newsletter['params'] = $params->toString();
		}

		//update the modified info
		$newsletter['modified']    = Date::toSql();
		$newsletter['modified_by'] = User::get('id');

		// if no plain text was entered lets take the html content
		if (isset($newsletter['plain_content']))
		{
			if ($newsletter['plain_content'] == '')
			{
				$newsletter['plain_content'] = strip_tags($newsletter['html_content']);
				$newsletter['plain_content'] = preg_replace('/(?:(?:\r\n|\r|\n)\s*){2}\n/', '', $newsletter['plain_content']);
			}

			// remove html from plain content
			$newsletter['plain_content'] = strip_tags($newsletter['plain_content']);
		}

		//save campaign
		if (!$newsletterNewsletter->save($newsletter))
		{
			$this->newsletter = new stdClass;
			$this->newsletter->id            = $newsletterNewsletter->id;
			$this->newsletter->alias         = $newsletterNewsletter->alias;
			$this->newsletter->name          = $newsletterNewsletter->name;
			$this->newsletter->issue         = $newsletterNewsletter->issue;
			$this->newsletter->type          = $newsletterNewsletter->type;
			$this->newsletter->template      = $newsletterNewsletter->template;
			$this->newsletter->published     = $newsletterNewsletter->published;
			$this->newsletter->sent          = $newsletterNewsletter->sent;
			$this->newsletter->html_content  = $newsletterNewsletter->html_content;
			$this->newsletter->plain_content = $newsletterNewsletter->plain_content;
			$this->newsletter->tracking      = $newsletterNewsletter->tracking;
			$this->newsletter->created       = $newsletterNewsletter->created;
			$this->newsletter->created_by    = $newsletterNewsletter->created_by;
			$this->newsletter->modified      = $newsletterNewsletter->modified;
			$this->newsletter->modified_by   = $newsletterNewsletter->modified_by;
			$this->newsletter->params        = $newsletterNewsletter->params;

			// set the id so we can pick up the stories
			Request::setVar('id', array($this->newsletter->id));

			$this->setError($newsletterNewsletter->getError());
			$this->editTask();
			return;
		}
		else
		{
			// set success message
			Notify::success(Lang::txt('COM_NEWSLETTER_SAVED_SUCCESS'));

			// redirect back to campaigns list
			App::redirect(Route::url('index.php?option=com_newsletter&controller=newsletter', false));

			// if we just created campaign go back to edit form so we can add content
			if (!isset($newsletter['id']) || $apply)
			{
				App::redirect(
					Route::url('index.php?option=com_newsletter&controller=newsletter&task=edit&id=' . $newsletterNewsletter->id, false)
				);
			}
		}
	}

	/**
	 * Duplicate newsletters
	 *
	 * @return 	void
	 */
	public function duplicateTask()
	{
		// get the request vars
		$ids = Request::getVar("id", array());

		// make sure we have ids
		if (isset($ids) && count($ids) > 0)
		{
			// delete each newsletter
			foreach ($ids as $id)
			{
				// instantiate newsletter object
				$newsletterNewsletter = new Letter($this->database);
				$newsletterNewsletter->duplicate($id);
			}
		}

		// redirect back to campaigns list
		App::redirect(
			Route::url('index.php?option=com_newsletter&controller=newsletter', false),
			Lang::txt('COM_NEWSLETTER_DUPLICATED_SUCCESS')
		);
	}

	/**
	 * Delete Task
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
				// instantiate newsletter object
				$newsletterNewsletter = new Letter($this->database);
				$newsletterNewsletter->load($id);

				// mark as deleted
				$newsletterNewsletter->deleted = 1;

				// save campaign marking as deleted
				if (!$newsletterNewsletter->save($newsletterNewsletter))
				{
					$this->setError(Lang::txt('COM_NEWSLETTER_DELETE_FAIL'));
					$this->displayTask();
					return;
				}
			}
		}

		// redirect back to campaigns list
		App::redirect(
			Route::url('index.php?option=com_newsletter&controller=newsletter', false),
			Lang::txt('COM_NEWSLETTER_DELETE_SUCCESS')
		);
	}

	/**
	 * Publish newsletter task
	 *
	 * @return 	void
	 */
	public function publishTask()
	{
		$this->togglePublishedStateTask(1);
	}

	/**
	 * Unpublish newsletter task
	 *
	 * @return 	void
	 */
	public function unpublishTask()
	{
		$this->togglePublishedStateTask(0);
	}

	/**
	 * Toggle published state for newsletter
	 *
	 * @return 	void
	 */
	private function togglePublishedStateTask($publish = 1)
	{
		//get the request vars
		$ids = Request::getVar("id", array());

		//make sure we have ids
		if (isset($ids) && count($ids) > 0)
		{
			//delete each newsletter
			foreach ($ids as $id)
			{
				//instantiate newsletter object
				$newsletterNewsletter = new Letter($this->database);
				$newsletterNewsletter->load($id);

				//mark as deleted
				$newsletterNewsletter->published = $publish;

				//save campaign marking as deleted
				if (!$newsletterNewsletter->save($newsletterNewsletter))
				{
					$this->setError(Lang::txt('COM_NEWSLETTER_STATE_CHANGE_FAIL'));
					$this->displayTask();
					return;
				}
			}
		}

		//set success message
		$msg = ($publish) ? 'COM_NEWSLETTER_PUBLISHED_SUCCESS' : 'COM_NEWSLETTER_UNPUBLISHED_SUCCESS';

		//redirect back to campaigns list
		App::redirect(
			Route::url('index.php?option=com_newsletter&controller=newsletter', false),
			Lang::txt($msg)
		);
	}

	/**
	 * Preview newsletter in lightbox
	 * 
	 * @return  void
	 */
	public function previewTask()
	{
		//get the request vars
		$id = Request::getInt('id', 0);
		$no_html = Request::getInt('no_html', 0);

		//get the newsletter
		$newsletterNewsletter = new Letter($this->database);
		$newsletterNewsletter->load($id);

		// output title 
		if ($no_html == 0)
		{
			$content  = '<h2 class="modal-title">' . Lang::txt('COM_NEWSLETTER_PREVIEW') . '</h2><br /><br />';
			$content .= '<iframe width="100%" height="100%" src="' . Route::url('index.php?option=com_newsletter&task=preview&id=' . $id . '&no_html=1') . '"></iframe>';
		}
		else
		{
			$content = $newsletterNewsletter->buildNewsletter($newsletterNewsletter);
		}

		//build newsletter for displaying preview
		echo $content;
	}

	/**
	 * Display send test newsletter form
	 *
	 * @return  void
	 */
	public function sendTestTask()
	{
		//set layout
		$this->view->setLayout('test');

		//get the request vars
		$ids = Request::getVar("id", array());
		$id = (isset($ids[0])) ? $ids[0] : null;

		//make sure we have an id
		if (!$id)
		{
			$this->setError(Lang::txt('COM_NEWSLETTER_SELECT_NEWSLETTER_FOR_MAILING'));
			$this->displayTask();
			return;
		}

		//get the newsletter
		$newsletterNewsletter = new Letter($this->database);
		$this->view->newsletter = $newsletterNewsletter->getNewsletters($id);

		//check if we have any errors
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Send test newsletter
	 *
	 * @return  void
	 */
	public function doSendTestTask()
	{
		//vars needed for test sending
		$goodEmails = array();
		$badEmails = array();

		//get request vars
		$emails       = Request::getVar('emails', '');
		$newsletterId = Request::getInt('nid', 0);

		//parse emails
		$emails = array_filter(array_map("strtolower", array_map("trim", explode(",", $emails))));

		//make sure we have valid email addresses
		foreach ($emails as $k => $email)
		{
			if (filter_var($email, FILTER_VALIDATE_EMAIL))
			{
				if (count($goodEmails) <= 4)
				{
					$goodEmails[] = $email;
				}
				else
				{
					$badEmails[] = $email;
				}
			}
			else
			{
				$badEmails[] = $email;
			}
		}

		//instantiate newsletter campaign object & load campaign
		$newsletterNewsletter = new Letter($this->database);
		$newsletterNewsletter->load($newsletterId);

		//build newsletter for sending
		$newsletterNewsletterHtmlContent  = $newsletterNewsletter->buildNewsletter($newsletterNewsletter);
		$newsletterNewsletterPlainContent = $newsletterNewsletter->buildNewsletterPlainTextPart($newsletterNewsletter);

		//send campaign
		$this->_send($newsletterNewsletter, $newsletterNewsletterHtmlContent, $newsletterNewsletterPlainContent, $goodEmails, $newsletterMailinglist = null, $sendingTest = true);

		//do we have good emails to tell user about
		if (count($goodEmails))
		{
			$message = Lang::txt('COM_NEWSLETTER_TEST_SEND_SUCCESS', $newsletterNewsletter->name, implode("<br />", $goodEmails));
			$type    = 'success';
		}

		//do we have any bad emails to tell user about
		if (count($badEmails))
		{
			$message = Lang::txt('COM_NEWSLETTER_TEST_SEND_FAIL', $newsletterNewsletter->name, implode("<br />", $badEmails));
			$type    = 'error';
		}

		//redirect after sent
		App::redirect(
			Route::url('index.php?option=com_newsletter&controller=newsletter', false),
			$message,
			$type
		);
	}

	/**
	 * Display send newsletter form
	 *
	 * @return  void
	 */
	public function sendNewsletterTask()
	{
		// get the request vars
		$ids = Request::getVar("id", array());
		$id = (isset($ids[0])) ? $ids[0] : null;

		// make sure we have an id
		if (!$id)
		{
			$this->setError(Lang::txt('COM_NEWSLETTER_SELECT_NEWSLETTER_FOR_MAILING'));
			$this->displayTask();
			return;
		}

		// get the newsletter
		$newsletterNewsletter = new Letter($this->database);
		$this->view->newsletter = $newsletterNewsletter->getNewsletters($id);

		// get newsletter mailing lists
		$newsletterMailinglist = new Mailinglist($this->database);
		$this->view->mailinglists = $newsletterMailinglist->getLists();

		// get the mailings
		$newsletterMailing = new Mailing($this->database);
		$this->view->mailings = $newsletterMailing->getMailings(null, $id);

		// get # left to send
		foreach ($this->view->mailings as $k => $mailing)
		{
			$this->database->setQuery("SELECT COUNT(*) FROM `#__newsletter_mailing_recipients` WHERE mid=" . $this->database->quote($mailing->id) . " AND status='queued'");
			$mailing->queueCount = $this->database->loadResult();
		}

		// check if we have any errors
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		// vars for view
		$this->view->database = $this->database;

		// Output the HTML
		$this->view->setLayout('send')->display();
	}

	/**
	 * Send Newsletter
	 *
	 * @return  void
	 */
	public function doSendNewsletterTask()
	{
		//get request vars
		$newsletterId  = Request::getInt('nid', 0);
		$mailinglistId = Request::getInt('mailinglist', '-1');

		//instantiate newsletter campaign object & load campaign
		$newsletterNewsletter = new Letter($this->database);
		$newsletterNewsletter->load($newsletterId);

		//check to make sure we have an object
		if (!is_object($newsletterNewsletter) || $newsletterNewsletter->name == '')
		{
			$this->setError(Lang::txt('COM_NEWSLETTER_UNABLE_TO_LOCATE'));
			$this->displayTask();
			return;
		}

		// make sure it wasnt deleted
		if ($newsletterNewsletter->deleted == 1)
		{
			$this->setError(Lang::txt('COM_NEWSLETTER_NEWSLETTER_SEND_DELETED'));
			$this->displayTask();
			return;
		}

		// get emails based on mailing list
		$newsletterMailinglist = new Mailinglist($this->database);

		// build newsletter for sending
		$newsletterNewsletterHtmlContent  = $newsletterNewsletter->buildNewsletter($newsletterNewsletter);
		$newsletterNewsletterPlainContent = $newsletterNewsletter->buildNewsletterPlainTextPart($newsletterNewsletter);

		// send campaign
		// purposefully send no emails, will create later
		$newsletterMailing = $this->_send($newsletterNewsletter, $newsletterNewsletterHtmlContent, $newsletterNewsletterPlainContent, array(), $mailinglistId, $sendingTest = false);

		// array of filters
		$filters = array(
			'lid'    => $mailinglistId,
			'status' => 'active',
			'limit'  => 10000,
			'start'  => 0,
			'select' => 'email'
		);

		// get count of emails
		$count = $newsletterMailinglist->getListEmailsCount($filters);
		$left  = $count;

		// make sure we have emails
		if ($count < 1)
		{
			$this->setError(Lang::txt('COM_NEWSLETTER_NEWSLETTER_SEND_MISSING_RECIPIENTS'));
			$this->displayTask();
			return;
		}

		// add recipients at 10000 at a time
		while ($left >= 0)
		{
			// get emails
			$emails = $newsletterMailinglist->getListEmails($mailinglistId, 'email', $filters);

			// add recipeients
			$this->_sendTo($newsletterMailing, $emails);

			// nullify vars
			$emails = null;
			unset($emails);

			//adjust our start
			$filters['start'] += $filters['limit'];

			// remove from what we have left to get
			$left -= $filters['limit'];
		}

		//mark campaign as sent
		$newsletterNewsletter->sent = 1;
		if ($newsletterNewsletter->save($newsletterNewsletter))
		{
			//redirect after sent
			App::redirect(
				Route::url('index.php?option=com_newsletter&controller=newsletter', false),
				Lang::txt('COM_NEWSLETTER_NEWSLETTER_SEND_TO', $newsletterNewsletter->name, number_format($count))
			);
		}
	}

	/**
	 * Send Newsletter
	 *
	 * @param   $newsletter
	 * @param   $newsletterHtmlContent
	 * @param   $newsletterPlainContent
	 * @param   $newsletterContacts
	 * @param   $newsletterMailinglist
	 * @param   $sendingTest
	 * @return  object
	 */
	private function _send($newsletter, $newsletterHtmlContent, $newsletterPlainContent, $newsletterContacts, $newsletterMailinglist, $sendingTest = false)
	{
		//set default mail from and reply-to names and addresses
		$defaultMailFromName       = Config::get("sitename") . ' Newsletter';
		$defaultMailFromAddress    = 'contact@' . $_SERVER['HTTP_HOST'];
		$defaultMailReplytoName    = Config::get("sitename") . ' Newsletter - Do Not Reply';
		$defaultMailReplytoAddress = 'do-not-reply@' . $_SERVER['HTTP_HOST'];

		//get the config mail from and reply-to names and addresses
		$mailFromName       = $this->config->get('newsletter_from_name', $defaultMailFromName);
		$mailFromAddress    = $this->config->get('newsletter_from_address', $defaultMailFromAddress);
		$mailReplytoName    = $this->config->get('newsletter_replyto_name', $defaultMailReplytoName);
		$mailReplytoAddress = $this->config->get('newsletter_replyto_address', $defaultMailReplytoAddress);

		//parse newsletter specific emails
		$params = new Registry($newsletter->params);
		$mailFromName       = $params->get('from_name', $mailFromName);
		$mailFromAddress    = $params->get('from_address', $mailFromAddress);
		$mailReplytoName    = $params->get('replyto_name', $mailReplytoName);
		$mailReplytoAddress = $params->get('replyto_address', $mailReplytoAddress);

		//set final mail from and reply-to
		$mailFrom    = '"' . $mailFromName . '" <' . $mailFromAddress . '>';
		$mailReplyTo = '"' . $mailReplytoName . '" <' . $mailReplytoAddress . '>';

		//set subject and body
		$mailSubject   = ($newsletter->name) ? $newsletter->name : 'Your ' . Config::get("sitename") . '.org Newsletter';
		$mailHtmlBody  = $newsletterHtmlContent;
		$mailPlainBody = $newsletterPlainContent;

		//set mail headers
		//$mailHeaders  = "MIME-Version: 1.0" . "\r\n";
		//$mailHeaders .= "Content-type: text/html; charset=\"UTF-8\"" . "\r\n";
		$mailHeaders  = "From: {$mailFrom}" . "\r\n";
		$mailHeaders .= "Reply-To: {$mailReplyTo}" . "\r\n";

		//set mail priority
		$mailHeaders .= "X-Priority: 3" . "\r\n";
		//$mailHeaders .= "X-MSMail-Priority: Normal" . "\r\n";
		//$mailHeaders .= "Importance: Normal\n";

		//set extra headers
		$mailHeaders .= "X-Mailer: PHP/" . phpversion()  . "\r\n";
		$mailHeaders .= "X-Component: " . $this->_option . "\r\n";
		$mailHeaders .= "X-Component-Object: Campaign Mailing" . "\r\n";
		$mailHeaders .= "X-Component-ObjectId: {{CAMPAIGN_MAILING_ID}}" . "\r\n";
		//$mailHeaders .= "List-Unsubscribe: <mailto:{{UNSUBSCRIBE_MAILTO_LINK}}>, <{{UNSUBSCRIBE_LINK}}>";

		//set mail args
		$mailArgs = '';
		//$mailArgs = '-f hubmail-bounces@' . $_SERVER['HTTP_HOST'];

		//are we sending test mailing
		if ($sendingTest)
		{
			foreach ($newsletterContacts as $contact)
			{
				// create new message
				$message = new \Hubzero\Mail\Message();

				foreach (explode("\r\n", $mailHeaders) as $header)
				{
					$parts = array_map("trim", explode(':', $header));
					switch ($parts[0])
					{
						case 'From':
							if (preg_match("/\\\"([^\"]*)\\\"\\s<([^>]*)>/ux", $parts[1], $matches))
							{
								$message->setFrom(array($matches[2] => $matches[1]));
							}
							break;
						case 'Reply-To':
							if (preg_match("/\\\"([^\"]*)\\\"\\s<([^>]*)>/ux", $parts[1], $matches))
							{
								$message->setReplyTo(array($matches[2] => $matches[1]));
							}
							break;
						case 'Importance':
						case 'X-Priority':
						case 'X-MSMail-Priority':
							$priority = (isset($parts[1]) && in_array($parts[1], array(1,2,3,4,5))) ? $parts[1] : 3;
							$message->setPriority($priority);
							break;
						default:
							if (isset($parts[1]))
							{
								$message->addHeader($parts[0], $parts[1]);
							}
					}
				}

				// build message object and send
				$message->setSubject('[SENDING TEST] - '.$mailSubject)
						->setTo($contact)
						->addPart($mailHtmlBody, 'text/html')
						->addPart($mailPlainBody, 'text/plain')
						->send();
			}

			return true;
		}

		//get the scheduling
		$scheduler = Request::getInt('scheduler', 1);

		if ($scheduler == '1')
		{
			$scheduledDate = Date::toSql();
		}
		else
		{
			$schedulerDate = Request::getVar('scheduler_date', '');
			$schedulerHour = Request::getVar('scheduler_date_hour', '00');
			$schedulerMinute = Request::getVar('scheduler_date_minute', '00');
			$schedulerMeridian = Request::getVar('scheduler_date_meridian', 'AM');

			//make sure we have at least the date or we use now
			if (!$schedulerDate)
			{
				$scheduledDate = Date::toSql();
			}

			//break apart parts of date
			$schedulerDateParts = explode('/', $schedulerDate);

			//make sure its in 24 time
			if ($schedulerMeridian == 'pm')
			{
				$schedulerHour += 12;
			}

			//build scheduled time
			$scheduledTime  = $schedulerDateParts[2] . '-' . $schedulerDateParts[0] . '-' . $schedulerDateParts[1];
			$scheduledTime .= ' ' . $schedulerHour . ':' . $schedulerMinute . ':00';
			$scheduledDate = Date::of(strtotime($scheduledTime))->toSql();
		}

		//create mailing object
		$mailing = new stdClass;
		$mailing->nid        = $newsletter->id;
		$mailing->lid        = $newsletterMailinglist;
		$mailing->subject    = $mailSubject;
		$mailing->html_body  = $mailHtmlBody;
		$mailing->plain_body = $mailPlainBody;
		$mailing->headers    = $mailHeaders;
		$mailing->args       = $mailArgs;
		$mailing->tracking   = $newsletter->tracking;
		$mailing->date       = $scheduledDate;

		//save mailing object
		$newsletterMailing = new Mailing($this->database);
		if (!$newsletterMailing->save($mailing))
		{
			$this->setError(Lang::txt('COM_NEWSLETTER_NEWSLETTER_SEND_FAIL'));
			$this->sendNewsletterTask();
			return;
		}

		// create recipients
		$this->_sendTo($newsletterMailing, $newsletterContacts);

		return $newsletterMailing;
	}

	/**
	 * Create newsletter mailing recipients
	 *
	 * @param   object  $mailing
	 * @param   array   $emails
	 * @return  void
	 */
	private function _sendTo($mailing, &$emails)
	{
		// array to hold values
		$values = array();

		// create date object once
		$date = Date::toSql();

		// create new record for each email
		foreach ($emails as $email)
		{
			$values[] = "(" . $this->database->quote($mailing->id) . "," . $this->database->quote($email) . ",'queued', " . $this->database->quote($date) . ")";
		}

		// make sure we have some values
		if (count($values) > 0)
		{
			// build full query & execute
			$sql = "INSERT INTO `#__newsletter_mailing_recipients` (`mid`,`email`,`status`,`date_added`) VALUES " . implode(',', $values);
			$this->database->setQuery($sql);
			$this->database->query();
		}

		// garbage collection
		$values = null;
		$sql    = null;
		unset($values);
		unset($sql);
	}

	/**
	 * Get Unique newsletter alias
	 *
	 * @param   string   $alias  Newsletter Alias
	 * @param   integer  $id     Newsletter Id
	 * @return  string
	 */
	private function _getUniqueNewsletterAlias($alias, $id)
	{
		$sql = "SELECT `alias` FROM `#__newsletters` WHERE `id` NOT IN (".$this->database->quote($id).")";
		$this->database->setQuery($sql);
		$aliases = $this->database->loadColumn();

		//if we have another newsletter with this alias lets add random #
		if (in_array($alias, $aliases))
		{
			$alias .= rand(0, 100);
		}

		return $alias;
	}
}
