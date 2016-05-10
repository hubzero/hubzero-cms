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

// No direct access
defined('_HZEXEC_') or die();

/**
 * Cron plugin for newsletters
 */
class plgCronNewsletter extends \Hubzero\Plugin\Plugin
{
	/**
	 * Return a list of events
	 *
	 * @return  array
	 */
	public function onCronEvents()
	{
		$this->loadLanguage();

		$obj = new stdClass();
		$obj->plugin = $this->_name;
		$obj->events = array(
			array(
				'name'   => 'processMailings',
				'label'  => Lang::txt('PLG_CRON_NEWSLETTER_PROCESS_QUEUE'),
				'params' => 'processmailings'
			),
			array(
				'name'   => 'processIps',
				'label'  => Lang::txt('PLG_CRON_NEWSLETTER_IP_ADDRESSES_TO_LOCATION'),
				'params' => ''
			)
		);

		return $obj;
	}

	/**
	 * Processes any queued newsletter mailings.
	 *
	 * @param   object   $job  \Components\Cron\Models\Job
	 * @return  boolean
	 */
	public function processMailings(\Components\Cron\Models\Job $job)
	{
		// load needed libraries
		require_once PATH_CORE . DS . 'components' . DS . 'com_newsletter' . DS . 'tables' . DS . 'mailing.recipient.php';
		require_once PATH_CORE . DS . 'components' . DS . 'com_newsletter' . DS . 'tables' . DS . 'mailing.php';
		require_once PATH_CORE . DS . 'components' . DS . 'com_newsletter' . DS . 'tables' . DS . 'mailinglist.php';
		require_once PATH_CORE . DS . 'components' . DS . 'com_newsletter' . DS . 'helpers' . DS . 'helper.php';

		// needed vars
		$limit     = 25;
		$processed = array();

		// do we have a param defined limit
		$params = $job->params;
		if (is_object($params) && $params->get('newsletter_queue_limit'))
		{
			$paramDefinedLimit = $params->get('newsletter_queue_limit');
			if (is_numeric($paramDefinedLimit) && $paramDefinedLimit > 0 && $paramDefinedLimit < 100)
			{
				$limit = $paramDefinedLimit;
			}
		}

		// create needed objects
		$database = App::get('db');

		// get all queued mailing recipients
		$sql = "SELECT nmr.id AS mailing_recipientid, nm.id AS mailingid, nm.nid AS newsletterid, nm.lid AS mailinglistid, nmr.email, nm.subject, nm.html_body, nm.plain_body, nm.headers, nm.args, nm.tracking, nl.autogen, nm.date AS sendDate
				FROM `#__newsletter_mailings` AS nm, `#__newsletter_mailing_recipients` AS nmr, `#__newsletters` AS nl
				WHERE nm.id=nmr.mid
				AND nm.nid=nl.id
				AND nmr.status='queued'
				AND nm.deleted=0
				AND UTC_TIMESTAMP() >= nm.date
				ORDER BY nmr.date_added
				LIMIT {$limit}";
		$database->setQuery($sql);
		$queuedEmails = $database->loadObjectList();

		// Get newsletter, check whether it is autogen

		// loop through each newsletter recipient, prepare and mail
		foreach ($queuedEmails as $queuedEmail)
		{
			if (in_array($queuedEmail->email, $processed))
			{
				continue;
			}

			// get tracking & unsubscribe token
			$emailToken = \Components\Newsletter\Helpers\Helper::generateMailingToken($queuedEmail);

			// if tracking is on add it to email
			if ($queuedEmail->tracking)
			{
				$queuedEmail->html_body = \Components\Newsletter\Helpers\Helper::addTrackingToEmailMessage($queuedEmail->html_body, $emailToken);
			}

			// create unsubscribe link
			$unsubscribeMailtoLink = '';
			$unsubscribeLink       = 'https://' . $_SERVER['SERVER_NAME'] . '/newsletter/unsubscribe?e=' . urlencode($queuedEmail->email) . '&t=' . $emailToken;

			// add unsubscribe link - placeholder & in header (must do after adding tracking!!)
			$queuedEmail->html_body = str_replace("{{UNSUBSCRIBE_LINK}}", $unsubscribeLink, $queuedEmail->html_body);
			$queuedEmail->headers   = str_replace("{{UNSUBSCRIBE_LINK}}", $unsubscribeLink, $queuedEmail->headers);
			$queuedEmail->headers   = str_replace("{{UNSUBSCRIBE_MAILTO_LINK}}", $unsubscribeMailtoLink, $queuedEmail->headers);

			// add mailing id to header
			$queuedEmail->headers  = str_replace("{{CAMPAIGN_MAILING_ID}}", $queuedEmail->mailingid, $queuedEmail->headers);

			// create new message
			$message = new \Hubzero\Mail\Message();

			// add headers
			foreach (explode("\r\n", $queuedEmail->headers) as $header)
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
			$message->setSubject($queuedEmail->subject)
					->setTo($queuedEmail->email)
					->setBody($queuedEmail->plain_body, 'text/plain')
					->addPart($queuedEmail->html_body, 'text/html');

			// mail message
			if ($message->send())
			{
				// add to process email array
				$processed[] = $queuedEmail->email;

				// Some trickery for supporting rescheduled emails
				if ($queuedEmail->autogen == 0 || $queuedEmail->autogen == null)
				{
					// load recipient object
					$newsletterMailingRecipient = new \Components\Newsletter\Tables\MailingRecipient($database);
					$newsletterMailingRecipient->load($queuedEmail->mailing_recipientid);

					// mark as sent and save
					$newsletterMailingRecipient->status    = 'sent';
					$newsletterMailingRecipient->date_sent = Date::toSql();
					$newsletterMailingRecipient->save($newsletterMailingRecipient);
				}
				else
				{
					$sql = "SELECT *, max(date) AS maxDate FROM #__newsletter_mailings WHERE nid = {$queuedEmail->newsletterid} AND deleted=0;";
					$database->setQuery($sql);
					$latestMailing = $database->loadObject();

					switch ($queuedEmail->autogen)
					{
						case 1:
							$lookahead = ' +1 day';
						break;
						case 2:
							$lookahead = ' +1 week';
						break;
						case 3:
							$lookahead = ' +1 month';
						break;
					}

					$nextDate = Date::of(strtotime($latestMailing->maxDate. $lookahead))->toLocal();

					$windowMin = strtotime(Date::of(strtotime($nextDate))->toLocal("Y-m-d"));
					$windowMax = strtotime(Date::of(strtotime($lookahead))->toLocal("Y-m-d"));

					// If there is no mailing set for the next interval, create it.
					if ($windowMax - $windowMin == 0)
					{
						// Create mailing
						$newMailing = new Components\Newsletter\Tables\Mailing($database);
						$newMailing->bind($latestMailing);
						$newMailing->id = null;
						$newMailing->date = $nextDate;
						$newMailing->save($newMailing);

						// Add recipients
						$mailingList = new Components\Newsletter\Tables\MailingList($database);
						$emails = $mailingList->getListEmails($newMailing->lid);

						// @TODO Verify there is no helper method to determine whether or not to send email
						foreach ($emails as $email)
						{
							if ($email->status == 'active')
							{
								$values[] = "(" . $database->quote($newMailing->id) . "," . $database->quote($email->email) . ",'queued', " . $database->quote(Date::of(time())->toLocal()) . ")";
							}
						}

						// make sure we have some values
						if (count($values) > 0)
						{
							// build full query & execute
							$sql = "INSERT INTO `#__newsletter_mailing_recipients` (`mid`,`email`,`status`,`date_added`) VALUES " . implode(',', $values);
							$database->setQuery($sql);
							$database->query();
						}
					} // End interval creation

					// load recipient object
					$newsletterMailingRecipient = new \Components\Newsletter\Tables\MailingRecipient($database);
					$newsletterMailingRecipient->load($queuedEmail->mailing_recipientid);

					// mark as sent and save
					$newsletterMailingRecipient->status    = 'sent';
					$newsletterMailingRecipient->date_sent = Date::toSql();
					$newsletterMailingRecipient->save($newsletterMailingRecipient);
				} // end autogen logic
			}
		}

		return true;
	}

