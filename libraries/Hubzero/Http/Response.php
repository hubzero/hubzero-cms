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

namespace Hubzero\Http;

use Symfony\Component\HttpFoundation\Response as BaseResponse;

/**
 * Response represents an HTTP response.
 */
class Response extends BaseResponse
{
	/**
	 * Compress the output?
	 *
	 * @var  boolean
	 */
	protected $compress = false;

	/**
	 * Should data be compressed?
	 *
	 * @param   boolean  $value
	 * @return  object
	 */
	public function compress($value)
	{
		$this->compress = (bool) $value;

		return $this;
	}

	/**
	 * Compress the data
	 *
	 * Checks the accept encoding of the browser and compresses the data before
	 * sending it to the client.
	 *
	 * @param   string  $data  Content to compress for output.
	 * @return  string  compressed data
	 */
	protected function squeeze($data)
	{
		$encoding = $this->acceptEncoding();

		if (!$encoding)
		{
			return $data;
		}

		if (!extension_loaded('zlib') || ini_get('zlib.output_compression'))
		{
			return $data;
		}

		if (headers_sent())
		{
			return $data;
		}

		if (connection_status() !== 0)
		{
			return $data;
		}

		// Ideal level
		$level = 4;

		/*
		$size = strlen($data);
		$crc  = crc32($data);

		$gzdata  = "\x1f\x8b\x08\x00\x00\x00\x00\x00";
		$gzdata .= gzcompress($data, $level);

		$gzdata  = substr($gzdata, 0, strlen($gzdata) - 4);
		$gzdata .= pack("V",$crc) . pack("V", $size);
		*/

		$gzdata = gzencode($data, $level);

		$this->headers->set('Content-Encoding', $encoding);
		$this->headers->set('X-Content-Encoded-By', 'HUBzero');

		return $gzdata;
	}

	/**
	 * Check, whether client supports compressed data
	 *
	 * @return  mixed
	 */
	protected function acceptEncoding()
	{
		$encoding = false;

		if (!isset($_SERVER['HTTP_ACCEPT_ENCODING']))
		{
			return $encoding;
		}

		if (false !== strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip'))
		{
			$encoding = 'gzip';
		}

		if (false !== strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'x-gzip'))
		{
			$encoding = 'x-gzip';
		}

		return $encoding;
	}

	/**
	 * Sends HTTP headers and content.
	 *
	 * @return  object  Response
	 */
	public function send($flush = false)
	{
		if ($this->compress)
		{
			$this->setContent($this->squeeze($this->getContent()));
		}

		$this->sendHeaders();
		$this->sendContent();

		if ($flush)
		{
			if (function_exists('fastcgi_finish_request'))
			{
				fastcgi_finish_request();
			}
			elseif ('cli' !== PHP_SAPI)
			{
				static::closeOutputBuffers(0, true);
				flush();
			}
		}

		return $this;
	}
}
