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

namespace Components\Newsletter\Helpers;

/**
 * Helper functions
 */
class Helper
{
	/**
	 * Get the encrypter utility
	 *
	 * @return  void
	 */
	protected static function getEncrypter()
	{
		$key = \App::hash('newletter');

		$crypt = new \Hubzero\Encryption\Encrypter(
			new \Hubzero\Encryption\Cipher\Simple,
			new \Hubzero\Encryption\Key('simple', $key, $key)
		);

		return $crypt;
	}

	/**
	 * Generate Mailing Token - For Open Tracker, Click Tracker, & Unsubscribe Link
	 *
	 * @param 	$mailingRecipientObject		Mailing Recipient Object
	 * @return 	string						Email Token for verify and tracking
	 */
	public static function generateMailingToken($mailingRecipientObject)
	{
		//we must have valid mailing recipient object
		if (!is_object($mailingRecipientObject) || !$mailingRecipientObject->mailingid || !$mailingRecipientObject->email)
		{
			return false;
		}

		//instantiate simple crypt object
		$crypt = self::getEncrypter();

		//encrypt campaign id and current timestamp
		$token = $crypt->encrypt($mailingRecipientObject->mailingid . ':' . $mailingRecipientObject->email);

		//url encode and base64 encode token
		return urlencode(base64_encode($token));
	}

	/**
	 * Generate Confirmation Token
	 *
	 * @param 	$email			Confirmation Email Address
	 * @param 	$mailinglist	Mailing list
	 * @return 	string
	 */
	public static function generateConfirmationToken($emailAddress, $mailinglistObject)
	{
		//we must have valid email address and mailing list object
		if ($emailAddress == '' || !is_object($mailinglistObject))
		{
			return false;
		}

		//instantiate simple crypt object
		$crypt = self::getEncrypter();

		//encrypt campaign id and current timestamp
		$token = $crypt->encrypt($mailinglistObject->id . ':' . $emailAddress);

		//url encode and base64 encode token
		return urlencode(base64_encode($token));
	}

	/**
	 * Parse Mailing Token - For Open Tracker, Click Tracker, & Unsubscribe Link
	 *
	 * @param 	$mailingToken	String Mailing token
	 * @return 	object			Mailing Recipient Object
	 */
	public static function parseMailingToken($mailingToken)
	{
		//we must have a token
		if (!$mailingToken || $mailingToken == '')
		{
			return false;
		}

		//instantiate simple crypt object
		$crypt = self::getEncrypter();

		//url decode token
		$mailingToken = urldecode($mailingToken);

		//base64 decode token
		$mailingToken = base64_decode($mailingToken);

		//decrypt token
		$mailingToken = $crypt->decrypt($mailingToken);

		//split token
		$mailingTokenParts = explode(':', $mailingToken);

		//get the mailing id and email from parts
		$mailingId = (isset($mailingTokenParts[0])) ? $mailingTokenParts[0] : '';
		$email = (isset($mailingTokenParts[1])) ? $mailingTokenParts[1] : '';

		//make sure we have a mailing id and email
		if ($mailingId == '' || $email == '')
		{
			return false;
		}

		//instantiate database
		$database = \App::get('db');

		//try to load mailing recipient object to validate
		$sql = "SELECT * FROM #__newsletter_mailing_recipients
				WHERE mid=" . $database->quote($mailingId) . "
				AND email=" . $database->quote($email);
		$database->setQuery($sql);
		$recipient = $database->loadObject();

		//return mailing recipient object which contains (mailing id, email, and mailing status)
		//let invidual methods handle from here
		return $recipient;
	}

	/**
	 * Parse Confirmation Email & Token to make sure its was a valid combination
	 *
	 * @param 	$email		Confirmation Email Address
	 * @param 	$token 		Confirmation Token
	 * @return 	array()
	 */
	public static function parseConfirmationToken($confirmationToken)
	{
		//make sure we have a token
		if (!$confirmationToken || $confirmationToken == '')
		{
			return false;
		}

		//instantiate simple crypt object
		$crypt = self::getEncrypter();

		//url decode token
		$confirmationToken = urldecode($confirmationToken);

		//base64 decode token
		$confirmationToken = base64_decode($confirmationToken);

		//descrypt token
		$confirmationToken = $crypt->decrypt($confirmationToken);

		//parse token
		$confirmationTokenParts = array_map("trim", explode(':', $confirmationToken));

		//get mailing list id & email
		$mailinglistId = (isset($confirmationTokenParts[0])) ? $confirmationTokenParts[0] : '';
		$email = (isset($confirmationTokenParts[1])) ? $confirmationTokenParts[1] : '';

		//make sure we have a mailing list and email
		if ($mailinglistId == '' || $email == '')
		{
			return false;
		}

		//instantiate database
		$database = \App::get('db');

		//attempt to load mailing list email object
		$sql = "SELECT * FROM #__newsletter_mailinglist_emails AS mle
				WHERE mle.mid=" . $database->quote($mailinglistId) . "
				AND mle.email=" . $database->quote($email);
		$database->setQuery($sql);
		$mailinglistEmail = $database->loadObject();

		return $mailinglistEmail;
	}


