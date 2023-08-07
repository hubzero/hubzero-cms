<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Session;

use Hubzero\Base\Obj;
use SessionHandlerInterface;

/**
 * Custom session storage handler for PHP
 *
 * Inspired by Joomla's JSessionStorage class
 */
abstract class Store extends Obj implements SessionHandlerInterface
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
	 * @param   string   $path  The path to the session object.
	 * @param   string   $name       The name of the session.
	 * @return  boolean  True on success, false otherwise.
	 */

	#[\ReturnTypeWillChange]
	public function open($path, $name)
	{
		return true;
	}

	/**
	 * Close the SessionHandler backend.
	 *
	 * @return  boolean  True on success, false otherwise.
	 */

	#[\ReturnTypeWillChange]
	public function close()
	{
		return true;
	}

	/**
	 * Read the data for a particular session identifier from the
	 * SessionHandler backend.
	 *
	 * @param   string|false  $id  The session identifier.
	 * @return  string  The session data.
	 */

	#[\ReturnTypeWillChange]
	public function read($id)
	{
		return false;
	}

	/**
	 * Write session data to the SessionHandler backend.
	 *
	 * @param   string   $id    The session identifier.
	 * @param   string   $ata  The session data.
	 * @return  boolean  True on success, false otherwise.
	 */

	#[\ReturnTypeWillChange]
	public function write($d, $data)
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

	#[\ReturnTypeWillChange]
	public function destroy($id)
	{
		return true;
	}

	/**
	 * Garbage collect stale sessions from the SessionHandler backend.
	 *
	 * @param   int  $maxlifetime  The maximum age of a session.
	 * @return  boolean  True on success, false otherwise.
	 */

	#[\ReturnTypeWillChange]
	public function gc($maxlifetime = null)
	{
		return true;
	}

	/**
	 * Get single session data as an object
	 *
	 * @param   int  $id  Session Id
	 * @return  object
	 */
	public function session($id)
	{
		$session = new Obj;
		$session->id = $id;

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