	/**
	 * Processes newsletter mailing actions (clicks, opens, etc) IP addresses into location data for stats
	 *
	 * @param   object   $job  \Components\Cron\Models\Job
	 * @return  boolean
	 */
	public function processIps(\Components\Cron\Models\Job $job)
	{
		// load needed libraries
		require_once PATH_CORE . DS . 'components' . DS . 'com_newsletter' . DS . 'tables' . DS . 'mailing.recipient.action.php';

		// get db
		$database = App::get('db');

		// get actions
		$newsletterMailingRecipientAction = new \Components\Newsletter\Tables\MailingRecipientAction($database);
		$unconvertedActions = $newsletterMailingRecipientAction->getUnconvertedActions();

		// convert all unconverted actions
		foreach ($unconvertedActions as $action)
		{
			// attempt to locate
			try
			{
				$location = Hubzero\Geocode\Geocode::locate($action->ip);
			}
			catch (Exception $e)
			{
				continue;
			}

			// if we got a valid result lets update our action with location info
			if (is_object($location) && $location['latitude'] != '' && $location['longitude'] != '-')
			{
				$sql = "UPDATE `#__newsletter_mailing_recipient_actions`
						SET
							countrySHORT=" . $database->quote($location['countryCode']) . ",
							countryLONG=" . $database->quote($location['country']) . ",
							ipREGION=" . $database->quote($location['region']) . ",
							ipCITY=" . $database->quote($location['city']) . ",
							ipLATITUDE=" . $database->quote($location['latitude']) . ",
							ipLONGITUDE=" . $database->quote($location['longitude']) . "
						WHERE id=" . $database->quote($action->id);
				$database->setQuery($sql);
				$database->query();
			}
		}
		return true;
	}
}

