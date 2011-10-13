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
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

class Hubzero_API_Request
{
	public $method = 'GET';
	public $suppress_response_codes = false;
	public $accepts = "text/plain";
	public $path = '';
		
	function getHeaderField($header)
	{
		$header = strtoupper($header);
		
		if ($header == 'ACCEPT')
		{
			return $this->accepts;
		}
		
		$key = 'HTTP_' . strtoupper($header);
		
		if (isset($_SERVER[$key]))
		{
			return $SERVER[$key];
		}
		
		return null;
	}
	
	function __construct()
	{
		$this->path = $_SERVER['SCRIPT_URL'];
		
		if (isset($_GET['format']))
		{
			$this->accepts = $this->_parse_accept($_GET['format']);
		}
		else if (isset($_POST['format']))
		{
			$this->accepts = $this->_parse_accept($_POST['format']);
		}
		else if (isset($_SERVER['HTTP_ACCEPT'])) 
		{
			$this->accepts = $_SERVER['HTTP_ACCEPT'];
		}
		
		if (empty($this->accepts))
		{
			$format = strrchr($_SERVER['REQUEST_URI'],'.');
		
			if (strchr($format,'/') === false)
			{
				$this->accepts = $this->_parse_accept(substr($format,1));
			}
		}
		
		if (isset($_GET['suppress_response_codes']))
		{
			$this->suppress_response_codes = true;
		}
		
		if (isset($_POST['suppress_response_codes']))
		{
			$this->suppress_response_codes = true;
		}
		
		if (isset($_SERVER['HTTP_X_HTTP_SUPPRESS_RESPONSE_CODES']))
		{
			$this->suppress_response_codes = true;
		}
		
		if (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']))
		{
			$this->method = strtoupper($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']);
		}
		else 
		{
			$this->method = $_SERVER['REQUEST_METHOD'];
		}
	}
		
	function getMethod()
	{
		return $this->method;
	}
	
	function getSuppressResponseCodes()
	{
		return $this->suppress_response_codes;
	}
	
	function _parse_accept($input)
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
		
		if (isset($_types[$input]))
		{
			return $_types[$input];
		}
		
		return '';
	}
	
}	