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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Resources\Helpers\Tests;

use Hubzero\Content\Auditor\Test;
use Hubzero\Content\Auditor\Result;
use GuzzleHttp\Client;

/**
 * Link Checker
 */
class Links implements Test
{
	/**
	 * @var \GuzzleHttp\Client
	 */
	private $client;

	/**
	 * Constructor
	 *
	 * @param   string  $rooturl
	 * @return  void
	 */
	public function __construct($verify = false)
	{
		$this->client = new Client();
		$this->client->setDefaultOption('verify', $verify);
	}

	/**
	 * Test name
	 *
	 * @return  string
	 */
	public function name()
	{
		return 'Link Checker';
	}

	/**
	 * Checks if a string is spam or not
	 *
	 * @param   mixed   $data     string|array
	 * @param   array   $options
	 * @return  object
	 */
	public function examine(array $data, array $options = array())
	{
		$path   = '';
		$status = 0;
		$meta   = array();

		if (isset($data['path']) && $data['path'])
		{
			$path = ltrim($data['path'], '/');
			$path = trim($path);

			$meta['field'] = $data['path'];

			$status = -1;

			if ($this->isLink($path))
			{
				try
				{
					$response = $this->client->head($path, [
						'exceptions' => false,
						'timeout'    => 10
					]);

					$meta['code'] = $response->getStatusCode();

					if ($response->getStatusCode() == 200)
					{
						$status = 1;
					}
				}
				catch (\Exception $e)
				{
					$meta['error'] = $e->getMessage();
				}
			}
			else
			{
				$params = \Component::params('com_resources');
				$base = $params->get('uploadpath', '/site/resources');
				$base = PATH_APP . DS . trim($base, DS) . DS;

				if (is_dir($base . $path))
				{
					$meta['error'] = 'Path is a directory';
				}

				if (file_exists($base . $path))
				{
					$status = 1;
				}
			}
		}

		$result = new Result();
		$result->set([
			'scope_id' => $data['id'],
			'status'   => $status,
			'notes'    => json_encode($meta)
		]);

		return $result;
	}

	/**
	 * Check if a string is a valid link
	 *
	 * @param   string  $text
	 * @return  boolean
	 */
	private function isLink($text)
	{
		if (preg_match("/^(http|https|ftp|ftps).*$/", $text))
		{
			if (filter_var($text, FILTER_VALIDATE_URL))
			{
				return true;
			}
		}

		return false;
	}
}
