<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
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
		require_once Component::path('com_newsletter') . DS . 'models' . DS . 'newsletter.php';
		require_once Component::path('com_newsletter') . DS . 'models' . DS . 'mailinglist.php';
		require_once Component::path('com_newsletter') . DS . 'models' . DS . 'mailing.php';
		require_once Component::path('com_newsletter') . DS . 'helpers' . DS . 'helper.php';

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
						$priority = (isset($parts[1]) && in_array($parts[1], array(1, 2, 3, 4, 5))) ? $parts[1] : 3;
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
					$newsletterMailingRecipient = \Components\Newsletter\Models\Mailing\Recipient::one($queuedEmail->mailing_recipientid);

					// mark as sent and save
					$newsletterMailingRecipient->set('status', 'sent');
					$newsletterMailingRecipient->set('date_sent', Date::toSql());
					$newsletterMailingRecipient->save();
				}
				else
				{
					$sql = "SELECT *, max(date) AS maxDate FROM `#__newsletter_mailings` WHERE nid = {$queuedEmail->newsletterid} AND deleted=0;";
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
						$newMailing = Components\Newsletter\Models\Mailing::blank();
						foreach (get_object_vars($latestMailing) as $k => $v)
						{
							$newMailing->set($k, $v);
						}
						$newMailing->set('id', null);
						$newMailing->set('date', $nextDate);
						$newMailing->save();

						// Add recipients
						$mailingList = Components\Newsletter\Models\MailingList::oneOrNew($newMailing->lid);
						$emails = $mailingList->emails()->rows();

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
					$newsletterMailingRecipient = \Components\Newsletter\Models\Mailing\Recipient::oneOrNew($queuedEmail->mailing_recipientid);

					// mark as sent and save
					$newsletterMailingRecipient->set('status', 'sent');
					$newsletterMailingRecipient->set('date_sent', Date::toSql());
					$newsletterMailingRecipient->save();
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
		require_once Component::path('com_newsletter') . DS . 'models' . DS . 'mailing' . DS . 'recipient' . DS . 'action.php';

		// get db
		$database = App::get('db');

		$params = $job->params;
		$limit = 100;
		if (is_object($params))
		{
			$limit = $params->get('newsletter_ips_limit', 100);
		}

		// get actions
		$unconvertedActions = \Components\Newsletter\Models\Mailing\Recipient\Action::all()
			->whereRaw("(ipLATITUDE = '' OR ipLATITUDE IS NULL OR ipLONGITUDE = '' OR ipLONGITUDE IS NULL)")
			->order('id', 'desc')
			->limit($limit)
			->rows();

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

				/*$location = array(
					'countryCode' => '',
					'country'     => '',
					'region'      => '',
					'city'        => '',
					'latitude'    => 0.0,
					'longitude'   => 0.0
				);*/
			}

			// if we got a valid result lets update our action with location info
			if (!empty($location) && $location['latitude'] != '' && $location['longitude'] != '-')
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
