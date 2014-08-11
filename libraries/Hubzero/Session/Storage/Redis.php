<?php 
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Session\Storage;
use Hubzero\Redis\Database;
use Hubzero\Session\StorageInterface;

class Redis implements StorageInterface
{
	/**
	 * Get Connection to Redis Client
	 * 
	 * @param  string $name name of client
	 * @return mixed
	 */
	private static function getDBO($name = 'default')
	{
		return Database::connect($name);
	}

	/**
	 * Get Hash key prefix
	 * 
	 * @return  string  key prefix
	 */
	private static function getPrefix()
	{
		// get site config
		$config = \JFactory::getConfig();

		// get redis key prefixes
		$prefixes = $config->get('redis_key_prefix', array());

		// return prefix
		return isset($prefixes['session']) ? $prefixes['session'] : 'session:';
	}

	/**
	 * Get single session data
	 * 
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public static function session($id)
	{
		// get database
		$database = self::getDBO();

		// get all key => values for hash
		return (object) $database->hgetall($id);
	}

	/**
	 * Get single session data (with Userid)
	 * 
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public static function sessionWithUserid($userid)
	{
		// get list of all sessions
		$sessions = self::allSessions(array(
			'guest' => 0,
			'distinct' => 1
		));

		// see if any session matches our userid
		foreach ($sessions as $session)
		{
			if ($session->userid == $userid)
			{
				return $session;
			}
		}

		// nothing found
		return null;
	}

	/**
	 * Get list of all sessions
	 * 
	 * @return [type]          [description]
	 */
	public static function allSessions($filters = array())
	{
		// get database
		$database = self::getDBO();

		// get prefix
		$prefix = self::getPrefix();

		// load all session keys
		$result   = $database->scan(0, array('MATCH' => $prefix . '*'));
		$cursor   = $result[0];
		$sessions = $result[1];

		// var to hold distinct sessions
		$distinct = array();
		
		// loop through all session keys and get data
		foreach ($sessions as $k => $v)
		{
			// get session data for key
			$sessions[$k] = self::session($v);
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
}