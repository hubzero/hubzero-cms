<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Api\Response;

use Hubzero\Base\Middleware;
use Hubzero\Http\Request;

/**
 * Expander Response Modifier
 */
class UriBase extends Middleware
{
	/**
	 * Array that normalizes all the different keys we use for
	 * the different objects
	 *
	 * @var  array
	 */
	private $keys = array(
		'uri',
		'url'
	);

	/**
	 * Handle request in HTTP stack
	 *
	 * @param   objct  $request  HTTP Request
	 * @return  mixes
	 */
	public function handle(Request $request)
	{
		$response = $this->next($request);

		if ($content = $response->original)
		{
			$content = $this->traverse($request, $content);

			$response->setContent($content);
		}

		return $response;
	}

	/**
	 * Look for keys in data and convert found values
	 *
	 * @param   object  $request
	 * @param   mixed   $data
	 * @return  mixed
	 */
	private function traverse($request, $data)
	{
		if (is_array($data))
		{
			foreach ($data as $key => $value)
			{
				$data[$key] = $this->convert($request, $key, $value);
			}
		}
		else if (is_object($data))
		{
			foreach (array_keys(get_object_vars($data)) as $key)
			{
				$data->$key = $this->convert($request, $key, $data->$key);
			}
		}

		return $data;
	}

	/**
	 * Convert a URI
	 *
	 * @param   object  $request
	 * @param   string  $key
	 * @param   mixed   $value
	 * @return  mixed
	 */
	private function convert($request, $key, $value)
	{
		if (is_array($value) || is_object($value))
		{
			return $this->traverse($request, $value);
		}

		if (!in_array($key, $this->keys, true))
		{
			return $value;
		}

		if (substr($value, 0, 4) == 'http')
		{
			return $value;
		}

		return rtrim(str_replace('/api', '', $request->root()), '/') . '/' . ltrim($value, '/');
	}
}
