<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * XMessage plugin class for internal messages
 */
class plgXMessageInternal extends \Hubzero\Plugin\Plugin
{
	/**
	 * Return this messaging type
	 *
	 * @return     string
	 */
	public function onMessageMethods()
	{
		return 'internal';
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
		if ($this->onMessageMethods() != $action)
		{
			return true;
		}

		return true;
	}
}
