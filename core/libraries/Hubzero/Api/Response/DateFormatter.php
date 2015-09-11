<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Api\Response;

use Hubzero\Base\Middleware;
use Hubzero\Http\Request;
use Hubzero\Utility\Date;

/**
 * Date Response Modifier
 */
class DateFormatter extends Middleware
{
	/**
	 * Keys we want to swap out for properly formatted dates
	 * 
	 * @var  array
	 */
	private $dateKeys = array('created', 'modified');

	/**
	 * Handle request in HTTP stack
	 * 
	 * @param   objct  $request  HTTP Request
	 * @return  mixes
	 */
	public function handle(Request $request)
	{
		// execute response
		$response = $this->next($request);

		// only do on json data
		if (!$response->isJson())
		{
			return $response;
		}

		// get the response content and json decode
		$content = json_decode($response->getContent());

		// get date keys from response options
		$this->dateKeys = array_merge(
			$this->dateKeys,
			$response->getTransformKeys('dates')
		);

		// make sure to handle array different then a single object
		if (is_array($content))
		{
			// loop through each item in array and covert dates
			foreach ($content as $key => $value)
			{
				$content[$key] = $this->convertDateKeysInObjects($value);
			}
		}
		else
		{
			// convert single object dates
			$content = $this->convertDateKeysInObjects($content);
		}

		// set the response content to modified content
		$response->setContent(json_encode($content));

		// return response
		return $response;
	}

	/**
	 * Convert dates from SQL format to ISO 8601
	 *  
	 * @param   mixed  $object  Convert date keys
	 * @return  mixed  Converted object
	 */
	private function convertDateKeysInObjects($object)
	{
		// only hanlde objects
		if (!is_object($object))
		{
			return $object;
		}

		// spin over each key replacing the date with new format
		foreach (array_keys(get_object_vars($object)) as $key)
		{
			if (in_array($key, $this->dateKeys))
			{
				$object->$key = with(new Date($object->$key))->format('Y-m-d\TH:i:s\Z');
			}
		}

		// return object
		return $object;
	}
}