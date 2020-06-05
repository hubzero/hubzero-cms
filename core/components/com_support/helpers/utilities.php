<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Support\Helpers;

use Hubzero\Mail\Message;
use Request;
use Config;

/**
 * Support Utilities class
 */
class Utilities
{
	/**
	 * Send an email
	 *
	 * @param   string   $email              Address to send to
	 * @param   string   $subject            Message subject
	 * @param   mixed    $contents           Message to send
	 * @param   array    $from               Who the message is from
	 * @param   array    $replyto            Reply to information
	 * @param   array    $additionalHeaders  More headers to apply
	 * @return  boolean
	 */
	public static function sendEmail($email, $subject, $contents, $from, $replyto = '', $additionalHeaders = null)
	{
		if (!$from)
		{
			return false;
		}

		$message = new Message();
		$message->setSubject(Config::get('sitename') . ' ' . $subject)
		        ->addFrom($from['email'], $from['name'])
		        ->addTo($email);

		if ($replyto)
		{
			$message->addReplyTo($replyto, $from['name']);
		}
		else
		{
			$message->addReplyTo($from['email'], $from['name']);
		}

		if (is_array($additionalHeaders))
		{
			// The xheaders array has name and value pairs
			foreach ($additionalHeaders as $header)
			{
				$message->addHeader($header['name'], $header['value']);
			}
		}

		if (is_array($contents))
		{
			if (isset($contents['attachments']))
			{
				if (!is_array($contents['attachments']))
				{
					$contents['attachments'] = array($contents['attachments']);
				}
				foreach ($contents['attachments'] as $path)
				{
					if (preg_match("/\.(bmp|gif|jpg|jpe|jpeg|png)$/i", $path))
					{
						$file = basename($path);
						$size = getimagesize($path);
						$width = ($size[0] > 650 ? 650 : $size[0]);
						$contents['multipart'] = preg_replace('/<a class="img" data\-filename="' . str_replace('.', '\.', $file) . '" href="(.*?)"\>(.*?)<\/a>/i', '<img width="' . $width . '" src="' . $message->getEmbed($path) . '" alt="" />', $contents['multipart']);
					}
					else
					{
						$message->addAttachment($path);
					}
				}
			}

			$message->addPart($contents['plaintext'], 'text/plain')
			        ->addPart($contents['multipart'], 'text/html');
		}
		else
		{
			$message->setBody($contents);
		}

		if ($message->send())
		{
			return true;
		}

		return false;
	}

	/**
	 * Check if a username is valid
	 *
	 * @param   string   $login  Username to check
	 * @return  boolean  True if valid
	 */
	public static function checkValidLogin($login)
	{
		if (preg_match("#^[_0-9a-zA-Z]+$#i", $login))
		{
			return true;
		}
		return false;
	}

	/**
	 * Check if an email address is valid
	 *
	 * @param   string   $email  Address to check
	 * @return  boolean  True if valid
	 */
	public static function checkValidEmail($email)
	{
		if (preg_match("#^[_\.\%0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$#i", $email))
		{
			return true;
		}
		return false;
	}

	/**
	 * Generate an array of severities
	 *
	 * @param   string  $severities  Comma-separated list
	 * @return  array
	 */
	public static function getSeverities($severities=null)
	{
		/*$s = array('major', 'normal', 'minor');

		if ($severities)
		{
			$s = explode(',', $severities);
			$s = array_map('trim', $s);
		}*/

		return array('critical', 'major', 'normal', 'minor');
	}

