<?php
/**
 * @package	framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license	http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Mail;

/**
 * Class for creating and sending email
 */
class Message extends \Symfony\Component\Mime\Email
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
		return parent::priority($priority);
	}

	/**
	 * Send the message
	 *
	 * @return  object
	 */
	public function send($transport='', $options=array())
	{
		if (is_subclass_of($transport, '\Symfony\Component\Mailer\Transport\AbstractTransport'))
		{
			// We have our transport
		}
		else if (is_string($transport) && self::hasTransporter($transport))
		{
			// Use the registered custom HUBzero transport
			$transport = self::getTransporter($transport);
		}
		else
		{
			$dsn = \Config::get('mailer_dsn', '');

			if (!$dsn)
			{
				$scheme = strtolower(trim(\Config::get('mailer','sendmail')));
				$host = strtolower(trim(\Config::get('smtphost','localhost')));
				$port = strtolower(trim(\Config::get('smtpport','0')));
				$username = strtolower(trim(\Config::get('smtpuser','')));
				$password = strtolower(trim(\Config::get('smtppass','')));

				switch ($scheme)
				{
					case 'smtp':
						$dsn = "smtp://";

						if ($username || $password)
						{
							$dsn .= $username;

							if ($password)
							{
								$dsn .= ":" . $password;
							}

							$dsn .= "@";
						}

						$dsn .= $host;

						if ($port)
						{
							$dsn .= ":" . $port;
						}

						break;

					case 'sendmail':
						$dsn = "sendmail://default";
						break;

					case 'mail':
					case 'native':
						$dsn = "native://default";
						break;

					case 'mandrill+smtp':
						$dsn = "mandrill+smtp://";

						if ($username || $password)
						{
							$dsn .= $username;

							if ($password)
							{
								$dsn .= ":" . $password;
							}

							$dsn .= "@";
						}

						$dsn .= "default";

						break;
				}
			}

			$transport = \Symfony\Component\Mailer\Transport::fromDsn($dsn);

			if (!is_subclass_of($transport, '\Symfony\Component\Mailer\Transport\AbstractTransport'))
			{
				throw new \InvalidArgumentException('Invalid transport specified');
			}
		}

		$mailer = new \Symfony\Component\Mailer\Mailer($transport);

		try
		{
			$mailer->send($this, $this->_failures);
			$result = true;
		}
		catch (Exception $e)
		{
			$result = false;
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
	 * @param   integer  $user_id	User ID
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
	 * @param   mixed   $attachment  File path (string) or object (Symfony\Component\Mime\Part\DataPart)
	 * @param   string  $filename	Optional filename to set
	 * @return  object
	 */
	public function addAttachment($attachment, $filename=null)
	{
		if (!($attachment instanceof Symfony\Component\Mime\Part\DataPart))
		{
			$attachment = \Symfony\Component\Mime\Part\DataPart::fromPath($attachment, $filename);
		}

		return $this->attachPart($attachment);
	}

	/**
	 * Remove an attachment
	 *
	 * @param   mixed  $attachment  File path (string) or object (Symfony\Component\Mime\Part\DataPart)
	 * @return  object
	 */
	public function removeAttachment($attachment)
	{
		if (!($attachment instanceof Symfony\Component\Mime\Part\DataPart))
		{
			$attachment = Symfony\Component\Mime\Part\DataPart::fromPath($attachment);
		}

		return $this->detach($attachment);
	}

	/**
	 * Get an embed string for an attachment
	 *
	 * @param   mixed  $attachment  File path (string)
	 * @return  object
	 */
	public function getEmbed($attachment)
	{
		$cid =  bin2hex(random_bytes(16)).'@hubzero';

		$this->embedFromPath($attachment, $cid);

		return $cid;
	}

	/**
	 * Set the body of this entity, as a string. (compatability function)
	 *
	 * @param mixed  $body
	 * @param string $contentType optional
	 *
	 * @return $this
	 */
	public function setBody(...$params)
	{
		if (isset($params[1]))
		{
			$this->text($params[0], $params[1]);
		}
		else
		{
			$this->text($params[0]);
		}

		return $this;
	}

	/**
	 * Set the subject of this message. (compatability function)
	 *
	 * @param string $subject
	 *
	 * @return $this
	 */
	public function setSubject($subject)
	{
		return $this->subject($subject);
	}

	/**
	 * Set the from address of this message (compatability function)
	 *
	 * You may pass an array of addresses if this message is from multiple people.
	 *
	 * If $name is passed and the first parameter is a string, this name will be
	 * associated with the address.
	 *
	 * @param string|array $addresses
	 * @param string	   $name	  optional
	 *
	 * @return $this
	 */
	public function setFrom($addresses, $name = null)
	{
		if (!is_array($addresses) && isset($name))
		{
			$addresses = array($addresses => $name);
		}

		foreach($addresses as $key => $value)
		{
			if (is_numeric($key))
			{
				$address = new \Symfony\Component\Mime\Address($value);
			}
			else
			{
				$address = new \Symfony\Component\Mime\Address($key, $value);
			}

			if (isset($first_set))
			{
				parent::from($address);
				$first_set = true;
			}
			else
			{
				parent::addFrom($address);
			}
		}

		return $this;
	}

	/**
	 * Add a From: address to this message. (compatability function)
	 *
	 * If $name is passed this name will be associated with the address.
	 *
	 * @param string $address
	 * @param string $name	optional
	 *
	 * @return $this
	 */
	public function addFrom(...$address)
	{
		if (isset($address[1]))
		{
			$address = new \Symfony\Component\Mime\Address($address[0], $address[1]);
		}
		else
		{
			$address = new \Symfony\Component\Mime\Address($address[0]);
		}

		parent::addFrom($address);

		return $this;
	}

	/**
	 * Set the to addresses of this message. (compatability function)
	 *
	 * If multiple recipients will receive the message an array should be used.
	 * Example: array('receiver@domain.org', 'other@domain.org' => 'A name')
	 *
	 * If $name is passed and the first parameter is a string, this name will be
	 * associated with the address.
	 *
	 * @param mixed  $addresses
	 * @param string $name	  optional
	 *
	 * @return $this
	 */
	public function setTo($addresses, $name = '')
	{
		if (!is_array($addresses))
		{
			$addresses = array($addresses => $name);
		}

		foreach($addresses as $key => $value)
		{
			if (is_numeric($key))
			{
				$address = new \Symfony\Component\Mime\Address($value);
			}
			else
			{
				$address = new \Symfony\Component\Mime\Address($key, $value);
			}

			if (!isset($first_set))
			{
				parent::to($address);

				$first_set = true;
			}
			else
			{
				parent::addTo($address);
			}
		}

		return $this;
	}

	/**
	 * Add a To: address to this message. (compatability function)
	 *
	 * If $name is passed this name will be associated with the address.
	 *
	 * @param string $address
	 * @param string $name	optional
	 *
	 * @return $this
	 */
	public function addTo(...$address)
	{
		if (isset($address[1]))
		{
			$address = new \Symfony\Component\Mime\Address($address[0], $address[1]);
		}
		else
		{
			$address = new \Symfony\Component\Mime\Address($address[0]);
		}

		parent::addTo($address);

		return $this;
	}

	/**
	 * Set the reply-to address of this message. (compatability function)
	 *
	 * You may pass an array of addresses if replies will go to multiple people.
	 *
	 * If $name is passed and the first parameter is a string, this name will be
	 * associated with the address.
	 *
	 * @param mixed  $addresses
	 * @param string $name	  optional
	 *
	 * @return $this
	 */
	public function setReplyTo($addresses, $name = '')
	{
		if (!is_array($addresses)) {
			$addresses = array($addresses => $name);
		}

		foreach($addresses as $key => $value)
		{
			if (is_numeric($key))
			{
				$address = new \Symfony\Component\Mime\Address($value);
			}
			else
			{
				$address = new \Symfony\Component\Mime\Address($key, $value);
			}

			if (isset($first_set))
			{
				parent::reply($address);
				$first_set = true;
			}
			else
			{
				parent::addReplyTo($address);
			}
		}

		return $this;
	}

	/**
	 * Add a Reply-To: address to this message. (compatability function)
	 *
	 * If $name is passed this name will be associated with the address.
	 *
	 * @param string $address
	 * @param string $name	optional
	 *
	 * @return $this
	 */
	public function addReplyTo(...$address)
	{
		if (isset($address[1]))
		{
			$address = new \Symfony\Component\Mime\Address($address[0], $address[1]);
		}
		else
		{
			$address = new \Symfony\Component\Mime\Address($address[0]);
		}

		return parent::addReplyTo($address);
	}

	/**
	 * Set the Bcc addresses of this message. (compatability function)
	 *
	 * If $name is passed and the first parameter is a string, this name will be
	 * associated with the address.
	 *
	 * @param mixed  $addresses
	 * @param string $name	  optional
	 *
	 * @return $this
	 */
	public function setBcc($addresses, $name = null)
	{
		if (!is_array($addresses) && isset($name)) {
			$addresses = array($addresses => $name);
		}

		foreach($addresses as $key => $value)
		{
			if (is_numeric($key))
			{
				$address = new \Symfony\Component\Mime\Address($value);
			}
			else
			{
				$address = new \Symfony\Component\Mime\Address($key, $value);
			}

			if (isset($first_set))
			{
				parent::bcc($address);
				$first_set = true;
			}
			else
			{
				parent::addBcc($address);
			}
		}

		return $this;
	}

	/**
	 * Add a Bcc: address to this message. (compatability function)
	 *
	 * If $name is passed this name will be associated with the address.
	 *
	 * @param string $address
	 * @param string $name	optional
	 *
	 * @return $this
	 */
	public function addBcc(...$address)
	{
		if (isset($address[1]))
		{
			$address = new \Symfony\Component\Mime\Address($address[0], $address[1]);
		}
		else
		{
			$address = new \Symfony\Component\Mime\Address($address[0]);
		}

		return parent::addBcc($address);
	}

	 /**
	 * Add a MimePart to this Message. (compatability function)
	 *
	 * @param string $body
	 * @param string $contentType
	 * @param string $charset
	 *
	 * @return $this
	 */
	public function addPart($body, $contentType = null, $charset = '')
	{
		if ($contentType == "text/html")
		{
			$this->html($body, $charset);
		}
		else if ($contentType == "text/plain")
		{
			$this->text($body, $charset);
		}
		else
		{
			$this->attach($body, null, $contentType);
		}

		return $this;
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
	 * @param   string  $name		 the mechanism name
	 * @param   object  $transporter the transporter object
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
	public static function hasTransporter($name)
	{
		return isset(self::$_transporters[$name]);
	}

	/**
	 * Gets the named transporter
	 *
	 * @param   string  $name  The transporter name
	 * @return  object
	 */
	public static function getTransporter($name)
	{
		return self::$_transporters[$name];
	}
}
