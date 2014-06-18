<?php
/**
 * HUBzero CMS
 *
 * Copyright 2011-2012 Purdue University. All rights reserved.
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
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2011-2012 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Api;

use Hubzero\Api\Response\Xml;

/**
 * API response class
 */
class Response
{

	/**
	 * Description for 'suppress_response_codes'
	 *
	 * @var boolean
	 */
	public  $suppress_response_codes = false;

	/**
	 * Description for '_request_accept'
	 *
	 * @var string
	 */
	private $_request_accept = '*/*';

	/**
	 * Description for '_response_accept'
	 *
	 * @var string
	 */
	private $_response_accept = 'text/plain';

	/**
	 * Description for '_request_accept_encoding'
	 *
	 * @var unknown
	 */
	private $_request_accept_encoding = null;

	/**
	 * Description for '_response_accept_encoding'
	 *
	 * @var unknown
	 */
	private $_response_accept_encoding = null;

	/**
	 * Description for '_autoencode'
	 *
	 * @var boolean
	 */
	private $_autoencode = false;

	/**
	 * Description for '_cachable'
	 *
	 * @var boolean
	 */
	private $_cachable = true;

	/**
	 * Description for '_sent'
	 *
	 * @var boolean
	 */
	private $_sent = false;

	/**
	 * Description for '_http_version'
	 *
	 * @var string
	 */
	private $_http_version = 'HTTP/1.1';

	/**
	 * Description for '_status_code'
	 *
	 * @var mixed
	 */
	private $_status_code = 200;

	/**
	 * Description for '_reason'
	 *
	 * @var string
	 */
	private $_reason = 'OK';

	/**
	 * Description for '_headers'
	 *
	 * @var array
	 */
	private $_headers = array();

	/**
	 * Description for '_content_type'
	 *
	 * @var unknown
	 */
	private $_content_type = null;

	/**
	 * Description for '_encoding'
	 *
	 * @var unknown
	 */
	private $_encoding = null;

	/**
	 * Description for '_body'
	 *
	 * @var array
	 */
	private $_body = array();

	/**
	 * Description for '_reasons'
	 *
	 * @var array
	 */
	private static $_reasons = array(
		200 => 'OK',
		404 => 'Not Found',
		406 => 'Not Acceptable',
		500 => 'Internal Server Error',
	);

	/**
	 * Short description for '__construct'
	 *
	 * Long description (if any) ...
	 *
	 * @return     void
	 */
	function __construct()
	{
		$headers = headers_list();

		foreach ($headers as $header)
		{
			$this->setHeader($header);
		}

	}

	/**
	 * Short description for 'setStatusCode'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $code Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	function setStatusCode($code)
	{
		if ($this->_sent)
		{
			return false;
		}

		$this->_status_code = $code;

		return true;
	}

	/**
	 * Short description for 'getStatusCode'
	 *
	 * Long description (if any) ...
	 *
	 * @return     mixed Return description (if any) ...
	 */
	function getStatusCode()
	{
		return (integer) $this->_status_code;
	}

	/**
	 * Short description for 'setHttpVersion'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $version Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	function setHttpVersion($version)
	{
		if ($this->_sent)
		{
			return false;
		}

		$this->_http_version = $version;

		return true;
	}

	/**
	 * Short description for 'getHttpVersion'
	 *
	 * Long description (if any) ...
	 *
	 * @return     string Return description (if any) ...
	 */
	function getHttpVersion()
	{
		return (string) $this->_http_version;
	}

	/**
	 * Short description for 'setReason'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $reason Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	function setReason($reason)
	{
		if ($this->_sent)
		{
			return false;
		}

		$this->_reason = $reason;

		return true;
	}

	/**
	 * Short description for 'getReason'
	 *
	 * Long description (if any) ...
	 *
	 * @return     string Return description (if any) ...
	 */
	function getReason()
	{
		return (string) $this->_reason;
	}

