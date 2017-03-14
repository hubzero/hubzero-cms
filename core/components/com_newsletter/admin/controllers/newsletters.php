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

use Components\Newsletter\Models\Newsletter;
use Components\Newsletter\Models\Template;
use Components\Newsletter\Models\MailingList;
use Components\Newsletter\Models\Mailing;
use Components\Newsletter\Models\Primary;
use Components\Newsletter\Models\Secondary;
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
class Newsletters extends AdminController
{
	/**
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->registerTask('add', 'edit');
		$this->registerTask('apply', 'save');
		$this->registerTask('publish', 'state');
		$this->registerTask('unpublish', 'state');

		parent::execute();
	}

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

		// Filters
		$filters = array(
			'search' => Request::getState(
				$this->_option . '.' . $this->_controller . '.search',
				'search',
				''
			),
			'published' => Request::getstate(
				$this->_option . '.' . $this->_controller . '.published',
				'published',
				-1,
				'int'
			),
			// Sorting
			'sort' => Request::getstate(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'created'
			),
			'sort_Dir' => Request::getstate(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'DESC'
			)
		);

		$records = Newsletter::all()
			->including(['template', function ($template){
				$template->select('*');
			}]);

		if ($filters['search'])
		{
			$filters['search'] = strtolower((string)$filters['search']);

			$records->whereLike('name', $filters['search']);
		}

		if ($filters['published'] >= 0)
		{
			$records->whereEquals('published', $filters['published']);
		}

		$rows = $records
			->order($filters['sort'], $filters['sort_Dir'])
			->paginated('limitstart', 'limit')
			->rows();

		// Output the HTML
		$this->view
			->setLayout('display')
			->set('rows', $rows)
			->set('filters', $filters)
			->display();
	}

	/**
	 * Edit newsletter task
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

			$row = Newsletter::oneOrNew($id);
		}

		if ($row->isNew())
		{
			// This is used to determine frequency
			// 0 - regular/disabled
			// 1 - daily
			// 2 - weekly
			// 3 - monthly
			$row->set('autogen', 0);
		}

		$templates = Template::all()
			->ordered()
			->rows();

		// Output the HTML
		$this->view
			->set('newsletter', $row)
			->set('templates', $templates)
			->set('config', $this->config)
			->setLayout('edit')
			->display();
	}

	/**
	 * Save campaign task
	 *
	 * @return 	void
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
		$row = Newsletter::oneOrNew($fields['id'])->set($fields);

		// did we have params
		$p = Request::getVar('params', array(), 'post');

		if (!empty($p))
		{
			// load previous params
			$params = new Registry($row->get('params'));

			// set from name
			if (isset($p['from_name']))
			{
				$params->set('from_name', $p['from_name']);
			}

			// set from address
			if (isset($p['from_address']))
			{
				$params->set('from_address', $p['from_address']);
			}

			// set reply-to name
			if (isset($p['replyto_name']))
			{
				$params->set('replyto_name', $p['replyto_name']);
			}

			// set reply-to address
			if (isset($p['replyto_address']))
			{
				$params->set('replyto_address', $p['replyto_address']);
			}

			//newsletter params to string
			$row->set('params', $params->toString());
		}

		// Save campaign
		if (!$row->save())
		{
			Notify::error($row->getError());
			return $this->editTask($row);
		}

		// Set success message
		Notify::success(Lang::txt('COM_NEWSLETTER_SAVED_SUCCESS'));

		if ($this->getTask() == 'apply')
		{
			// If we just created campaign go back to edit form so we can add content
			return $this->editTask($row);
		}

		// Redirect back to campaigns list
		$this->cancelTask();
	}

	/**
	 * Duplicate newsletters
	 *
	 * @return  void
	 */
	public function duplicateTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		if (!User::authorise('core.create', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Get the request vars
		$ids = Request::getVar('id', array());

		// Make sure we have ids
		$success = 0;

		if (isset($ids) && count($ids) > 0)
		{
			// delete each newsletter
			foreach ($ids as $id)
			{
				// Instantiate newsletter object
				$newsletter = Newsletter::oneOrFail($id);

				if (!$newsletter->duplicate())
				{
					Notify::error($newsletter->getError());
					continue;
				}

				$success++;
			}
		}

		if ($success)
		{
			Notify::success(Lang::txt('COM_NEWSLETTER_DUPLICATED_SUCCESS'));
		}

		// Redirect back to campaigns list
		$this->cancelTask();
	}

	/**
	 * Delete Task
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

		// Get the request vars
		$ids = Request::getVar('id', array());

		// Make sure we have ids
		$success = 0;

		if (isset($ids) && count($ids) > 0)
		{
			foreach ($ids as $id)
			{
				// Instantiate newsletter object
				$newsletter = Newsletter::oneOrFail($id);

				// Mark as deleted
				$newsletter->set('deleted', 1);

				// Save changes
				if (!$newsletter->save())
				{
					Notify::error(Lang::txt('COM_NEWSLETTER_DELETE_FAIL'));
					continue;
				}

				$success++;
			}
		}

		if ($success)
		{
			Notify::success(Lang::txt('COM_NEWSLETTER_DELETE_SUCCESS'));
		}

		// Redirect back to campaigns list
		$this->cancelTask();
	}

	/**
	 * Toggle published state for newsletter
	 *
	 * @return  void
	 */
	public function stateTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		if (!User::authorise('core.edit.state', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		$publish = $this->getTask() == 'publish' ? 1 : 0;

		// Get the request vars
		$ids = Request::getVar('id', array());

		// Make sure we have ids
		$success = 0;

		if (isset($ids) && count($ids) > 0)
		{
			foreach ($ids as $id)
			{
				// Instantiate newsletter object
				$newsletter = Newsletter::oneOrFail($id);

				// Set state
				$newsletter->set('published', $publish);

				// Save changes
				if (!$newsletter->save())
				{
					Notify::error(Lang::txt('COM_NEWSLETTER_STATE_CHANGE_FAIL'));
					continue;
				}

				$success++;
			}
		}

		if ($success)
		{
			// Set success message
			$msg = ($publish) ? 'COM_NEWSLETTER_PUBLISHED_SUCCESS' : 'COM_NEWSLETTER_UNPUBLISHED_SUCCESS';

			Notify::success($msg);
		}

		// Redirect back to campaigns list
		$this->cancelTask();
	}

	/**
	 * Preview newsletter in lightbox
	 * 
	 * @return  void
	 */
	public function previewTask()
	{
		// Get the request vars
		$id = Request::getInt('id', 0);
		$no_html = Request::getInt('no_html', 0);

		// Get the newsletter
		$newsletter = Newsletter::oneOrFail($id);

		// Generate output
		if (!$no_html)
		{
			$content  = '<h2 class="modal-title">' . Lang::txt('COM_NEWSLETTER_PREVIEW') . '</h2><br /><br />';
			$content .= '<iframe width="100%" height="100%" src="' . Route::url('index.php?option=' . $this->_option. '&task=preview&id=' . $id . '&no_html=1') . '"></iframe>';
		}
		else
		{
			$content = $newsletter->buildNewsletter($newsletter);
		}

		echo $content;
	}

	/**
	 * Display send test newsletter form
	 *
	 * @return  void
	 */
	public function sendTestTask()
	{
		// Get the request vars
		$ids = Request::getVar('id', array());
		$id = (isset($ids[0])) ? $ids[0] : 0;

		// Make sure we have an id
		if (!$id)
		{
			Notify::warning(Lang::txt('COM_NEWSLETTER_SELECT_NEWSLETTER_FOR_MAILING'));

			return $this->cancelTask();
		}

		// Get the newsletter
		$newsletter = Newsletter::oneOrFail($id);

		// Output the HTML
		$this->view
			->setLayout('test')
			->set('newsletter', $newsletter)
			->display();
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
		$badEmails  = array();

		// Get request vars
		$emails       = Request::getVar('emails', '');
		$newsletterId = Request::getInt('nid', 0);

		// Parse emails
		$emails = array_filter(array_map("strtolower", array_map("trim", explode(",", $emails))));

		// Make sure we have valid email addresses
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

		// Instantiate newsletter campaign object & load campaign
		$newsletter = Newsletter::oneOrFail($newsletterId);

		// Build newsletter for sending
		$htmlContent  = $newsletter->buildNewsletter($newsletter);
		$plainContent = $newsletter->buildNewsletterPlainTextPart($newsletter);

		// Send campaign
		$this->_send(
			$newsletter,
			$htmlContent,
			$plainContent,
			$goodEmails,
			$mailinglist = null,
			$sendingTest = true
		);

		// Do we have good emails to tell user about
		if (count($goodEmails))
		{
			Notify::success(Lang::txt('COM_NEWSLETTER_TEST_SEND_SUCCESS', $newsletter->name, implode('<br />', $goodEmails)));
		}

		// Do we have any bad emails to tell user about
		if (count($badEmails))
		{
			Notify::error(Lang::txt('COM_NEWSLETTER_TEST_SEND_FAIL', $newsletter->name, implode('<br />', $badEmails)));
		}

		$this->cancelTask();
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
			Notify::error(Lang::txt('COM_NEWSLETTER_SELECT_NEWSLETTER_FOR_MAILING'));
			return $this->cancelTask();
		}

		// get the newsletter
		$newsletter = Newsletter::oneOrFail($id);

		// get newsletter mailing lists
		$mailinglists = Mailinglist::all()
			->whereEquals('deleted', 0)
			->ordered()
			->rows();

		// get the mailings
		$mailings = Mailing::all()
			->including(['recipients', function ($recipient){
				$recipient
					->select('id', null, true)
					->whereEquals('status', 'queued');
			}])
			->whereEquals('nid', $id)
			->rows();

		// Output the HTML
		$this->view
			->set('newsletter', $newsletter)
			->set('mailinglists', $mailinglists)
			->set('mailings', $mailings)
			->setLayout('send')
			->display();
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
		$newsletter = Newsletter::oneOrFail($newsletterId);

		//check to make sure we have an object
		if ($newsletter->name == '')
		{
			Notify::error(Lang::txt('COM_NEWSLETTER_UNABLE_TO_LOCATE'));
			return $this->cancelTask();
		}

		// make sure it wasnt deleted
		if ($newsletter->deleted == 1)
		{
			Notify::error(Lang::txt('COM_NEWSLETTER_NEWSLETTER_SEND_DELETED'));
			return $this->cancelTask();
		}

		// get emails based on mailing list
		$mailinglist = Mailinglist::oneOrFail($mailinglistId);

		// build newsletter for sending
		$htmlContent  = $newsletter->buildNewsletter($newsletter);
		$plainContent = $newsletter->buildNewsletterPlainTextPart($newsletter);

		// send campaign
		// purposefully send no emails, will create later
		$mailing = $this->_send(
			$newsletter,
			$htmlContent,
			$plainContent,
			array(),
			$mailinglistId,
			$sendingTest = false
		);

		// array of filters
		$filters = array(
			'lid'    => $mailinglistId,
			'status' => 'active',
			'limit'  => 10000,
			'start'  => 0,
			'select' => 'email'
		);

		// get count of emails
		$count = $mailinglist
			->emails()
			->whereEquals('status', 'active')
			->total();
		$left = $count;

		// make sure we have emails
		if ($count < 1)
		{
			Notify::error(Lang::txt('COM_NEWSLETTER_NEWSLETTER_SEND_MISSING_RECIPIENTS'));
			return $this->cancelTask();
		}

		// add recipients at 10000 at a time
		while ($left >= 0)
		{
			// get emails
			$emails = $mailinglist
				->emails()
				->whereEquals('status', 'active')
				->limit($filters['limit'])
				->start($filters['start'])
				->rows()
				->toArray();

			// add recipeients
			$this->_sendTo($mailing, $emails);

			// nullify vars
			$emails = null;
			unset($emails);

			//adjust our start
			$filters['start'] += $filters['limit'];

			// remove from what we have left to get
			$left -= $filters['limit'];
		}

		// Mark campaign as sent
		$newsletter->set('sent', 1);

		if (!$newsletter->save())
		{
			Notify::error($newsletter->getError());
		}
		else
		{
			Notify::success(Lang::txt('COM_NEWSLETTER_NEWSLETTER_SEND_TO', $newsletter->name, number_format($count)));
		}

		$this->cancelTask();
	}

	/**
	 * Send Newsletter
	 *
	 * @param   object   $newsletter
	 * @param   string   $newsletterHtmlContent
	 * @param   string   $newsletterPlainContent
	 * @param   array    $newsletterContacts
	 * @param   object   $newsletterMailinglist
	 * @param   boolean  $sendingTest
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
		$params = new Registry($newsletter->get('params'));
		$mailFromName       = $params->get('from_name', $mailFromName);
		$mailFromAddress    = $params->get('from_address', $mailFromAddress);
		$mailReplytoName    = $params->get('replyto_name', $mailReplytoName);
		$mailReplytoAddress = $params->get('replyto_address', $mailReplytoAddress);

		//set final mail from and reply-to
		$mailFrom    = '"' . $mailFromName . '" <' . $mailFromAddress . '>';
		$mailReplyTo = '"' . $mailReplytoName . '" <' . $mailReplytoAddress . '>';

		//set subject and body
		$mailSubject   = $newsletter->get('name', 'Your ' . Config::get("sitename") . '.org Newsletter');
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
				// get tracking & unsubscribe token
				$recipient = new stdClass;
				$recipient->email = $contact;
				$recipient->mailingid = ($newsletterMailinglist ? $newsletterMailinglist : -1);

				$emailToken = \Components\Newsletter\Helpers\Helper::generateMailingToken($recipient);

				// create unsubscribe link
				$unsubscribeMailtoLink = '';
				$unsubscribeLink       = 'https://' . $_SERVER['SERVER_NAME'] . '/newsletter/unsubscribe?e=' . urlencode($contact) . '&t=' . $emailToken;

				// add unsubscribe link - placeholder & in header (must do after adding tracking!!)
				$mailHtmlBody  = str_replace("{{UNSUBSCRIBE_LINK}}", $unsubscribeLink, $mailHtmlBody);
				$mailPlainBody = str_replace("{{UNSUBSCRIBE_LINK}}", $unsubscribeLink, $mailPlainBody);

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

		// Get the scheduling
		$scheduler = Request::getInt('scheduler', 1);

		if ($scheduler == '1')
		{
			$scheduledDate = Date::toSql();
		}
		else
		{
			$schedulerDate     = Request::getVar('scheduler_date', '');
			$schedulerHour     = Request::getVar('scheduler_date_hour', '00');
			$schedulerMinute   = Request::getVar('scheduler_date_minute', '00');
			$schedulerMeridian = Request::getVar('scheduler_date_meridian', 'AM');

			// Make sure we have at least the date or we use now
			if (!$schedulerDate)
			{
				$scheduledDate = Date::toSql();
			}

			// Break apart parts of date
			$schedulerDateParts = explode('/', $schedulerDate);

			// Make sure its in 24 time
			if ($schedulerMeridian == 'pm')
			{
				$schedulerHour += 12;
			}

			// Build scheduled time
			$scheduledTime  = $schedulerDateParts[2] . '-' . $schedulerDateParts[0] . '-' . $schedulerDateParts[1];
			$scheduledTime .= ' ' . $schedulerHour . ':' . $schedulerMinute . ':00';
			$scheduledDate = Date::of(strtotime($scheduledTime))->toSql();
		}

		// Create mailing object
		$mailing = Mailing::blank()
			->set(array(
				'nid'        => $newsletter->id,
				'lid'        => $newsletterMailinglist,
				'subject'    => $mailSubject,
				'html_body'  => $mailHtmlBody,
				'plain_body' => $mailPlainBody,
				'headers'    => $mailHeaders,
				'args'       => $mailArgs,
				'tracking'   => $newsletter->tracking,
				'date'       => $scheduledDate
			));

		// Save mailing object
		if (!$newsletterMailing->save())
		{
			Notify::error(Lang::txt('COM_NEWSLETTER_NEWSLETTER_SEND_FAIL'));
			return $this->sendNewsletterTask();
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
	private function _sendTo($mailing, $emails)
	{
		// create date object once
		$date = Date::toSql();

		// create new record for each email
		foreach ($emails as $email)
		{
			$insert = Recipient::blank()
				->set(array(
					'mid'        => $mailing->id,
					'email'      => (is_array($email) ? $email['email'] : $email),
					'status'     => 'queued',
					'date_added' => $date
				));

			if (!$insert->save())
			{
				Notify::error($insert->getError());
			}
		}
	}
}
