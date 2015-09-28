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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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