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

defined('JPATH_PLATFORM') or die;

use Hubzero\Redis\Database;

/**
 * Redis Session Storage class
 *
 * @package    Hubzero.CMS
 * @subpackage Session
 * @since      1.3.0
 */
class JSessionStorageRedis extends JSessionStorage
{
	/**
	 * Format for hash keys
	 * @var string
	 */
	private $prefix  = '';

	/**
	 * Redis database object
	 * @var [type]
	 */
	private $database = null;

	/**
	 * Overload Constructor to do additional check
	 * 
	 * @param array $options [description]
	 */
	public function __construct($options = array())
	{
		// run test
		if (!$this->test())
		{
			return JError::raiseError(404, JText::_('JLIB_SESSION_REDIS_EXTENSION_NOT_AVAILABLE'));
		}

		// get site config
		$config = JFactory::getConfig();

		// get redis key prefixes
		$prefixes = $config->get('redis_key_prefix', array());

		// set session key
		$this->prefix = isset($prefixes['session']) ? $prefixes['session'] : 'session:';
		
		// parent construct
		parent::__construct($options);
	}
	
	/**
	 * Open Connection to redis
	 * 
	 * @param  [type] $save_path    [description]
	 * @param  [type] $session_name [description]
	 * @return [type]               [description]
	 */
	public function open($save_path, $session_name)
	{
		try 
		{
			$this->database = Database::connect('default');
			$this->database->connect();
		} 
		catch (Exception $e) 
		{
			return JError::raiseError(500, $e->getMessage());
		}
	}

	/**
	 * Disconnect Redis
	 * 
	 * @return [type] [description]
	 */
	public function close()
	{
		try
		{
			$this->database->disconnect();
		}
		catch (Exception $e)
		{
			return JError::raiseError(500, $e->getMessage());
		}
	}

	/**
	 * Read session hash for Id
	 * 
	 * @param   string  $id  Session Id
	 * @return  mixed        Session Data
	 */
	public function read($id)
	{
		// get key
		$key = $this->prefix . $id;

		// get session hash
		$session = $this->database->hgetall($key);

		// return session data
		return (isset($session['data'])) ? $session['data'] : null;
	}
	
	/**
	 * Write Session Data
	 * 
	 * @param  string   $id           Session Id
	 * @param  [type] $session_data [description]
	 * @return [type]               [description]
	 */
	public function write($id, $session_data)
	{
		// make sure we should write
		if (!$this->shouldWrite())
		{
			return true;
		}

		// get user object
		$user = JFactory::getUser();
		$app  = JFactory::getApplication();

		// get key
		$key = $this->prefix . $id;

		// array to hold session data
		$sessionData = array(
			'session_id' => $id,
			'client_id'  => $app->getClientId(),
			'guest'      => $user->get('guest'),
			'time'       => time(),
			'data'       => $session_data,
			'userid'     => $user->get('id'),
			'username'   => $user->get('username'),
			'usertype'   => null,
			'ip'         => $_SERVER['REMOTE_ADDR']
		);

		// create the session hash with session data
		$saved = $this->database->hmset($key, $sessionData);

		//return save result
		return $saved;
	}

	/**
	 * Check to make sure we should write session data
	 * 
	 * @return  bool  should/not write session data
	 */
	public function shouldWrite()
	{
		return php_sapi_name() != 'cli' && JFactory::getApplication()->getClientId() != 4;
	}
	
	/**
	 * Delete session hash
	 * 
	 * @param  string  $id  Session Id 
	 * @return boolean      Destroyed or not
	 */
	public function destroy($id)
	{
		$key = $this->prefix . $id;
		if (!$this->database->del($key))
		{
			return false;
		}
		return true;
	}
	
	/**
	 * Garbage Collect Sessions
	 * 
	 * @param  [type] $maxlifetime [description]
	 * @return [type]              [description]
	 */
	public function gc($maxlifetime = null)
	{
		error_log('redis gc');	
	}
	
	/**
	 * Test to see if Predis Library exists
	 * 
	 * @return boolean 
	 */
	public static function test()
	{
		return new Database != null;
	}
}