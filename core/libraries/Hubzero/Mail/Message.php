<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
	 * @var  array
	 */
	private $_failures = null;

	/**
	 * Message tags
	 *
	 * @var  array
	 */
	private $_tags = array();

	/**
	 * Message transporters
	 *
	 * @var  array
	 */
	private static $_transporters = array();

	/**
	 * Check if message needs to be sent as multipart
	 * MIME message or if it has only one part.
	 *
	 * @return  bool
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
	 * @param   mixed  $priority  integer|string
	 * @return  object
	 */
	public function setPriority($priority)
	{
		if (is_string($priority))
		{
			switch (strtolower($priority))
			{
				case 'high':
					$priority = 1;
					break;

				case 'normal':
					$priority = 3;
					break;

				case 'low':
					$priority = 5;
					break;

				default:
					$priority = 3;
					break;
			}
		}
		return parent::setPriority($priority);
	}

	/**
	 * Send the message
	 *
	 * @return  object
	 */
	public function send($transporter='', $options=array())
	{
		$transporter = $transporter ? $transporter : \Config::get('mailer');

		if (is_object($transporter) && ($transporter instanceof \Swift_Transport))
		{
			// We were given a valid tranport mechanisms, so just use it
			$transport = $transporter;
		}
		elseif (is_string($transporter) && self::hasTrasporter($transporter))
		{
			$transport = self::getTrasporter($transporter);
		}
		else
		{
			switch (strtolower($transporter))
			{
				case 'smtp':
					if (!isset($options['host']))
					{
						$options['host'] = \Config::get('smtphost');
					}
					if (!isset($options['port']))
					{
						$options['port'] = \Config::get('smtpport');
					}
					if (!isset($options['username']))
					{
						$options['username'] = \Config::get('smtpuser');
					}
					if (!isset($options['password']))
					{
						$options['password'] = \Config::get('smtppass');
					}

					if (!empty($options))
					{
						$transport = \Swift_SmtpTransport::newInstance($options['host'], $options['port']);
						$transport->setUsername($options['username'])
						          ->setPassword($options['password']);
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
		}

		$mailer = \Swift_Mailer::newInstance($transport);
		$result = $mailer->send($this, $this->_failures);

		if ($result)
		{
			\Log::info(sprintf('Mail sent to %s', json_encode($this->getTo())));
		}
		else
		{
			\Log::error(sprintf('Failed to mail %s', json_encode($this->getTo())));
		}

		return $result;
	}

	/**
	 * Get the list of failed email addresses
	 *
	 * @return  array|null
	 */
	public function getFailures()
	{
		return $this->_failures;
	}

	/**
	 * Generates email token
	 *
	 * @param   integer  $user_id    User ID
	 * @param   integer  $object_id  Object ID
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
	 * @param   mixed   $attachment  File path (string) or object (Swift_Mime_MimeEntity)
	 * @param   string  $filename    Optional filename to set
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
	 * @param   mixed  $attachment  File path (string) or object (Swift_Mime_MimeEntity)
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

	/**
	 * Get an embed string for an attachment
	 *
	 * @param   mixed  $attachment  File path (string) or object (Swift_Image)
	 * @return  object
	 */
	public function getEmbed($attachment)
	{
		if (!($attachment instanceof \Swift_Image))
		{
			$attachment = \Swift_Image::fromPath($attachment);
		}

		return $this->embed($attachment);
	}

	/**
	 * Sets tags on the message
	 *
	 * @param   array  $tags  The tags to set
	 * @return  void
	 */
	public function setTags($tags)
	{
		$this->_tags = $tags;
	}

	/**
	 * Grabs the message tags
	 *
	 * @return  array
	 */
	public function getTags()
	{
		return $this->_tags;
	}

	/**
	 * Adds a transport mechanisms to the known list
	 *
	 * @param   string  $name         the mechanism name
	 * @param   object  $transporter  the transporter object
	 * @return  void
	 */
	public static function addTransporter($name, $transporter)
	{
		self::$_transporters[$name] = $transporter;
	}

	/**
	 * Checks to see if a transporter by the given name exists
	 *
	 * @param   string  $name  The transporter name
	 * @return  bool
	 */
	public static function hasTrasporter($name)
	{
		return isset(self::$_transporters[$name]);
	}

	/**
	 * Gets the named transporter
	 *
	 * @param   string  $name  The transporter name
	 * @return  object
	 */
	public static function getTrasporter($name)
	{
		return self::$_transporters[$name];
	}
}
