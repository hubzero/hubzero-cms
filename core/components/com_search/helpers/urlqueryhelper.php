<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Helpers;
use ReflectionClass;
use Hubzero\Search\Searchable;

/**
 * Solr helper class
 */
class UrlQueryHelper
{
	public static function buildQueryString($url, $values, $excludes = array())
	{
		$queryString = '';
		if (is_string($excludes))
		{
			$excludes = array($excludes);
		}
		foreach ($values as $type => $fields)
		{
			if (in_array($type, $excludes))
			{
				continue;
			}
			foreach ($fields as $field => $value)
			{
				$queryString .= '&childTerms[' . $type . '][' . $field . ']=' . $value;
			}
		}
		return $url . $queryString;
	}
}
