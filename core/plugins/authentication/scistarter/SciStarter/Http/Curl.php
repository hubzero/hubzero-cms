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
 * @author    Ilya Shunko <ishunko@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace SciStarter\Http;

/**
 * Curl http transport class
 **/
class Curl
{
	/**
	 * The connection resource
	 *
	 * @var  object
	 **/
	private $resource = null;

	/**
	 * Constructs a new instance
	 *
	 * @return  void
	 **/
	public function __construct()
	{
		$this->initialize();
	}

	/**
	 * Initializes the resource
	 *
	 * @return  $this
	 **/
	public function initialize()
	{
		$this->resource = curl_init();
		$this->setReturnTransfer();

		return $this;
	}

	/**
	 * Sets a generic option on the curl resource
	 *
	 * @param   int    $opt    The curl option to set
	 * @param   mixed  $value  The curl option value to use
	 * @return  $this
	 **/
	public function setOpt($opt, $value)
	{
		curl_setopt($this->resource, $opt, $value);

		return $this;
	}

	/**
	 * Returns string response
	 *
	 * @return  $this
	 **/
	public function setReturnTransfer()
	{
		return $this->setOpt(CURLOPT_RETURNTRANSFER, 1);
	}

	/**
	 * Sets the url endpoint
	 *
	 * @param   string  $url  the url endpoint to set
	 * @return  $this
	 **/
	public function setUrl($url)
	{
		return $this->setOpt(CURLOPT_URL, $url);
	}

	/**
	 * Sets the post fields (and implicitly implies a post request)
	 *
	 * @param   array  $fields  the post fields to set on the request
	 * @return  $this
	 **/
	public function setPostFields($fields)
	{
		// Form raw string version of fields
		$raw   = '';
		$first = true;

		foreach ($fields as $key => $value)
		{
			if (!$first)
			{
				$raw .= '&';
			}

			$raw .= $key . '=' . $value;

			$first = false;
		}

		$this->setOpt(CURLOPT_POST, count($fields));
		$this->setOpt(CURLOPT_POSTFIELDS, $raw);

		return $this;
	}

	/**
	 * Sets a header on the request
	 *
	 * @param   string|array  $header  the header to set
	 * @return  $this
	 **/
	public function setHeader($header)
	{
		$headers = [];

		if (is_array($header))
		{
			foreach ($header as $key => $value)
			{
				$headers[] = $key . ': ' . $value;
			}
		}
		else
		{
			$headers[] = $header;
		}

		$this->setOpt(CURLOPT_HTTPHEADER, $headers);

		return $this;
	}

	/**
	 * Executes the request
	 *
	 * @return  string
	 **/
	public function execute()
	{
		$response = curl_exec($this->resource);

		$this->reset();

		return $response;
	}

	/**
	 * Resets the curl resource to be used again
	 *
	 * @return  $this
	 **/
	public function reset()
	{
		$this->close()
			 ->initialize();

		return $this;
	}

	/**
	 * Shuts down the resource
	 *
	 * @return  $this
	 **/
	public function close()
	{
		curl_close($this->resource);

		return $this;
	}
}
