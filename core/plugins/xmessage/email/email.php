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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
		if ($user->get('emailConfirmed') <= 0)
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

		if (is_array($xmessage->message))
		{
			if (isset($xmessage->message['attachments']))
			{
				if (!is_array($xmessage->message['attachments']))
				{
					$xmessage->message['attachments'] = array($xmessage->message['attachments']);
				}
				foreach ($xmessage->message['attachments'] as $path)
				{
					if (preg_match("/\.(bmp|gif|jpg|jpe|jpeg|png)$/i", $path))
					{
						$file = basename($path);
						$xmessage->message['multipart'] = preg_replace(
							'/<a class="img" data\-filename="' . str_replace('.', '\.', $file) . '" href="(.*?)"\>(.*?)<\/a>/i',
							'<img src="' . $message->getEmbed($path) . '" alt="" />',
							$xmessage->message['multipart']
						);
					}
					else
					{
						$message->addAttachment($path);
					}
				}
			}

			$message->addPart($xmessage->message['plaintext'], 'text/plain')
			        ->addPart($xmessage->message['multipart'], 'text/html');
		}
		else
		{
			$message->setBody($xmessage->message);
		}

		// send mail
		if ($message->send())
		{
			return true;
		}
		return false;
	}
}
