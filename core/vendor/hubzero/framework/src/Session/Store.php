<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   framework
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @copyright Copyright 2005-2014 Open Source Matters, Inc.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 */

namespace Hubzero\Session;

use Hubzero\Base\Object;
use SessionHandlerInterface;

/**
 * Custom session storage handler for PHP
 *
 * Inspired by Joomla's JSessionStorage class
 */
abstract class Store extends Object implements SessionHandlerInterface
{
	/**
	 * Storage instances container.
	 *
	 * @var  array
	 */
	protected static $instances = array();

	/**
	 * Constructor
	 *
	 * @param   array  $options  Optional parameters.
	 * @return  void
	 */
	public function __construct($options = array())
	{
		$this->register($options);
	}

	/**
	 * Returns a session storage handler object, only creating it if it doesn't already exist.
	 *
	 * @param   string  $name     The session store to instantiate
	 * @param   array   $options  Array of options
	 * @return  object
	 */
	public static function getInstance($name = 'none', $options = array())
	{
		$name = strtolower(preg_replace('/[^A-Z_]/i', '', (string) $name));

		if (empty(self::$instances[$name]))
		{
			$class = __NAMESPACE__ . '\\Storage\\' . ucfirst($name);

			if (!class_exists($class))
			{
				// No attempt to die gracefully here, as it tries to close the non-existing session
				exit('Unable to load session storage class: ' . $name);
			}

			self::$instances[$name] = new $class($options);
		}

		return self::$instances[$name];
	}

	/**
	 * Register the functions of this class with PHP's session handler
	 *
	 * @param   array  $options
	 * @return  void
	 */
	public function register($options = array())
	{
		// Use this object as the session handler
		session_set_save_handler(
			array($this, 'open'),
			array($this, 'close'),
			array($this, 'read'),
			array($this, 'write'),
			array($this, 'destroy'),
			array($this, 'gc')
		);
	}

	/**
	 * Open the SessionHandler backend.
	 *
	 * @param   string   $save_path  The path to the session object.
	 * @param   string   $name       The name of the session.
	 * @return  boolean  True on success, false otherwise.
	 */
	public function open($save_path, $name)
	{
		return true;
	}

	/**
	 * Close the SessionHandler backend.
	 *
	 * @return  boolean  True on success, false otherwise.
	 */
	public function close()
	{
		return true;
	}

	/**
	 * Read the data for a particular session identifier from the
	 * SessionHandler backend.
	 *
	 * @param   string  $id  The session identifier.
	 * @return  string  The session data.
	 */
	public function read($session_id)
	{
		return;
	}

	/**
	 * Write session data to the SessionHandler backend.
	 *
	 * @param   string   $session_id    The session identifier.
	 * @param   string   $session_data  The session data.
	 * @return  boolean  True on success, false otherwise.
	 */
	public function write($session_id, $session_data)
	{
		return true;
	}

	/**
	 * Destroy the data for a particular session identifier in the
	 * SessionHandler backend.
	 *
	 * @param   string   $id  The session identifier.
	 * @return  boolean  True on success, false otherwise.
	 */
	public function destroy($session_id)
	{
		return true;
	}

	/**
	 * Garbage collect stale sessions from the SessionHandler backend.
	 *
	 * @param   integer  $maxlifetime  The maximum age of a session.
	 * @return  boolean  True on success, false otherwise.
	 */
	public function gc($maxlifetime = null)
	{
		return true;
	}

	/**
	 * Get single session data as an object
	 * 
	 * @param   integer  $session_id  Session Id 
	 * @return  object
	 */
	public function session($session_id)
	{
		$session = new Object;
		$session->id = $session_id;

		return $session;
	}

	/**
	 * Get list of all sessions
	 * 
	 * @param   array  $filters
	 * @return  array
	 */
	public function all($filters = array())
	{
		return array();
	}

	/**
	 * Test to see if the SessionHandler is available.
	 *
	 * @return  boolean  True on success, false otherwise.
	 */
	public static function isAvailable()
	{
		return true;
	}
}
