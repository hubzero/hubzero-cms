<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