	/**
	 * Short description for 'setStatusLine'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $string Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	function setStatusLine($string = null)
	{
		if ($this->_sent)
		{
			return false;
		}

		if (is_null($string))
		{
			return false;
		}

		if (empty($string))
		{
			$this->setStatusCode(200);
			$this->setReason('OK');
			$this->setHttpVersion('HTTP/1.1');
			return true;
		}

		list($v, $s, $r) = explode(' ',$string,3);

		if (!is_numeric($s))
		{
			return false;
		}

		$this->setStatusCode($s);
		$this->setReason($r);
		$this->setHttpVersion($v);

		return true;
	}

	/**
	 * Short description for 'getStatusLine'
	 *
	 * Long description (if any) ...
	 *
	 * @return     string Return description (if any) ...
	 */
	function getStatusLine()
	{
		return (string) $this->getHttpVersion() . " " . $this->getStatusCode() . " " . $this->getReason();
	}

	/**
	 * Short description for 'setHeader'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $string Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	function setHeader($string)
	{
		if ($this->_sent)
		{
			return false;
		}

		return $this->addHeader($string, true);
	}

	/**
	 * Short description for 'addHeader'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $string Parameter description (if any) ...
	 * @param      boolean $replace Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	function addHeader($string, $replace = false)
	{
		if ($this->_sent)
		{
			return false;
		}

		list($name , $value) = explode(':',$string,2);

		$name   = trim($name);
		$value  = trim($value);

		if (empty($value))
			return false;

		if ($replace == true)
			$this->removeHeader($name);

		$this->_headers[] = array('name' => $name, 'value' => $value, 'replace' => $replace);

		return true;
	}

	/**
	 * Short description for 'removeAllHeaders'
	 *
	 * Long description (if any) ...
	 *
	 * @return     boolean Return description (if any) ...
	 */
	function removeAllHeaders()
	{
		if ($this->_sent)
		{
			return false;
		}

		$this->_headers = array();
		$this->setStatusCode(200);
		$this->setReason('OK');
		$this->setHttpVersion('HTTP/1.1');

		return true;
	}

	/**
	 * Short description for 'removeHeader'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $name Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	function removeHeader($name)
	{
		if ($this->_sent)
		{
			return false;
		}

		foreach ($this->_headers as $header)
		{
			if ($name == $header['name'])
			{
				unset($this->_headers[$key]);
			}
		}

		return true;
	}

	/**
	 * Short description for 'getHeader'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $name Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	function getHeader($name)
	{
		$result = array();

		foreach ($this->headers as $header)
		{
			if ($header['name'] == $name)
			{
				$result[] =  $header['name'] . ': ' . $header['value'] . "\n";
			}
		}

		return $result;
	}

	/**
	 * Short description for 'getAllHeaders'
	 *
	 * Long description (if any) ...
	 *
	 * @return     void
	 */
	function getAllHeaders()
	{
		$result = array();

		$result[] = $this->_http_version . ' ' . $this->_status_code . ' ' . $this->_reason . "\n";

		foreach ($this->_headers as $header)
		{
			$result[] =  $header['name'] . ': ' . $header['value'] . "\n";
		}

	}

	/**
	 * Short description for 'headersSent'
	 *
	 * Long description (if any) ...
	 *
	 * @return     boolean Return description (if any) ...
	 */
	function headersSent()
	{
		if (PHP_SAPI == 'cli')
		{
			return $this->_sent;
		}

		return $this->_sent && headers_sent();
	}

	/**
	 * Short description for 'sendHeaders'
	 *
	 * Long description (if any) ...
	 *
	 * @return     boolean Return description (if any) ...
	 */
	function sendHeaders()
	{
		$this->_sent = true;

		@header($this->_http_version . ' ' . $this->_status_code . ' ' . $this->_reason . "\n");

		foreach ($this->_headers as $header)
			@header($header['name'] . ': ' . $header['value'], $header['replace']);

		if (PHP_SAPI == 'cli')
		{
			echo $this->_http_version . ' ' . $this->_status_code . ' ' . $this->_reason . "\n";

			foreach ($this->_headers as $header)
			{
				echo $header['name'] . ': ' . $header['value'] . "\n";
			}

			echo "\n";
		}

		return true;
	}

