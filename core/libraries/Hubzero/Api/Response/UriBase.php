<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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

		return rtrim(str_replace('/api', '', $request->base()), '/') . '/' . ltrim($value, '/');
	}
}