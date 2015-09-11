<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Session\Storage;

use Hubzero\Redis\Database;

/**
 * Redis Session Storage class
 */
class Redis extends Store
{
	/**
	 * Format for hash keys
	 *
	 * @var  string
	 */
	private $prefix  = 'session:';

	/**
	 * Redis database connection
	 *
	 * @var  object
	 */
	private $database = null;

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
			throw new Exception(\Lang::txt('JLIB_SESSION_REDIS_EXTENSION_NOT_AVAILABLE'));
		}

		if (!array_key_exists('redis_key_prefix', $options))
		{
			$options['redis_key_prefix'] = array();
		}

		$prefixes = $options['redis_key_prefix'];

		if (isset($prefixes['session']))
		{
			$this->prefix = $prefixes['session'];
		}

		parent::__construct($options);
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
		$this->database = Database::connect('default');
		$this->database->connect();
	}

	/**
	 * Close the SessionHandler backend.
	 *
	 * @return  boolean  True on success, false otherwise.
	 */
	public function close()
	{
		$this->database->disconnect();
	}

	/**
	 * Read session hash for Id
	 * 
	 * @param   string  $session_id  Session Id
	 * @return  mixed   Session Data
	 */
	public function read($session_id)
	{
		// get session hash
		$session = $this->database->hgetall($this->key($session_id));

		// return session data
		return (isset($session['data'])) ? $session['data'] : null;
	}

	/**
	 * Write session data to the SessionHandler backend.
	 *
	 * @param   string   $id    The session identifier.
	 * @param   string   $data  The session data.
	 * @return  boolean  True on success, false otherwise.
	 */
	public function write($session_id, $session_data)
	{
		$data = array(
			'session_id' => $session_id,
			'client_id'  => \App::get('client')->id,
			'guest'      => \User::isGuest(),
			'time'       => time(),
			'data'       => $session_data,
			'userid'     => \User::get('id'),
			'username'   => \User::get('username'),
			'usertype'   => null,
			'ip'         => $_SERVER['REMOTE_ADDR']
		);

		$saved = $this->database->hmset($this->key($session_id), $data);

		return $saved;
	}

	/**
	 * Delete session hash
	 * 
	 * @param  string  $session_id  Session Id 
	 * @return boolean              Destroyed or not
	 */
	public function destroy($session_id)
	{
		if (!$this->database->del($this->key($session_id)))
		{
			return false;
		}
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
		error_log('redis gc');
	}

	/**
	 * Get single session data as an object
	 * 
	 * @param   integer  $session_id  Session Id 
	 * @return  object
	 */
	public function session($session_id)
	{
		return (object) $this->database->hgetall($session_id);
	}

	/**
	 * Get list of all sessions
	 * 
	 * @param   array  $filters
	 * @return  array
	 */
	public function all($filters = array())
	{
		// load all session keys
		$result   = $this->database->scan(0, array('MATCH' => $this->prefix . '*'));
		$cursor   = $result[0];
		$sessions = $result[1];

		// var to hold distinct sessions
		$distinct = array();

		// loop through all session keys and get data
		foreach ($sessions as $k => $v)
		{
			// get session data for key
			$sessions[$k] = $this->database->hgetall($v);
			$userid       = $sessions[$k]->userid;
			$guest        = $sessions[$k]->guest;
			$client       = $sessions[$k]->client_id;

			// guest filter
			if (isset($filters['guest']) && $filters['guest'] != $guest)
			{
				unset($sessions[$k]);
				continue;
			}

			// client filter
			if (isset($filters['client']))
			{
				// make sure is array
				if (!is_array($filters['client']))
				{
					$filters['client'] = array($filters['client']);
				}
				// check to make sure client is in what we want
				if (!in_array($client, $filters['client']))
				{
					unset($sessions[$k]);
					continue;
				}
			}

			// distinct filter
			if (isset($filters['distinct']) && $filters['distinct'] == 1)
			{
				if (isset($distinct[$client]) && in_array($userid, array_keys($distinct[$client])))
				{
					$key         = $distinct[$client][$userid]->key;
					$beforeTime  = $distinct[$client][$userid]->time;
					$currentTime = $sessions[$k]->time;

					// is this sessions time greater then the 
					// previous one saved for this user for this client?
					if ($currentTime < $beforeTime)
					{
						$key = $k;
					}

					unset($sessions[$key]);
					continue;
				}
				else
				{
					$sessions[$k]->key = $k;
					$distinct[$client][$userid] = $sessions[$k];
				}
			}
		}

		// return array of session objects
		return array_values(array_filter($sessions));
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
	 * Test to see if Predis Library exists
	 * 
	 * @return  boolean 
	 */
	public static function isAvailable()
	{
		return new Database != null;
	}
}