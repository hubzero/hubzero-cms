<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Facades;

/**
 * Log facade
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
