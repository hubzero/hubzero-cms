<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Mail\Transport;

use Hubzero\Error\Exception\RuntimeException;
use Mandrill as MandrillApi;
use Mandrill_Error;
use Swift_Attachment;
use Swift_Events_EventListener;
use Swift_Mime_Message;
use Swift_Transport;

/**
 * Class Swift_Transport_Mandrill
 */
class Mandrill implements Swift_Transport
{
	/**
	 * Whether or not the Mandrill object has been created
	 *
	 * @var  bool
	 */
	private $started = false;

	/**
	 * The Mandrill API key
	 *
	 * @var  string
	 */
	private $apiKey = '';

	/**
	 * The Mandrill object
	 *
	 * @var  object
	 */
	private $mandrill = null;

	/**
	 * Constructs a new Mandril transport mechanism
	 *
	 * @param   string  $apiKey  The Mandrill api key
	 * @return  void
	 **/
	public function __construct($apiKey)
	{
		$this->apiKey = $apiKey;
	}

	/**
	 * Tests to see if the transporter has been started
	 *
	 * @return  bool
	 */
	public function isStarted()
	{
		return $this->started;
	}

	/**
	 * Starts the transport mechanism
	 *
	 * @return  void
	 */
	public function start()
	{
		$this->mandrill = new MandrillApi($this->apiKey);
	}

	/**
	 * Stop the transport mechanism
	 *
	 * @return  void
	 */
	public function stop()
	{
		$this->mandrill = null;
	}

	/**
	 * Sends the given message
	 *
	 * Recipient/sender data will be retrieved from the Message API.
	 * The return value is the number of recipients who were accepted for delivery.
	 *
	 * @param   object  $message           The message to be sent
	 * @param   array   $failedRecipients  An array of failures
	 * @return  int
	 */
	public function send(Swift_Mime_Message $message, &$failedRecipients=null)
	{
		try
		{
			// Start building the message
			$from = $message->getFrom();
			$to   = [];

			// Process recipients
			foreach ($message->getTo() as $address => $name)
			{
				$to[] = [
					'email' => $address,
					'name'  => $name,
					'type'  => 'to'
				];
			}

			// Check for attachments
			$attachments = [];
			$html        = '';
			$txt         = '';

			foreach ($message->getChildren() as $children)
			{
				if ($children instanceof Swift_Attachment)
				{
					$attachments[] = [
						'type'    => $children->getContentType(),
						'name'    => $children->getFilename(),
						'content' => base64_encode($children->getBody())
					];
				}
				elseif ($message->getBody() == null)
				{
					if ($children->getContentType() == 'text/html')
					{
						$html .= $children->getBody();
					}
					else
					{
						$txt .= $children->getBody();
					}
				}
				else
				{
					if ($message->getContentType() == 'text/html')
					{
						$html .= $message->getBody();
					}
					else
					{
						$txt .= $message->getBody();
					}
				}
			}

			// Build message
			$mail = [
				'html'                => $html,
				'txt'                 => $txt,
				'subject'             => $message->getSubject(),
				'from_email'          => array_keys($from)[0],
				'from_name'           => reset($from),
				'to'                  => $to,
				'headers'             => array('Reply-To' => $message->getReplyTo()),
				'attachments'         => $attachments,
				'tags'                => $message->getTags(),
				'preserve_recipients' => false,
			];

			// @FIXME: could paramertize some of these options
			$async   = false;
			$ip_pool = 'Main Pool';
			$result  = $this->mandrill->messages->send($mail, $async, $ip_pool);

			// Check for issues in sending
			foreach ($result as $recepient)
			{
				if (!in_array($recepient['status'], ['queued', 'sent']))
				{
					\Log::info(\Lang::txt('Mail to %s failed', $recepient['email']));
				}
			}

			return true;
		}
		catch (Mandrill_Error $e)
		{
			throw new RuntimeException('A mandrill error occurred: ' . $e->getMessage(), 500);
		}
	}

	/**
	 * Registers a plugin on the transporter
	 *
	 * @FIXME: not exactly sure how this comes into play (more research needed)
	 *
	 * @param   object  $plugin
	 * @return  void
	 */
	public function registerPlugin(Swift_Events_EventListener $plugin)
	{
		return;
	}
}