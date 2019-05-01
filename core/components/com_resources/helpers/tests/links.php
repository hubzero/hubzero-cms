<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
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
	 * Property indicating whether or not to use SSL.
	 * @var boolean 
	 */
	private $verify;

	/**
	 * Constructor
	 *
	 * @param   string  $rooturl
	 * @return  void
	 */
	public function __construct($verify = false)
	{
		$this->client = new Client();
		$this->verify = $verify;

		// Guzzle 6 no longer uses this method
		//$this->client->setDefaultOption('verify', $verify);
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
						'timeout'    => 10,
						'verify'	 => $this->verify
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
