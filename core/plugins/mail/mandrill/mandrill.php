<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Plugin class for providing Mandrill mailing functionality
 */
class plgMailMandrill extends \Hubzero\Plugin\Plugin
{
	/**
	 * Instantiates and registers a mail transport mechanism on the message
	 * 
	 * @return  void
	 */
	public function onMailersRegister()
	{
		$transporter = new \Hubzero\Mail\Transport\Mandrill($this->params->get('api_key'));

		\Hubzero\Mail\Message::addTransporter('mandrill', $transporter);
	}
}
