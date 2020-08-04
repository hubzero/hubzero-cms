<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Notification\Storage;

use Hubzero\Notification\MessageStore;
use Hubzero\Utility\Cookie as Monster;

/**
 * Cookie storage handler.
 */
class Cookie implements MessageStore
{
	/**
	 * Lifetime of the cookie (in minutes)
	 *
	 * @var  integer
	 */
	private $lifetime;

	/**
	 * Constructor
	 *
	 * @param   integer  lifetime
	 * @return  void
	 */
	public function __construct($lifetime)
	{
		$this->lifetime = $lifetime;
	}

	/**
	 * Store a message
	 *
	 * @param   array   $data
	 * @param   string  $domain
	 * @return  void
	 */
	public function store($data, $domain)
	{
		$messages   = (array) $this->retrieve($domain);
		$messages[] = $data;

		Monster::bake($this->key($domain), $this->expires($this->lifetime), $messages);
	}

	/**
	 * Return a list of messages
	 *
	 * @param   array   $data
	 * @param   string  $domain
	 * @return  array
	 */
	public function retrieve($domain)
	{
		if (!($messages = Monster::eat($this->key($domain))))
		{
			$messages = array();
		}

		if (count($messages))
		{
			$this->clear($domain);
		}

		return $messages;
	}

	/**
	 * Clear all messages
	 *
	 * @param   string  $domain
	 * @return  void
	 */
	public function clear($domain)
	{
		Monster::bake($this->key($domain), $this->expires(0), array());
	}

	/**
	 * Return a count of messages
	 *
	 * @param   string  $domain
	 * @return  integer
	 */
	public function total($domain)
	{
		return count($this->retrieve($domain));
	}

	/**
	 * Get the storage key
	 *
	 * @param   string  $domain
	 * @return  string
	 */
	private function key($domain)
	{
		$domain = (!$domain ? '' : $domain . '.');

		return md5($domain . 'application.queue');
	}

	/**
	 * Get the expiration time a # of minutes from now
	 *
	 * @param   itneger  $minutes
	 * @return  integer
	 */
	private function expires($minutes)
	{
		return time() + 60 * $minutes;
	}
}