	/**
	 * Creates empty GIF image
	 *
	 * @return 	void
	 */
	public static function mailingOpenTrackerGif ()
	{
		//create fake image to output
		$im = imagecreatetruecolor(1, 1);
		$red = imagecolorallocate($im, 255, 0, 0);
		imagecolortransparent($im, $red);
		imagefilledrectangle($im, 0, 0, 99, 99, 0xFF0000);
		header('Content-Type: image/gif');
		imagegif ($im);
		imagedestroy($im);
	}


	/**
	 * Send confirmation Email to user
	 *
	 * @param 	$email			Confirmation Email Address
	 * @param 	$mailinglist	Mailing list we just subscribed to
	 * @param 	$addedByAdmin	Did we sign up or we were added by admin?
	 * @return 	void
	 */
	public static function sendMailinglistConfirmationEmail($emailAddress, $mailinglistObject, $addedByAdmin = true)
	{
		// create from details
		$from = array(
			'name'  => \Config::get('sitename') . ' Mailing Lists',
			'email' => 'hubmail-mailinglists@' . $_SERVER['HTTP_HOST']
		);

		// create replyto details
		$replyto = array(
			'name'  => 'DO NOT REPLY',
			'email' => 'do-not-reply@' . $_SERVER['HTTP_HOST']
		);

		//build subject
		$subject = "Confirm Email Subscription to '" . $mailinglistObject->name . "' on " . \Config::get('sitename');

		//get token
		$token = self::generateConfirmationToken($emailAddress, $mailinglistObject);

		//build body
		if ($addedByAdmin)
		{
			$body = "You are receiving this email because you have been added to the following mailing list by a site administrator. ";
		}
		else
		{
			$body = "You are receiving this email because you have signed up for the following mailing list. ";
		}
		$body .= "Please confirm or remove your email subscription by clicking on one of the links below" . PHP_EOL . PHP_EOL;
		$body .= $mailinglistObject->name . PHP_EOL;
		$body .= $mailinglistObject->description . PHP_EOL . PHP_EOL . PHP_EOL;
		$body .= "========================================================================" . PHP_EOL . PHP_EOL;
		$body .= "Click this link to CONFIRM your subscription:" . PHP_EOL;
		$body .= 'https://' . $_SERVER['HTTP_HOST'] . '/newsletter/confirm?e=' . urlencode($emailAddress) . '&t=' . $token . PHP_EOL . PHP_EOL;
		$body .= "------------------------------------------------------------------------" . PHP_EOL . PHP_EOL;
		$body .= "Click this link to REMOVE this email from the mailing list:" . PHP_EOL;
		$body .= 'https://' . $_SERVER['HTTP_HOST'] . '/newsletter/remove?e=' . urlencode($emailAddress) . '&t=' . $token . PHP_EOL . PHP_EOL;
		$body .= "========================================================================";

		// create new message
		$message = new \Hubzero\Mail\Message();

		// build message object and send
		$message->setSubject($subject)
				->addFrom($from['email'], $from['name'])
				->setReplyTo($replyto['email'], $replyto['name'])
				->setTo($emailAddress)
				->addHeader('X-Mailer', 'PHP/' . phpversion())
				->addHeader('X-Component', 'com_newsletter')
				->addHeader('X-Component-Object', 'Mailinglist')
				->addHeader('X-Component-ObjectId', $mailinglistObject->id)
				->addPart($body, 'text/plain')
				->send();

		return true;
	}


	/**
	 * Helper Function to add all tracking methods to email message
	 *
	 * @param    $emailMessage    Email Body
	 * @param    $emailToken      Email Token to track per message
	 * @return   String
	 */
	public static function addTrackingToEmailMessage($emailMessage, $emailToken)
	{
		$emailMessage = str_replace('&#8203;', '', $emailMessage);
		$emailMessage = self::addclickTrackingtoEmailMessage($emailMessage, $emailToken);
		$emailMessage = self::addOpenTrackingToEmailMessage($emailMessage, $emailToken);
		$emailMessage = self::addPrintTrackingToEmailMessage($emailMessage, $emailToken);
		$emailMessage = self::addForwardingToEmailMessage($emailMessage, $emailToken);
		return $emailMessage;
	}