	/**
	 * Retrieve and parse incoming filters
	 *
	 * @return  array
	 */
	public static function getFilters()
	{
		// Query filters defaults
		$filters = array();
		$filters['search']     = '';
		$filters['status']     = 'open';
		$filters['type']       = 0;
		$filters['owner']      = '';
		$filters['reportedby'] = '';
		$filters['severity']   = 'normal';
		$filters['severity']   = '';

		$filters['sort']       = trim(Request::getState('com_support.tickets.sort', 'filter_order', 'created'));
		$filters['sortdir']    = trim(Request::getState('com_support.tickets.sortdir', 'filter_order_Dir', 'DESC'));

		// Paging vars
		$filters['limit']      = Request::getState('com_support.tickets.limit', 'limit', Config::get('list_limit'), 'int');
		$filters['start']      = Request::getState('com_support.tickets.limitstart', 'limitstart', 0, 'int');

		// Incoming
		$filters['_find']      = urldecode(trim(Request::getState('com_support.tickets.find', 'find', '')));
		$filters['_show']      = urldecode(trim(Request::getState('com_support.tickets.show', 'show', '')));

		// Break it apart so we can get our filters
		// Starting string hsould look like "filter:option filter:option"
		if ($filters['_find'] != '')
		{
			$chunks = explode(' ', $filters['_find']);
			$filters['_show'] = '';
		}
		else
		{
			$chunks = explode(' ', $filters['_show']);
		}

		// Loop through each chunk (filter:option)
		foreach ($chunks as $chunk)
		{
			if (!strstr($chunk, ':'))
			{
				$chunk = trim($chunk, '"');
				$chunk = trim($chunk, "'");

				$filters['search'] = $chunk;
				continue;
			}

			// Break each chunk into its pieces (filter, option)
			$pieces = explode(':', $chunk);

			// Find matching filters and ensure the vaule provided is valid
			switch ($pieces[0])
			{
				case 'q':
					$pieces[0] = 'search';
					if (isset($pieces[1]))
					{
						$pieces[1] = trim($pieces[1], '"');  // Remove any surrounding quotes
						$pieces[1] = trim($pieces[1], "'");  // Remove any surrounding quotes
					}
					else
					{
						$pieces[1] = $filters[$pieces[0]];
					}
				break;
				case 'status':
					$allowed = array('open', 'closed', 'all', 'waiting', 'new');
					if (!in_array($pieces[1], $allowed))
					{
						$pieces[1] = $filters[$pieces[0]];
					}
				break;
				case 'type':
					$allowed = array(
						'submitted' => 0,
						'automatic' => 1,
						'none'      => 2,
						'tool'      => 3
					);
					if (in_array($pieces[1], $allowed))
					{
						$pieces[1] = $allowed[$pieces[1]];
					}
					else
					{
						$pieces[1] = 0;
					}
				break;
				case 'owner':
				case 'reportedby':
					if (isset($pieces[1]))
					{
						if ($pieces[1] == 'me')
						{
							$pieces[1] = User::get('username');
						}
						else if ($pieces[1] == 'none')
						{
							$pieces[1] = 'none';
						}
					}
				break;
				case 'severity':
					$allowed = array('critical', 'major', 'normal', 'minor', 'trivial');
					if (!in_array($pieces[1], $allowed))
					{
						$pieces[1] = $filters[$pieces[0]];
					}
				break;
			}

			$filters[$pieces[0]] = (isset($pieces[1])) ? $pieces[1] : '';
		}

		// Return the array
		return $filters;
	}

	/**
	 * Calculate the average life of a ticket
	 *
	 * @param   array  $data  A list of ticket's opened and closed dates
	 * @return  array  [days, hours, minutes]
	 */
	public static function calculateAverageLife($data=null)
	{
		$lifetime = array();

		if ($data && is_array($data))
		{
			$count = 0;
			$lt = 0;
			foreach ($data as $tim)
			{
				$lt += $tim->closed - $tim->opened;
				$count++;
			}
			$difference = ($lt / $count);
			if ($difference < 0)
			{
				$difference = 0;
			}

			$days    = floor($difference/60/60/24);
			$hours   = floor(($difference - $days*60*60*24)/60/60);
			$minutes = floor(($difference - $days*60*60*24 - $hours*60*60)/60);

			$lifetime = array($days, $hours, $minutes);
		}
		return $lifetime;
	}

	/**
	 * Add attachments
	 *
	 * @param   integer  $ticketid
	 * @param   itneger  $commentid
	 * @return  void
	 */
	public static function addAttachments($ticketid, $commentid=0)
	{
		$attachments = Request::getArray('attachments', null, 'files');

		if (is_array($attachments) && count($attachments) > 0 && is_array($attachments['name']))
		{
			for ($i = 0; $i < count($attachments['name']); $i++)
			{
				$attachment = \Components\Support\Models\Attachment::blank();
				$attachment->set('ticket', $ticketid);
				$attachment->set('comment_id', $commentid);
				$attachment->addFile($attachments['tmp_name'][$i], $attachments['name'][$i], $ticketid);

				if (!$attachment->save())
				{
					throw new \Exception($attachment->getError(), 500);
				}
			}
		}
	}

	/**
	 * Check if a string is base64 encoded
	 *
	 * @param   string  $str
	 * @return  bool
	 */
	public static function isBase64($str)
	{
		// Check if there are valid base64 characters
		if (!preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $str))
		{
			return false;
		}

		// Decode the string in strict mode and check the results
		$decoded = base64_decode($str, true);
		if (false === $decoded)
		{
			return false;
		}

		// Encode the string again
		if (base64_encode($decoded) != $str)
		{
			return false;
		}

		return true;
	}
}
