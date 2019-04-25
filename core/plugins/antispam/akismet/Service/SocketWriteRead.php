<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Antispam\Akismet\Service;

use Exception;

/**
 * Utility class used by Akismet
 *
 * This class is used by Akismet to do the actual sending and receiving of data.  It opens a connection to a remote host, sends some data and the reads the response and makes it available to the calling program.
 * The code that makes up this class originates in the Akismet WordPress plugin, which is {@link http://akismet.com/download/ available on the Akismet website}.
 * N.B. It is not necessary to call this class directly to use the Akismet class.  This is included here mainly out of a sense of completeness.
 *
 * @package  akismet
 * @version  0.1
 * @author   Alex Potsides
 * @link     http://www.achingbrain.net/
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
	 * Constructor
	 *
	 * @param   string   $host            The host to send/receive data.
	 * @param   integer  $port            The port on the remote host.
	 * @param   string   $request         The data to send.
	 * @param   integer  $responseLength  The amount of data to read.  Defaults to 1160 bytes.
	 * @return  void
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
	 * Sends the data to the remote host.
	 *
	 * @throws  An exception is thrown if a connection cannot be made to the remote host.
	 */
	public function send()
	{
		$this->response = '';

		$fs = @fsockopen($this->host, $this->port, $this->errorNumber, $this->errorString, 3);

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
	 * Returns the server response text
	 *
	 * @return  string
	 */
	public function getResponse()
	{
		return $this->response;
	}

	/**
	 * Returns the error number
	 *
	 * If there was no error, 0 will be returned.
	 *
	 * @return  integer
	 */
	public function getErrorNumner()
	{
		return $this->errorNumber;
	}

	/**
	 * Returns the error string
	 *
	 * If there was no error, an empty string will be returned.
	 *
	 * @return  string
	 */
	public function getErrorString()
	{
		return $this->errorString;
	}
}
