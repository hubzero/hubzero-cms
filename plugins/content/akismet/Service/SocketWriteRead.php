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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Plugins\Content\Akismet\Service;

use Exception;

/**
 *	Utility class used by Akismet
 *
 *  This class is used by Akismet to do the actual sending and receiving of data.  It opens a connection to a remote host, sends some data and the reads the response and makes it available to the calling program.
 *  The code that makes up this class originates in the Akismet WordPress plugin, which is {@link http://akismet.com/download/ available on the Akismet website}.
 *	N.B. It is not necessary to call this class directly to use the Akismet class.  This is included here mainly out of a sense of completeness.
 *
 *	@package	akismet
 *	@name		SocketWriteRead
 *	@version	0.1
 *  @author		Alex Potsides
 *  @link		http://www.achingbrain.net/
 */
class SocketWriteRead
{
	/**
	 * The host to send/receive data.
	 *
	 * @param string
	 */
	private $host;

	/**
	 * The port on the remote host.
	 *
	 * @param integer
	 */
	private $port;

	/**
	 * The data to send.
	 *
	 * @param string
	 */
	private $request;

	/**
	 * Response
	 *
	 * @param string
	 */
	private $response;

	/**
	 * The amount of data to read.  Defaults to 1160 bytes.
	 *
	 * @param integer
	 */
	private $responseLength;

	/**
	 * The error number.
	 *
	 * @param integer
	 */
	private $errorNumber;

	/**
	 * The error message.
	 *
	 * @param string
	 */
	private $errorString;

	/**
	 *	@param	string	$host			The host to send/receive data.
	 *	@param	int		$port			The port on the remote host.
	 *	@param	string	$request		The data to send.
	 *	@param	int		$responseLength	The amount of data to read.  Defaults to 1160 bytes.
	 */
	public function __construct($host, $port, $request, $responseLength = 1160)
	{
		$this->host           = $host;
		$this->port           = $port;
		$this->request        = $request;
		$this->responseLength = $responseLength;
		$this->errorNumber    = 0;
		$this->errorString    = '';
	}

	/**
	 *  Sends the data to the remote host.
	 *
	 * @throws	An exception is thrown if a connection cannot be made to the remote host.
	 */
	public function send()
	{
		$this->response = '';

		$fs = fsockopen($this->host, $this->port, $this->errorNumber, $this->errorString, 3);

		if ($this->errorNumber != 0)
		{
			throw new Exception('Error connecting to host: ' . $this->host . ' Error number: ' . $this->errorNumber . ' Error message: ' . $this->errorString);
		}

		if ($fs !== false)
		{
			@fwrite($fs, $this->request);

			while (!feof($fs))
			{
				$this->response .= fgets($fs, $this->responseLength);
			}

			fclose($fs);
		}
	}

	/**
	 *  Returns the server response text
	 *
	 *  @return	string
	 */
	public function getResponse()
	{
		return $this->response;
	}

	/**
	 *	Returns the error number
	 *
	 *	If there was no error, 0 will be returned.
	 *
	 *	@return int
	 */
	public function getErrorNumner()
	{
		return $this->errorNumber;
	}

	/**
	 *	Returns the error string
	 *
	 *	If there was no error, an empty string will be returned.
	 *
	 *	@return string
	 */
	public function getErrorString()
	{
		return $this->errorString;
	}
}