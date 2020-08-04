<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Facades;

/**
 * Log facade
 *
 * @codeCoverageIgnore
 */
class Log extends Facade
{
	/**
	 * Get the registered name.
	 *
	 * @return  string
	 */
	protected static function getAccessor()
	{
		return 'log';
	}

	/**
	 * Log an entry to the auth logger
	 *
	 * @param   string   $message
	 * @return  boolean
	 */
	public static function auth($message)
	{
		$instance = static::getRoot();

		if ($instance->has('auth'))
		{
			$logger = $instance->logger('auth');
		}
		else
		{
			$logger = $instance->logger();
		}

		return $logger->info($message);
	}

	/**
	 * Log an entry to the spam logger
	 *
	 * @param   string   $message
	 * @return  boolean
	 */
	public static function spam($message)
	{
		$instance = static::getRoot();

		if ($instance->has('spam'))
		{
			$logger = $instance->logger('spam');
		}
		else
		{
			$logger = $instance->logger();
		}

		return $logger->info($message);
	}
}
