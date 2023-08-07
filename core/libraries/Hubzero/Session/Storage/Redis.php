<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Session\Storage;

use Hubzero\Session\Store;
use Hubzero\Redis\Database as RedisDatabase;
use Exception;

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
	private $prefix = 'session:';

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
	#[\ReturnTypeWillChange]
	public function open($save_path, $name)
	{
		$this->database = RedisDatabase::connect('default');
		$this->database->connect();
	}

	/**
	 * Close the SessionHandler backend.
	 *
	 * @return  boolean  True on success, false otherwise.
	 */
	#[\ReturnTypeWillChange]
	public function close()
	{
		$this->database->disconnect();
	}

	/**
	 * Read session hash for Id
	 *
	 * @param   string  $id  Session Id
	 * @return  mixed   Session Data
	 */
	#[\ReturnTypeWillChange]
	public function read($id)
	{
		// get session hash
		$session = $this->database->hgetall($this->key($id));

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
	#[\ReturnTypeWillChange]
	public function write($id, $data)
	{
		$data = array(
			'session_id' => $id,
			'client_id'  => \App::get('client')->id,
			'guest'      => \User::isGuest(),
			'time'       => time(),
			'data'       => $data,
			'userid'     => \User::get('id'),
			'username'   => \User::get('username'),
			'usertype'   => null,
			'ip'         => $_SERVER['REMOTE_ADDR']
		);

		$saved = $this->database->hmset($this->key($id), $data);

		return $saved;
	}

	/**
	 * Delete session hash
	 *
	 * @param  string  $id  Session Id
	 * @return boolean              Destroyed or not
	 */
	#[\ReturnTypeWillChange]
	public function destroy($id)
	{
		if (!$this->database->del($this->key($id)))
		{
			return false;
		}
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
		//'redis gc';
	}

	/**
	 * Get single session data as an object
	 *
	 * @param   int  $id  Session Id
	 * @return  object
	 */
	public function session($id)
	{
		return (object) $this->database->hgetall($id);
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
		return new RedisDatabase != null;
	}
}