	/**
	 * Short description for 'setEncodeOnOutput'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $value Parameter description (if any) ...
	 * @return     void
	 */
	function setEncodeOnOutput($value)
	{
		if ($value)
		{
			$this->_autoencode = true;
		}
		else
		{
			$this->_autoencode = false;
		}
	}

	/**
	 * Short description for 'getEncodeOnOutput'
	 *
	 * Long description (if any) ...
	 *
	 * @return     boolean Return description (if any) ...
	 */
	function getEncodeOnOutput()
	{
		return (boolean) $this->_autoencode;
	}

	/**
	 * Short description for 'setCachable'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $value Parameter description (if any) ...
	 * @return     void
	 */
	function setCachable($value)
	{
		if ($value)
		{
			$this->_cachable = true;
		}
		else
		{
			$this->_cachable = false;
		}
	}

	/**
	 * Short description for 'getCachable'
	 *
	 * Long description (if any) ...
	 *
	 * @return     boolean Return description (if any) ...
	 */
	function getCachable()
	{
		return $this->_cachable;
	}

	/**
	 * Short description for 'setBody'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $content Parameter description (if any) ...
	 * @return     void
	 */
	function setBody($content)
	{
		$this->_body = array((string) $content);
	}

	/**
	 * Short description for 'prependBody'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $content Parameter description (if any) ...
	 * @return     void
	 */
	function prependBody($content)
	{
		array_unshift($this->_body, (string) $content);
	}

	/**
	 * Short description for 'appendBody'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $content Parameter description (if any) ...
	 * @return     void
	 */
	function appendBody($content)
	{
		array_push($this->_body, (string) $content);
	}

	/**
	 * Short description for 'getBody'
	 *
	 * Long description (if any) ...
	 *
	 * @param      boolean $toArray Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	function getBody($toArray = false)
	{
		if ($toArray)
		{
			return $this->_body;
		}

		ob_start();
		foreach ($this->_body as $content)
		{
			echo $content;
		}
		return ob_get_clean();
	}

	/**
	 * Short description for 'setSuppressResponseCodes'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $value Parameter description (if any) ...
	 * @return     void
	 */
	function setSuppressResponseCodes($value)
	{
		if ($value)
		{
			$this->suppress_response_codes = true;
		}
		else
		{
			$this->suppress_response_codes = false;
		}
	}

	/**
	 * Short description for 'getSuppressResponseCodes'
	 *
	 * Long description (if any) ...
	 *
	 * @return     boolean Return description (if any) ...
	 */
	function getSuppressResponseCodes()
	{
		return $this->suppress_response_codes;
	}

	/**
	 * Short description for '_parse_accept'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $input Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	private function _parse_accept($input)
	{
		static $_types = array(
			'xml' => 'application/xml',
			'html' => 'text/html',
			'xhtml' => 'application/xhtml+xml',
			'json' => 'application/json',
			'text' => 'text/plain',
			'txt' => 'text/plain',
			'plain' => 'text/plain',
			'php_serialized' => 'application/vnd.php.serialized',
			'php' => 'application/php',
		);

		$accept = array();

		foreach (explode(',', $input) as $header)
		{
			$result = preg_split('/;\s*q=/', $header);

			$type = isset($result[0]) ? $result[0] : null;

			$q = isset($result[1]) ? $result[1] : 1;

			if (isset($_types[$type]))
			{
				$type = $_types[$type];
			}

			if (!empty($type))
			{
				$accept[$type] = $q;
			}
		}

		arsort($accept);

		return $accept;
	}

	/**
	 * Short description for '_parse_encoding'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $input Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	private function _parse_encoding($input)
	{
		$accept = array();

		foreach (explode(',', $input) as $header)
		{
			$result = preg_split('/;\s*q=/', $header);

			$type = isset($result[0]) ? $result[0] : null;

			$q = isset($result[1]) ? $result[1] : 1;

			if (!empty($type))
			{
				$accept[$type] = $q;
			}
		}

		arsort($accept);

		return $accept;
	}

	/**
	 * Short description for 'setRequestAccepts'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $accept Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	function setAcceptableMediaTypes($accept)
	{
		$accepts = $this->_parse_accept($accept);
		$provides = $this->_parse_accept($this->_response_accept);

		$new_content_type = $this->_resolveContentType($accepts, $provides);

		if (empty($this->_body) || $this->_content_type === null || $this->_content_type == $new_content_type)
		{
			$this->_content_type = $new_content_type;
			$this->_request_accept = $accept;
			return true;
		}

		return false;
	}

	/**
	 * Short description for 'getRequestAccepts'
	 *
	 * Long description (if any) ...
	 *
	 * @return     string Return description (if any) ...
	 */
	function getAcceptableMediaTypes()
	{
		return $this->_request_accept;
	}

