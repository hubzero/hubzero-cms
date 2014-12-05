<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2014 Purdue University. All rights reserved.
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
 * @author    Steve Snyder <snyder13@purdue.edu>
 * @copyright Copyright 2005-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('JPATH_BASE') or die();

/**
 * Hubgraph connection error
 */
class HubgraphConnectionError extends \Exception
{
}

require_once(__DIR__ . DS . 'configuration.php');
require_once(__DIR__ . DS . 'db.php');

/**
 * Hubgraph client
 */
class HubgraphClient
{
	const CHUNK_LEN = 1024;

	/**
	 * Description...
	 *
	 * @param   unknown  $method
	 * @param   unknown  $url
	 * @param   unknown  $entity
	 * @return  array
	 */
	private static function http($method, $url, $entity = NULL)
	{
		$conf = HubgraphConfiguration::instance();

		if (!($sock = @fsockopen($conf['host'], $conf['port'], $_errno, $errstr, 1)))
		{
			throw new HubGraphConnectionError('unable to establish HubGraph connection using ' . $conf['host'] . ': ' . $errstr);
		}

		fwrite($sock, "$method $url HTTP/1.1\r\n");
		fwrite($sock, "Host: localhost\r\n");
		fwrite($sock, "X-HubGraph-Request: " . sha1(uniqid()) . "\r\n");
		if ($entity)
		{
			fwrite($sock, "Content-Length: " . strlen($entity) . "\r\n");
		}
		fwrite($sock, "Connection: close\r\n\r\n");
		if ($entity)
		{
			fwrite($sock, $entity);
		}

		$first     = true;
		$inHeaders = true;
		$status    = NULL;
		$body      = '';

		while (($chunk = fgets($sock, self::CHUNK_LEN)))
		{
			if ($first && !preg_match('/^HTTP\/1\.1\ (\d{3})/', $chunk, $code))
			{
				throw new \Exception('Unable to determine response status');
			}
			elseif ($first)
			{
				if (($status = intval($code[1])) === 204)
				{
					break;
				}
				$first = false;
			}
			elseif ($inHeaders && preg_match('/^[\r\n]+$/', $chunk))
			{
				$inHeaders = false;
			}
			elseif (!$inHeaders)
			{
				$body .= $chunk;
			}
		}
		fclose($sock);

		return array($status, $body);
	}

	/**
	 * Description...
	 *
	 * @param   unknown  $key
	 * @param   unknown  $args
	 * @return  unknown
	 */
	public static function execView($key, $args = NULL)
	{
		static $count = 0;

		++$count;

		$path = '/views/' . $key;

		$query = '';
		if ($args)
		{
			foreach ($args as $k => $v)
			{
				if (is_array($v))
				{
					$query .= ($query == '' ? '' : '&') . $k . '=' . implode(',', array_map('urlencode', $v));
				}
				else
				{
					$query .= ($query == '' ? '' : '&') . $k . '=' . (is_bool($v) ? ($v ? 'true' : 'false') : urlencode($v));
				}
			}
		}
		$query .= '&count=' . $count;

		list($code, $entity) = self::http('GET', $path . '?' . $query);

		return $entity;
	}
}
