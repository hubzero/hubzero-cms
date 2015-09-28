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