	/**
	 * Short description for 'setResponseProvides'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $provide Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	function setResponseProvides($provide)
	{
		$accepts = $this->_parse_accept($this->_request_accept);
		$provides = $this->_parse_accept($provide);

		$new_content_type = $this->_resolveContentType($accepts, $provides);

		if (empty($this->_body) || $this->_content_type === null || $this->_content_type == $new_content_type)
		{
			$this->_content_type = $new_content_type;
			$this->_response_accept = $provide;
			return true;
		}

		return false;
	}

	/**
	 * Short description for 'getResponseProvides'
	 *
	 * Long description (if any) ...
	 *
	 * @return     string Return description (if any) ...
	 */
	function getResponseProvides()
	{
		return $this->_response_accept;
	}

	/**
	 * Short description for 'getContentType'
	 *
	 * Long description (if any) ...
	 *
	 * @return     unknown Return description (if any) ...
	 */
	function getContentType()
	{
		$accepts = $this->_parse_accept($this->_request_accept);
		$provides = $this->_parse_accept($this->_response_accept);

		if ($this->_content_type === null)
		{
			$this->_content_type = $this->_resolveContentType($accepts, $provides);
		}

		return $this->_content_type;
	}

	/**
	 * Short description for 'setRequestAcceptsEncodings'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $accept Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	function setAcceptableEncodings($accept)
	{
		$accepts = $this->_parse_encoding($accept);
		$provides = $this->_parse_encoding($this->_response_accept_encoding);

		$new_encoding = $this->_resolveEncoding($accepts, $provides);

		if (empty($this->_body) || $this->_encoding === null || $this->_encoding == $new_encoding)
		{
			$this->_encoding = $new_encoding;
			$this->_request_accept_encoding = $accept;
			return true;
		}

		return false;
	}

	/**
	 * Short description for 'getRequestAcceptsEncodings'
	 *
	 * Long description (if any) ...
	 *
	 * @return     unknown Return description (if any) ...
	 */
	function getAcceptableEncodings()
	{
		return $this->_request_accept_encoding;
	}

	/**
	 * Short description for 'setResponseProvideEncoding'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $provide Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	function setResponseProvideEncoding($provide)
	{
		$accepts = $this->_parse_encoding($this->_request_accept_encoding);
		$provides = $this->_parse_encoding($provide);

		$new_encoding = $this->_resolveEncoding($accepts, $provides);

		if (empty($this->_body) || $this->_encoding === null || $this->_encoding == $new_encoding)
		{
			$this->_encoding = $new_encoding;
			$this->_response_accpet_encoding = $provide;
			return true;
		}

		return false;
	}

	/**
	 * Short description for 'getResponseProvideEncoding'
	 *
	 * Long description (if any) ...
	 *
	 * @return     unknown Return description (if any) ...
	 */
	function getResponseProvideEncoding()
	{
		return $this->_response_accept_encoding;
	}

	/**
	 * Short description for 'getEncoding'
	 *
	 * Long description (if any) ...
	 *
	 * @return     unknown Return description (if any) ...
	 */
	function getEncoding()
	{
		$accepts = $this->_parse_accept($this->_request_accept_encoding);
		$provides = $this->_parse_accept($this->_response_accept_encoding);

		if ($this->_encoding === null)
		{
			$this->_encoding = $this->_resolveEncoding($accepts, $provides);
		}

		return $this->_encoding;
	}

