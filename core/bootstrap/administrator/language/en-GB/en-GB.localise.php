<?php
/**
 * HUBzero CMS
 *
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * en-GB localise class
 */
abstract class en_GBLocalise
{
	/**
	 * Returns the potential suffixes for a specific number of items
	 *
	 * @param   integer  $count  The number of items.
	 * @return  array    An array of potential suffixes.
	 */
	public static function getPluralSuffixes($count)
	{
		if ($count == 0)
		{
			$return =  array('0');
		}
		elseif ($count == 1)
		{
			$return =  array('1');
		}
		else
		{
			$return = array('MORE');
		}
		return $return;
	}

	/**
	 * Returns the ignored search words
	 *
	 * @return  array  An array of ignored search words.
	 */
	public static function getIgnoredSearchWords()
	{
		$search_ignore = array(
			'and',
			'in',
			'on'
		);

		return $search_ignore;
	}

	/**
	 * Returns the lower length limit of search words
	 *
	 * @return  integer  The lower length limit of search words.
	 */
	public static function getLowerLimitSearchWord()
	{
		return 3;
	}

	/**
	 * Returns the upper length limit of search words
	 *
	 * @return  integer  The upper length limit of search words.
	 */
	public static function getUpperLimitSearchWord()
	{
		return 20;
	}

	/**
	 * Returns the number of chars to display when searching
	 *
	 * @return  integer  The number of chars to display when searching.
	 */
	public static function getSearchDisplayedCharactersNumber()
	{
		return 200;
	}
}
