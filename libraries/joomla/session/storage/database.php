<?php
/**
* @version		$Id:database.php 6961 2007-03-15 16:06:53Z tcp $
* @package		Joomla.Framework
* @subpackage	Session
* @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

/**
* Database session storage handler for PHP
*
* @package		Joomla.Framework
* @subpackage	Session
* @since		1.5
* @see http://www.php.net/manual/en/function.session-set-save-handler.php
*/
class JSessionStorageDatabase extends JSessionStorage
{
	var $_data = null;

	/**
	 * Open the SessionHandler backend.
	 *
	 * @access public
	 * @param string $save_path     The path to the session object.
	 * @param string $session_name  The name of the session.
	 * @return boolean  True on success, false otherwise.
	 */
	function open($save_path, $session_name)
	{
		return true;
	}

	/**
	 * Close the SessionHandler backend.
	 *
	 * @access public
	 * @return boolean  True on success, false otherwise.
	 */
	function close()
	{
		return true;
	}

 	/**
 	 * Read the data for a particular session identifier from the
 	 * SessionHandler backend.
 	 *
 	 * @access public
 	 * @param string $id  The session identifier.
 	 * @return string  The session data.
 	 */
	function read($id)
	{
		$db =& JFactory::getDBO();
		if(!$db->connected()) {
			return false;
		}

		$session = & JTable::getInstance('session');
		$session->load($id);
		return (string)$session->data;
	}

	/**
	 * Write session data to the SessionHandler backend.
	 *
	 * @access public
	 * @param string $id            The session identifier.
	 * @param string $session_data  The session data.
	 * @return boolean  True on success, false otherwise.
	 */
	function write($id, $session_data)
	{
		if (JFactory::getApplication()->getClientId() == 4)
		{
			return true; // skip session write on api calls
		}
		
		$db =& JFactory::getDBO();
		if(!$db->connected()) {
			return false;
		}

		$session = & JTable::getInstance('session');

		// START: HUBzero Session Optimization when using database handler
		// All session updates/inserts have been deferred to this handler so we
		// need to pull extra data for the table out of the session object
		/*
		if ($session->load($id)) {
			$session->data = $session_data;
			$session->store();
		} else {
			// if load failed then we assume that it is because
			// the session doesn't exist in the database
			// therefore we use insert instead of store
			$app = &JFactory::getApplication();
			$session->data = $session_data;
			$session->insert($id, $app->getClientId());
		}
		*/

		$client_id = isset($_SESSION['__default']['session.client_id']) ? (int)$_SESSION['__default']['session.client_id'] : 'NULL';

		$session->username   = null; // set to null so we don't overwrite
		$session->session_id = $id;
		$session->guest      = null; // set to null so we don't overwrite
		$session->userid     = null; // set to null so we don't overwrite
		$session->usertype   = null; // set to null so we don't overwrite
		$session->gid        = null; // set to null so we don't overwrite
		$session->client_id  = null; // set to null so we don't overwrite
		$session->data       = $session_data;
		
		if (!empty($_SESSION['__default']['user']))
		{
			$user = $_SESSION['__default']['user'];
			
			$session->username   = $user->username;
			$session->guest      = $user->guest;
			$session->userid     = (int) $user->id;
			$session->usertype   = $user->usertype;
			$session->gid        = $user->gid;
		}
		
		if (!$session->update())
		{
			$session->insert($id, $client_id);
		
			$db = JFactory::getDBO();
			
			$ip   = (!empty($_SERVER['REMOTE_ADDR'])) ? $db->Quote($_SERVER['REMOTE_ADDR']) : 'NULL';

			$psid = 'NULL';
			
			if (!empty($_SESSION['__default']['tracker.psid'])) 
			{
				if ($_SESSION['__default']['tracker.psid'] != $id)
				{
					$psid = $db->Quote($_SESSION['__default']['tracker.psid']);					
				}
			}
			
			$source = (!empty($_SESSION['__default']['session.source'])) ? $db->Quote($_SESSION['__default']['session.source']) : 'NULL';
			
			if (empty($user->id))
			{
				if (!empty($_SESSION['__default']['tracker.user_id']))
				{
					$user_id = $_SESSION['__default']['tracker.user_id'];
					$source = $db->Quote('tracking'); 
				}
				else
				{
					$user_id = null;
				}
			}
			else
			{
				$user_id = (int) $user->id;
			}
			
			$rsid = (!empty($_SESSION['__default']['tracker.rsid'])) ? $db->Quote($_SESSION['__default']['tracker.rsid']) : 'NULL';
			$ssid = (!empty($_SESSION['__default']['tracker.ssid'])) ? $db->Quote($_SESSION['__default']['tracker.ssid']) : 'NULL';
			$user_id = (!empty($user_id)) ? (int) $user_id : 'NULL';

			$authenticator = (!empty($_SESSION['__default']['session.authenticator'])) ? $db->Quote($_SESSION['__default']['session.authenticator']) : 'NULL';
						
			$db->setQuery("INSERT INTO #__session_log (clientid, session_id, psid, rsid, ssid, user_id, authenticator, source, ip, created) VALUES (" .
							$client_id . ", " .
							$db->Quote($id) . ", " .
							$psid . ", " .
							$rsid . ", " .
							$ssid . ", " .
							$user_id . ", " .
							$authenticator . ", " .
							$source . ", " .
							$ip . ", " .
							"NOW());");
			
			$db->query();
		}
		// END: HUBzero Session Optimization when using database handler

		return true;
	}

	/**
	  * Destroy the data for a particular session identifier in the
	  * SessionHandler backend.
	  *
	  * @access public
	  * @param string $id  The session identifier.
	  * @return boolean  True on success, false otherwise.
	  */
	function destroy($id)
	{
		$db =& JFactory::getDBO();
		if(!$db->connected()) {
			return false;
		}

		$session = & JTable::getInstance('session');
		$session->delete($id);
		return true;
	}

	/**
	 * Garbage collect stale sessions from the SessionHandler backend.
	 *
	 * @access public
	 * @param integer $maxlifetime  The maximum age of a session.
	 * @return boolean  True on success, false otherwise.
	 */
	function gc($maxlifetime)
	{
		$db =& JFactory::getDBO();
		if(!$db->connected()) {
			return false;
		}

		$session = & JTable::getInstance('session');
		$session->purge($maxlifetime);
		return true;
	}
}
