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
 * WinCache session storage handler
 *
 * Inspired by Joomla's JSessionStorageWincache class
 */
class WinCache extends Store
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
			throw new Exception(\Lang::txt('JLIB_SESSION_WINCACHE_EXTENSION_NOT_AVAILABLE'));
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
		return (string) wincache_ucache_get($this->key($id));
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
		return wincache_ucache_set($this->key($id), $data, ini_get("session.gc_maxlifetime"));
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
		return wincache_ucache_delete($this->key($id));
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
		return (extension_loaded('wincache') && function_exists('wincache_ucache_get') && !strcmp(ini_get('wincache.ucenabled'), "1"));
	}
}
