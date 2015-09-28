<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   framework
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
