<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Session\Storage;

use Hubzero\Session\Store;
use Exception;

/**
 * eAccelerator session storage handler
 *
 * Inspired by Joomla's JSessionStorageEAccelerator class
 */
class Eaccelerator extends Store
{
	/**
	 * Key prefix
	 *
	 * @var  string
	 */
	private $prefix  = 'sess_';

	/**
	 * Constructor
	 *
	 * @param   array  $options  Optional parameters.
	 * @return  void
	 */
	public function __construct($options = array())
	{
		if (!self::isAvailable())
		{
			throw new Exception(\Lang::txt('JLIB_SESSION_EACCELERATOR_EXTENSION_NOT_AVAILABLE'));
		}

		if (isset($options['prefix']))
		{
			$this->prefix = $options['prefix'];
		}

		parent::__construct($options);
	}

	/**
	 * Read the data for a particular session identifier from the SessionHandler backend.
	 *
	 * @param   string  $id  The session identifier.
	 * @return  string  The session data.
	 */
	#[\ReturnTypeWillChange]
	public function read($id)
	{
		return (string) eaccelerator_get($this->key($id));
	}

	/**
	 * Write session data to the SessionHandler backend.
	 *
	 * @param   string   $id    The session identifier.
	 * @param   string   $data  The session data.
	 * @return  boolean  True on success, false otherwise.
	 */
	#[\ReturnTypeWillChange]
	public function write($id, $data)
	{
		return eaccelerator_put($this->key($id), $data, ini_get("session.gc_maxlifetime"));
	}

	/**
	 * Destroy the data for a particular session identifier in the SessionHandler backend.
	 *
	 * @param   string   $id  The session identifier.
	 * @return  boolean  True on success, false otherwise.
	 */
	#[\ReturnTypeWillChange]
	public function destroy($id)
	{
		return eaccelerator_rm($this->key($id));
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
		eaccelerator_gc();
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
		$session = new Object;
		$session->session_id = $id;
		$session->data       = $this->read($id);

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
		$keys = eaccelerator_list_keys();

		$data = array();

		if (is_array($keys))
		{
			foreach ($keys as $key)
			{
				// Trim leading ":" to work around list_keys namespace bug in eAcc.
				// This will still work when bug is fixed.
				$key['name'] = ltrim($key['name'], ':');

				if (strpos($key['name'], $this->prefix) === 0)
				{
					continue;
				}

				$session = new Object;
				$session->session_id = $file->getName();
				$session->data       = (string) eaccelerator_get($key['name']);

				$data[] = $session;
			}
		}

		return $data;
	}

	/**
	 * Build the storage key
	 *
	 * @param   string  $id  The session identifier.
	 * @return  string
	 */
	protected function key($id)
	{
		return $this->prefix . $id;
	}

	/**
	 * Test to see if the SessionHandler is available.
	 *
	 * @return  boolean  True on success, false otherwise.
	 */
	public static function isAvailable()
	{
		return (extension_loaded('eaccelerator') && function_exists('eaccelerator_get'));
	}
}
