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
	 * @param   string  $session_id  The session identifier.
	 * @return  string  The session data.
	 */
	public function read($session_id)
	{
		return (string) eaccelerator_get($this->key($session_id));
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
		return eaccelerator_put($this->key($session_id), $session_data, ini_get("session.gc_maxlifetime"));
	}

	/**
	 * Destroy the data for a particular session identifier in the SessionHandler backend.
	 *
	 * @param   string   $id  The session identifier.
	 * @return  boolean  True on success, false otherwise.
	 */
	public function destroy($session_id)
	{
		return eaccelerator_rm($this->key($session_id));
	}

	/**
	 * Garbage collect stale sessions from the SessionHandler backend.
	 *
	 * @param   integer  $maxlifetime  The maximum age of a session.
	 * @return  boolean  True on success, false otherwise.
	 */
	public function gc($maxlifetime = null)
	{
		eaccelerator_gc();
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
		$session->session_id = $session_id;
		$session->data       = $this->read($session_id);

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