	/**
	 * Get protocol for tracking
	 *
	 * @return   String
	 */
	public static function getNewsletterTrackingProtocol()
	{
		//get params for com newsletter
		$params = \Component::params('com_newsletter');

		//return protocol
		return $params->get('email_tracking_protocol', 'http');
	}


	/**
	 * Add Click Tracking to Email Message
	 *
	 * @param    $emailMessage    Email Body
	 * @param    $emailToken      Email Token to track per message
	 * @return   String
	 */
	public static function addclickTrackingtoEmailMessage($emailMessage, $emailToken)
	{
		//get protocol to track with
		$protocol = self::getNewsletterTrackingProtocol();

		//get all links in email body
		preg_match_all('/<a.*href="([^"]+)"[^>]*>/', $emailMessage, $urls);

		//add clicking to each link
		if ($urls && count($urls[1]) > 0)
		{
			foreach ($urls[1] as $url)
			{
				//only track http & https links
				if (substr($url, 0, 4) == 'http')
				{
					//build tracking url
					$clickTracker = $protocol . '://' . $_SERVER['SERVER_NAME'] . '/newsletter/track/click/?t=' . $emailToken . '&l=' . urlencode($url);

					//replace normal links with tracking links
					// Make sure to only replace links wrapped in href="" otherwise
					// a link inbetween the anchor tags will get replaced too. Ex:
					//    <a href="http://ink?clicktracking">http://ink?clicktracking</a>
					$emailMessage = str_replace('href="' . $url . '"', 'href="' . $clickTracker . '"', $emailMessage);
				}
			}
		}

		return $emailMessage;
	}


	/**
	 * Add Open Tracking to Email Message
	 *
	 * @param    $emailMessage    Email Body
	 * @param    $emailToken      Email Token to track per message
	 * @return   String
	 */
	public static function addOpenTrackingToEmailMessage($emailMessage, $emailToken)
	{
		//get protocol to track with
		$protocol = self::getNewsletterTrackingProtocol();

		//create open tracker img
		$openTracker = '<img src="' . $protocol . '://' . $_SERVER['SERVER_NAME'] . '/newsletter/track/open?t=' . $emailToken . '" width="1" height="1" />';

		//add to the end of the message body
		$emailMessage = str_replace('</body>', $openTracker . '</body>', $emailMessage);

		//return message body
		return $emailMessage;
	}


	/**
	 * Add Print Tracking to Email Message
	 *
	 * @param    $emailMessage    Email Body
	 * @param    $emailToken      Email Token to track per message
	 * @return   String
	 */
	public static function addPrintTrackingToEmailMessage($emailMessage, $emailToken)
	{
		//get protocol to track with
		$protocol = self::getNewsletterTrackingProtocol();

		//create print tracker
		$printTracker = "<style>
							@media print {
								#_print {
									width:1px;
									height:1px;
									border:none;
									background-color: transparent;
									background-image: url('" . $protocol . "://" . $_SERVER['SERVER_NAME'] . "/newsletter/track/print?t=" . $emailToken . "');
								}
							}
						</style>";
		$printTracker .= "<div id=\"_print\"></div>";

		//add to the end of the message body
		$emailMessage = str_replace('</body>', $printTracker . '</body>', $emailMessage);

		//return message body
		return $emailMessage;
	}


	/**
	 * Add Forwarding Tracking to Email Message
	 *
	 * @param    $emailMessage    Email Body
	 * @param    $emailToken      Email Token to track per message
	 * @return   String
	 */
	public static function addForwardingToEmailMessage($emailMessage, $emailToken)
	{
		//get protocol to track with
		$protocol = self::getNewsletterTrackingProtocol();

		//create forward tracker
		$forwardTracker = "<style>
							div.OutlookMessageHeader,
							table.moz-email-headers-table,
							blockquote #_forward,
							.gmail_quote #_forward {
								width:1px;
								height:1px;
								border:none;
								background-color: transparent;
								background-image: url('" . $protocol . "://" . $_SERVER['SERVER_NAME'] . "/newsletter/track/forward?t=" . $emailToken . "');
							}
						</style>";
		$forwardTracker .= "<div id=\"_forward\"></div>";

		//add to the end of the message body
		$emailMessage = str_replace('</body>', $forwardTracker . '</body>', $emailMessage);

		//return message body
		return $emailMessage;
	}
}