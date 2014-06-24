<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Mail;

/**
 * Class for creating and sending email
 */
class Message extends \Swift_Message
{
	/**
	 * Failed email address
	 *
	 * @var array
	 */
	private $_failures = null;

	/**
	 * Check if message needs to be sent as multipart
	 * MIME message or if it has only one part.
	 *
	 * @return bool
	 */
	public function addHeader($headerFieldNameOrLine, $fieldValue = null)
	{
		$this->getHeaders()->addTextHeader($headerFieldNameOrLine, $fieldValue);
		return $this;
	}

	/**
	 * Set the priority of this message.
	 * The value is an integer where 1 is the highest priority and 5 is the lowest.
	 *
	 * Modified version to also accept a string $message->setPriority('high');
	 *
	 * @param      mixed $priority integer|string
	 * @return     object
	 */
	public function setPriority($priority)
	{
		if (is_string($priority))
		{
			switch (strtolower($priority))
			{
				case 'high':   $priority = 1; break;
				case 'normal': $priority = 3; break;
				case 'low':    $priority = 5; break;

				default:       $priority = 3; break;
			}
		}
		return parent::setPriority($priority);
	}

	/**
	 * Send the message
	 *
	 * @return object
	 */
	public function send($transporter='', $options=array())
	{
		$config = \JFactory::getConfig();

		$transporter = $transporter ? $transporter : $config->getValue('config.mailer');

		switch (strtolower($transporter))
		{
			case 'smtp':
				if (!isset($options['host']))
				{
					$options['host'] = $config->getValue('config.smtphost');
				}
				if (!isset($options['port']))
				{
					$options['port'] = $config->getValue('config.smtpport');
				}
				if (!isset($options['username']))
				{
					$options['username'] = $config->getValue('config.smtpuser');
				}
				if (!isset($options['password']))
				{
					$options['password'] = $config->getValue('config.smtppass');
				}

				if (!empty($options))
				{
					$transport = \Swift_SmtpTransport::newInstance($options['host'], $options['port']);
					$transport->setUsername($options['username'])
					          ->setUsername($options['password']);
				}
			break;

			case 'sendmail':
				if (!isset($options['command']))
				{
					$options['command'] = '/usr/sbin/exim -bs';
				}
				$transport = \Swift_SendmailTransport::newInstance($options['command']);
			break;

			case 'mail':
			default:
				$transport = \Swift_MailTransport::newInstance();
				//set mail additional args (mail return path - used for bounces)
				//$transport->setExtraParams('-f hubmail-bounces@' . $_SERVER['HTTP_HOST']);
			break;
		}

		if (!($transport instanceof \Swift_Transport))
		{
			throw new \InvalidArgumentException('Invalid transport specified');
		}

		$mailer = \Swift_Mailer::newInstance($transport);
		$result = $mailer->send($this, $this->_failures);

		$log = \JFactory::getLogger();
		$log->info(\JText::sprintf('Mail sent to %s', json_encode($this->getTo())));

		return $result;
	}

	/**
	 * Get the list of failed email addresses
	 *
	 * @return array|null
	 */
	public function getFailures()
	{
		return $this->_failures;
	}

	/**
	 * Get the list of failed email addresses
	 *
	 * @param   integer $user_id   User ID
	 * @param   integer $object_id Object ID
	 * @return  string
	 */
	public function buildToken($user_id, $object_id)
	{
		$encryptor = new Token();
		return $encryptor->buildEmailToken(1, 1, $user_id, $object_id);
	}

	/**
	 * Add an attachment
	 *
	 * @param   mixed  $attachment File path (string) or object (Swift_Mime_MimeEntity)
	 * @param   string $filename   Optional filename to set
	 * @return  object
	 */
	public function addAttachment($attachment, $filename=null)
	{
		if (!($attachment instanceof Swift_Mime_MimeEntity))
		{
			$attachment = \Swift_Attachment::fromPath($attachment);
		}

		if ($filename && is_string($filename))
		{
			$attachment->setFilename($filename);
		}

		return $this->attach($attachment);
	}

	/**
	 * Remove an attachment
	 *
	 * @param   mixed  $attachment File path (string) or object (Swift_Mime_MimeEntity)
	 * @return  object
	 */
	public function removeAttachment($attachment)
	{
		if (!($attachment instanceof Swift_Mime_MimeEntity))
		{
			$attachment = \Swift_Attachment::fromPath($attachment);
		}

		return $this->detach($attachment);
	}
}
