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
 * @package   hubzero-cms
 * @author    Steve Snyder <snyder13@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Models\Hubgraph;

use Exception;

require_once(__DIR__ . DS . 'connectionerror.php');
require_once(__DIR__ . DS . 'configuration.php');
require_once(__DIR__ . DS . 'db.php');

/**
 * Hubgraph client
 */
class Client
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
		$conf = Configuration::instance();

		if (!($sock = @fsockopen($conf['host'], $conf['port'], $_errno, $errstr, 1)))
		{
			throw new ConnectionError('unable to establish HubGraph connection using ' . $conf['host'] . ': ' . $errstr);
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
				throw new Exception('Unable to determine response status');
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
