<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * XMessage plugin class for email
 */
class plgXMessageEmail extends \Hubzero\Plugin\Plugin
{
	/**
	 * Return this messaging type
	 *
	 * @return     string
	 */
	public function onMessageMethods()
	{
		return 'email';
	}

	/**
	 * Send a message to a user
	 *
	 * @param      array   $from     Message 'from' data (e.g., name, address)
	 * @param      object  $xmessage The message to send
	 * @param      object  $user     User to send the message to
	 * @param      string  $action   Messaging method (e.g., email, smstxt, etc.)
	 * @return     boolean True if message was sent
	 */
	public function onMessage($from, $xmessage, $user, $action)
	{
		//make sure were supposed to be performing this action
		if ($this->onMessageMethods() != $action)
		{
			return true;
		}

		//check to make sure users account is confirmed
		if ($user->get('activation') <= 0)
		{
			return false;
		}

		//if we dont have an email stop
		if (!$user->get('email'))
		{
			return false;
		}

		// if we dont have a from set the use site from name and email
		if (!isset($from['name']) || $from['name'] == '')
		{
			$from['name'] = Config::get('sitename') . ' Administrator';
		}
		if (!isset($from['email']) || $from['email'] == '')
		{
			$from['email'] = Config::get('mailfrom');
		}

		$name = $user->get('name');
		if (preg_match('/[А-Яа-яЁё]/u', $name))
		{
			$name = $user->get('email');
		}

		$message = new \Hubzero\Mail\Message();
		$message->setSubject(Config::get('sitename') . ' ' . $xmessage->subject)
		        ->addFrom($from['email'], $from['name'])
		        ->addTo($user->get('email'), $name);

		// In case a different reply to email address is specified
		if (array_key_exists('replytoemail', $from))
		{
			$replytoname = (isset($from['replytoname']) && $from['replytoname'] != '') ? $from['replytoname'] : $from['name'];

			if (preg_match('/[А-Яа-яЁё]/u', $replytoname))
			{
				$replytoname = $from['replytoemail'];
			}

			$message->addReplyTo($from['replytoemail'], $replytoname);
		}
		else
		{
			if (preg_match('/[А-Яа-яЁё]/u', $from['name']))
			{
				$from['name'] = $from['email'];
			}

			$message->addReplyTo($from['email'], $from['name']);
		}

		//set mail additional args (mail return path - used for bounces)
		$message->addHeader('X-Component', $xmessage->component)
		        ->addHeader('X-Component-Object', $xmessage->type);

		// Want to add some extra headers? We put them into the from array
		// If none are there, this breaks nothing
		if (array_key_exists('xheaders', $from))
		{
			// The xheaders array has name and value pairs
			foreach ($from['xheaders'] as $n => $v)
			{
				$message->addHeader($n, $v);
			}
		}

		$m = $xmessage->get('message');

		if (is_array($m))
		{

			if (isset($m['attachments']))
			{
				if (!is_array($m['attachments']))
				{
					$m['attachments'] = array($m['attachments']);
				}
				foreach ($m['attachments'] as $path)
				{
					if (preg_match("/\.(bmp|gif|jpg|jpe|jpeg|png)$/i", $path))
					{
						$file = basename($path);
						$m['multipart'] = preg_replace(
							'/<a class="img" data\-filename="' . str_replace('.', '\.', $file) . '" href="(.*?)"\>(.*?)<\/a>/i',
							'<img src="' . $message->getEmbed($path) . '" alt="" />',
							$m['multipart']
						);
					}
					else
					{
						$message->addAttachment($path);
					}
				}
			}

			$message->addPart($m['plaintext'], 'text/plain')
			        ->addPart($m['multipart'], 'text/html');
		}
		else
		{
			$message->setBody($m);
		}

		// send mail
		if ($message->send())
		{
			return true;
		}
		return false;
	}
}
