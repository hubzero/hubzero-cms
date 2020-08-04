<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
	 * Set a header on the Response.
	 *
	 * @param   string  $key
	 * @param   mixed   $values
	 * @param   bool    $replace
	 * @return  object  $this
	 */
	public function header($key, $values, $replace = true)
	{
		$this->headers->set($key, $values, $replace);

		return $this;
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
