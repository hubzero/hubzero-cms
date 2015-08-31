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

namespace Hubzero\Base;

/**
 * Manager for application client types
 */
class ClientManager
{
	/**
	 * Client information array
	 *
	 * @var  array
	 */
	protected static $_clients = null;

	/**
	 * Gets information on a specific client id.  This method will be useful in
	 * future versions when we start mapping applications in the database.
	 *
	 * This method will return a client information array if called
	 * with no arguments which can be used to add custom application information.
	 *
	 * @param   integer  $id      A client identifier
	 * @param   boolean  $byName  If True, find the client by its name
	 * @return  mixed    Object describing the client or false if not known
	 */
	public static function client($id = null, $byName = false)
	{
		// Only create the array if it does not exist
		if (self::$_clients === null)
		{
			self::$_clients = array();

			$dirIterator = new \DirectoryIterator(__DIR__ . DS . 'Client');
			foreach ($dirIterator as $file)
			{
				if ($file->isDot() || $file->isDir())
				{
					continue;
				}

				$client = preg_replace('#\.[^.]*$#', '', $file->getFilename());
				if ($client == 'ClientInterface')
				{
					continue;
				}

				$cls = __NAMESPACE__ . '\\Client\\' . ucfirst(strtolower($client));
				if (!class_exists($cls))
				{
					throw new \InvalidArgumentException(sprintf('Invalid client type of "%s".', $client));
				}

				$obj = new $cls;

				self::$_clients[$obj->id] = $obj;
			}
			ksort(self::$_clients);
		}

		// If no client id has been passed return the whole array
		if (is_null($id))
		{
			return self::$_clients;
		}

		// Are we looking for client information by id or by name?
		if (!$byName)
		{
			if (isset(self::$_clients[$id]))
			{
				return self::$_clients[$id];
			}
		}
		else
		{
			foreach (self::$_clients as $client)
			{
				if ($client->name == strtolower($id))
				{
					return $client;
				}
			}
		}

		return null;
	}

	/**
	 * Modify information on a client.
	 *
	 * @param   mixed    $client    A client identifier either an array or object
	 * @param   string   $property  Property to set
	 * @param   mixed    $value     Value to set
	 * @return  void
	 */
	public static function modify($client, $property, $value)
	{
		self::client($client)->$property = $value;
	}

	/**
	 * Adds information for a client.
	 *
	 * @param   mixed    $client  A client identifier either an array or object
	 * @return  boolean  True if the information is added. False on error
	 */
	public static function append($client)
	{
		if (is_array($client))
		{
			$client = (object) $client;
		}

		if (!is_object($client))
		{
			return false;
		}

		$info = self::client();

		if (!isset($client->id))
		{
			$client->id = count($info);
		}

		self::$_clients[$client->id] = clone $client;

		return true;
	}
}
