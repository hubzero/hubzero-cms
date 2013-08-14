<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

class Hubzero_Newsletter_Helper
{
	/**
	 * Generate Mailing Token - For Open Tracker, Click Tracker, & Unsubscribe Link
	 *
	 * @param 	$mailingRecipientObject		Mailing Recipient Object
	 * @return 	string						Email Token for verify and tracking
	 */
	public function generateMailingToken( $mailingRecipientObject )
	{
		//we must have valid mailing recipient object
		if (!is_object($mailingRecipientObject) || !$mailingRecipientObject->mailingid || !$mailingRecipientObject->email )
		{
			return false;
		}
		
		//include joomla simple crypt
		jimport('joomla.utilities.simplecrypt');
		
		//instantiate simple crypt object
		$crypt = new JSimpleCrypt();
		
		//encrypt campaign id and current timestamp
		$token = $crypt->encrypt( $mailingRecipientObject->mailingid . ':' . $mailingRecipientObject->email);
		
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
	public static function generateConfirmationToken( $emailAddress, $mailinglistObject )
	{
		//we must have valid email address and mailing list object
		if ($emailAddress == '' || !is_object($mailinglistObject))
		{
			return false;
		}
		
		//include joomla simple crypt
		jimport('joomla.utilities.simplecrypt');
		
		//instantiate simple crypt object
		$crypt = new JSimpleCrypt();
		
		//encrypt campaign id and current timestamp
		$token = $crypt->encrypt( $mailinglistObject->id . ':' . $emailAddress );
		
		//url encode and base64 encode token
		return urlencode(base64_encode($token));
	}
	
	
	/**
	 * Parse Mailing Token - For Open Tracker, Click Tracker, & Unsubscribe Link
	 *
	 * @param 	$mailingToken	String Mailing token
	 * @return 	object			Mailing Recipient Object
	 */
	public function parseMailingToken( $mailingToken )
	{
		//we must have a token
		if (!$mailingToken || $mailingToken == '')
		{
			return false;
		}
		
		//include joomla simple crypt
		jimport('joomla.utilities.simplecrypt');
		
		//instantiate simple crypt object
		$crypt = new JSimpleCrypt();
		
		//url decode token
		$mailingToken = urldecode( $mailingToken );
		
		//base64 decode token
		$mailingToken = base64_decode( $mailingToken );
		
		//decrypt token
		$mailingToken = $crypt->decrypt( $mailingToken );
		
		//split token
		$mailingTokenParts = explode(':', $mailingToken);
		
		//get the mailing id and email from parts
		$mailingId 	= (isset($mailingTokenParts[0])) ? $mailingTokenParts[0] : '';
		$email 		= (isset($mailingTokenParts[1])) ? $mailingTokenParts[1] : '';
		
		//make sure we have a mailing id and email
		if ($mailingId == '' || $email == '')
		{
			return false;
		}
		
		//instantiate database
		$database =& JFactory::getDBO();
		
		//try to load mailing recipient object to validate
		$sql = "SELECT * FROM #__newsletter_mailing_recipients 
				WHERE mid=" . $this->database->quote( $mailingId ) . "
				AND email=" . $this->database->quote( $email );
		$database->setQuery( $sql );
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
	public static function parseConfirmationToken( $confirmationToken )
	{
		//make sure we have a token
		if (!$confirmationToken || $confirmationToken == '')
		{
			return false;
		}
		
		//include joomla simple crypt
		jimport('joomla.utilities.simplecrypt');
		
		//instantiate simple crypt object
		$crypt = new JSimpleCrypt();
		
		//url decode token
		$confirmationToken = urldecode( $confirmationToken );
		
		//base64 decode token
		$confirmationToken = base64_decode( $confirmationToken );
		
		//descrypt token
		$confirmationToken = $crypt->decrypt( $confirmationToken );
		
		//parse token
		$confirmationTokenParts = array_map( "trim", explode( ':', $confirmationToken ) );
		
		//get mailing list id & email
		$mailinglistId 	= (isset($confirmationTokenParts[0])) ? $confirmationTokenParts[0] : '';
		$email 			= (isset($confirmationTokenParts[1])) ? $confirmationTokenParts[1] : '';
		
		//make sure we have a mailing list and email
		if ($mailinglistId == '' || $email == '')
		{
			return false;
		}
		
		//instantiate database
		$database =& JFactory::getDBO();
		
		//attempt to load mailing list email object
		$sql = "SELECT * FROM #__newsletter_mailinglist_emails AS mle
				WHERE mle.mid=" . $database->quote( $mailinglistId ) . "
				AND mle.email=" . $database->quote( $email );
		$database->setQuery( $sql );
		$mailinglistEmail = $database->loadObject();
		
		return $mailinglistEmail;
	}
	
	
	/**
	 * Creates empty GIF image
	 *
	 * @return 	void
	 */
	public function mailingOpenTrackerGif()
	{
		//create fake image to output
		$im = imagecreatetruecolor(1, 1);
		$red = imagecolorallocate($im, 255, 0, 0);
		imagecolortransparent($im, $red);
		imagefilledrectangle($im, 0, 0, 99, 99, 0xFF0000);
		header('Content-Type: image/gif');
		imagegif($im);
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
	public static function sendMailinglistConfirmationEmail( $emailAddress, $mailinglistObject, $addedByAdmin = true )
	{
		//get site config
		$hubConfig = JFactory::getConfig();
		
		//setup from and replyto
		$from = '"'.$hubConfig->getValue('sitename').' Mailing Lists" <hubmail-mailinglists@'.$_SERVER['HTTP_HOST'].'>';
		$replyto = '"DO NOT REPLY" <do-not-reply@'.$_SERVER['HTTP_HOST'].'>';
		
		//set mail headers
		$headers  = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type: text/plain; charset=iso-8859-1" . "\r\n";
		$headers .= "From: {$from}" . "\r\n";
		$headers .= "Reply-To: {$replyto}" . "\r\n";
		
		//set mail priority
		$headers .= "X-Priority: 3" . "\r\n";
		$headers .= "X-MSMail-Priority: Normal" . "\r\n";
		$headers .= "Importance: Normal\n";
		
		//set extra headers
		$headers .= "X-Mailer: PHP/" . phpversion()  . "\r\n";
		$headers .= "X-Component: com_newsletter" . "\r\n";
		$headers .= "X-Component-Object: Mailinglist" . "\r\n";
		$headers .= "X-Component-ObjectId: " . $mailinglistObject->id . "\r\n";
		
		//set extra args
		$args = '-f hubmail-bounces@' . $_SERVER['HTTP_HOST'];
		
		//build subject
		$subject = "Confirm Email Subscription to '" . $mailinglistObject->name . "' on " . $hubConfig->getValue('sitename');
		
		//get token
		$token = Hubzero_Newsletter_Helper::generateConfirmationToken( $emailAddress, $mailinglistObject );
		
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
		$body .= 'https://' . $_SERVER['HTTP_HOST'] . '/newsletter/confirm?e=' . $emailAddress . '&t=' . $token . PHP_EOL . PHP_EOL;
		$body .= "------------------------------------------------------------------------" . PHP_EOL . PHP_EOL;
		$body .= "Click this link to REMOVE this email from the mailing list:" . PHP_EOL;
		$body .= 'https://' . $_SERVER['HTTP_HOST'] . '/newsletter/remove?e=' . $emailAddress . '&t=' . $token . PHP_EOL . PHP_EOL;
		$body .= "========================================================================";
		
		//send email
		return mail($emailAddress, $subject, $body, $headers, $args);
	}
	
	
	/**
	 * Helper Function to add all tracking methods to email message
	 *
	 * @param    $emailMessage    Email Body
	 * @param    $emailToken      Email Token to track per message
	 * @return   String
	 */
	public static function addTrackingToEmailMessage( $emailMessage, $emailToken )
	{
		$emailMessage = self::addclickTrackingtoEmailMessage( $emailMessage, $emailToken );
		$emailMessage = self::addOpenTrackingToEmailMessage( $emailMessage, $emailToken );
		$emailMessage = self::addPrintTrackingToEmailMessage( $emailMessage, $emailToken );
		$emailMessage = self::addForwardingToEmailMessage( $emailMessage, $emailToken );
		return $emailMessage;
	}
	
	
	/**
	 * Add Click Tracking to Email Message
	 *
	 * @param    $emailMessage    Email Body
	 * @param    $emailToken      Email Token to track per message
	 * @return   String
	 */
	public static function addclickTrackingtoEmailMessage( $emailMessage, $emailToken )
	{
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
					$clickTracker = 'https://' . $_SERVER['SERVER_NAME'] . DS . 'newsletter' . DS . 'track' . DS . 'click' . DS . '?t=' . $emailToken . '&l=' . urlencode($url);

					//replace normal links with tracking links
					$emailMessage = str_replace( $url, $clickTracker, $emailMessage);
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
	public static function addOpenTrackingToEmailMessage( $emailMessage, $emailToken )
	{
		//create open tracker img
		$openTracker = '<img src="https://' . $_SERVER['SERVER_NAME'] . '/newsletter/track/open?t='.$emailToken.'" width="1" height="1" />';
		
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
	public static function addPrintTrackingToEmailMessage( $emailMessage, $emailToken )
	{
		//create print tracker
		$printTracker = "<style>
							@media print {
								#_print {
									width:1px;
									height:1px;
									border:none;
									background-color: transparent;
									background-image: url('https://" . $_SERVER['SERVER_NAME'] . "/newsletter/track/print?t=" . $emailToken . "');
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
	public function addForwardingToEmailMessage( $emailMessage, $emailToken )
	{
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
								background-image: url('https://" . $_SERVER['SERVER_NAME'] . "/newsletter/track/forward?t=" . $emailToken . "');
							}
						</style>";
		$forwardTracker .= "<div id=\"_forward\"></div>";
		
		//add to the end of the message body
		$emailMessage = str_replace('</body>', $forwardTracker . '</body>', $emailMessage);
		
		//return message body
		return $emailMessage;
	}
}