	/**
	 * Short description for '_resolveContentType'
	 *
	 * Long description (if any) ...
	 *
	 * @param      array $accept Parameter description (if any) ...
	 * @param      array $provide Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	private function _resolveContentType($accept, $provide)
	{
		$best_type = '';
		$best_score = '';
		$score = null;

		if (empty($accept))
		{
			$accept = array("*/*"=>"1");
		}

		foreach ($accept as $client_type=>$client_value)
		{
			if ($client_type == 'text/*')
			{
				foreach ($provide as $provider_type=>$provider_value)
				{
					if (strncmp($provider_type,'text/',5) == 0)
					{
						$score = $client_value * $provider_value;
						$client_type = $provider_type;
						break;
					}
				}
			}
			else if (($client_type == '*/*') || ($client_type == 'application/*'))
			{
				foreach ($provide as $provider_type=>$provider_value)
				{
					$score = $client_value * $provider_value;
					$client_type = $provider_type;
					break;
				}
			}
			else if (isset($provide[$client_type]))
			{
				$score = $client_value * $provide[$client_type];
			}

			if ($score > $best_score)
			{
				$best_score = $score;
				$best_type = $client_type;
			}
		}

		if (empty($best_type))
			return null;
		else
			return $best_type;
	}

	/**
	 * Short description for '_resolveEncoding'
	 *
	 * Long description (if any) ...
	 *
	 * @param      array $accept Parameter description (if any) ...
	 * @param      array $provide Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	private function _resolveEncoding($accept, $provide)
	{
		$best_type = '';
		$best_score = '';
		$score = null;

		foreach ($accept as $client_type => $client_value)
		{
			if (isset($provide[$client_type]))
			{
				$score = $client_value * $provide[$client_type];
			}

			if ($score > $best_score)
			{
				$best_score = $score;
				$best_type = $client_type;
			}
		}

		if (empty($best_type))
			return 'identity';
		else
			return $best_type;
	}

	/**
	 * Short description for '_encode'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $data Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	private function _encode( $data )
	{
		$encoding = $this->getEncoding();

		if (empty($encoding) || ($encoding == 'identity'))
		{
			return $data;
		}

		if (!in_array($encoding, array('gzip','x-gzip','deflate','x-deflate')))
			return false;

		if (!extension_loaded('zlib')) {
			return false;
		}

		if ($encoding == 'gzip' || $encoding == 'x-gzip')
		{
			return gzencode($data);
		}
		else if ($encoding == 'deflate' || $encoding == 'x-deflate')
		{
			return gzcompress($data);
		}
		else
		{
			return false;
		}

		return $gzdata;
	}

	/**
	 * Short description for 'send'
	 *
	 * Long description (if any) ...
	 *
	 * @return     void
	 */
	function send()
	{
		if (!$this->_cachable)
		{
			$this->setHeader( 'Expires: Mon, 1 Jan 2001 00:00:00 GMT');
			$this->setHeader( 'Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
			$this->setHeader( 'Cache-Control: no-store, no-cache, must-revalidate');
			$this->addHeader( 'Cache-Control: post-check=0, pre-check=0');
			$this->setHeader( 'Pragma: no-cache' );
			$this->setHeader( 'Connection: close' );
		}

		$content_type = $this->getContentType();

		$this->setHeader('Content-Type: ' . $content_type);

		$this->sendHeaders();

		$data = $this->getBody();

		if ($this->_autoencode)
			$data = $this->_encode($data);

		echo $data;
	}

	/**
	 * Short description for '_serializeResponseObject'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $mixed Parameter description (if any) ...
	 * @param      boolean $encode Parameter description (if any) ...
	 * @return     integer Return description (if any) ...
	 */
	private function _serializeResponseObject($mixed, $encode = true)
	{
		$suppress_response_codes = $this->suppress_response_codes;
		$content_type = $this->getContentType();
		$reason = $this->_reason;
		$status = $this->_status_code;
		$message = $mixed;

		if ($content_type == null)
			return 406;

		ob_start();

		if ($suppress_response_codes)
		{
			$response = new \stdClass();
			$response->status = $status;
			$response->reason = $reason;
			$response->message = $message;
		}
		else
			$response = $message;

		if ($content_type == 'text/plain')
		{
			if ($suppress_response_codes)
			{
				echo "Status: $status\n";
				echo "Reason: $reason\n";
				echo "\n";
			}

			if (!is_object($message) && !is_array($message))
			{
				echo $message;
			}
			else
			{
				echo json_encode($message);
			}
		}
		else if ($content_type == 'text/html')
		{
			$reason = htmlspecialchars($reason);
			$message = htmlspecialchars($message);

			echo "<!DOCTYPE html>\n";
			echo "<html lang=en>\n";
			echo "<head>\n";
			echo "<meta charset=utf-8>\n";
			echo "<title>$status $reason</title>\n";
			echo "</head>\n";
			echo "<body>\n";
			echo '<div class="error">' . "\n";

			echo '<h1 id="reason">' . $reason . "</h1>\n";

			if ($suppress_response_codes)
			{
				echo '<p id="status">' . htmlspecialchars($status) . "</p>\n";
			}

			if (!is_object($message) && !is_array($message))
			{
				echo '<p id ="message">' . $message . "</p>\n";
			}
			else
			{
				echo '<p id ="message">' . json_encode($message) . "</p>\n";
			}

			echo "</div>\n";
			echo "</body>\n";
			echo "</html>";
		}
		else if ($content_type == 'application/xhtml+xml')
		{
			$reason = htmlspecialchars($reason);
			$message = htmlspecialchars($message);

			echo '<?xml version="1.0" ?>' . "\n";
			echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">' . "\n";
			echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">' . "\n";
			echo "<head>\n";
			echo "<title>$status $reason</title>\n";
			echo "</head>\n";
			echo "<body>\n";
			echo '<div class="error">' . "\n";

			echo '<h1 id="reason">' . $reason . "</h1>\n";

			if ($suppress_response_codes)
			{
				echo '<p id="status">' . htmlspecialchars($status) . "</p>\n";
			}
			if (!is_object($message) && !is_array($message))
			{
				echo '<p id ="message">' . $message . "</p>\n";
			}
			else
			{
				echo '<p id ="message">' . json_encode($message) . "</p>\n";
			}

			echo "</div>\n";
			echo "</body>\n";
			echo "</html>";
		}
		else if ($content_type == "application/xml")
		{
			echo Xml::encode($response);
		}
		else if ($content_type == 'application/json')
		{
			echo json_encode($response);
		}
		else if ($content_type == 'application/vnd.php.serialized')
		{
			echo serialize($response);
		}
		else if ($content_type == 'application/php')
		{
			var_export($response);
		}
		else if ($content_type == 'application/x-www-form-urlencoded')
		{
			if (!is_object($message))
			{
				echo $message;
			}
			else
			{
				echo json_encode($message);
			}
		}

		$data = ob_get_clean();

		if ($encode)
		{
			$data = $this->_encode($data);
		}

		return $data;
	}

	/**
	 * Short description for 'setMessage'
	 *
	 * Long description (if any) ...
	 *
	 * @param      integer $message Parameter description (if any) ...
	 * @param      unknown $status Parameter description (if any) ...
	 * @param      unknown $reason Parameter description (if any) ...
	 * @return     void
	 */
	function setMessage($message = null, $status = null, $reason = null)
	{
		if ($status != null)
		{
			$this->setStatusCode($status);
		}

		if ($reason != null)
		{
			$this->setReason($reason);
		}

		$message = $this->_serializeResponseObject($message);

		if ($message === 406)
		{
			$this->setStatusCode(406);
			$this->setReason('Not Acceptable');
			$this->setBody(null);
			$this->setEncodeOnOutput(false);
		}
		else
		{
			$this->setBody($message);
			$this->setEncodeOnOutput(true);
		}
	}

	/**
	 * Short description for 'setErrorMessage'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $status Parameter description (if any) ...
	 * @param      unknown $reason Parameter description (if any) ...
	 * @param      unknown $message Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	function setErrorMessage($status = null, $reason = null, $message = null)
	{
		return $this->setMessage($message,$status,$reason);
	}
}
