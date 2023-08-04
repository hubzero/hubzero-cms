<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Html\Builder;

use Hubzero\Error\Exception\RuntimeException;
use Hubzero\Base\Obj;
use Lang;
use App;

/**
 * Utility class working with content language select lists
 */
class ContentLanguage
{
	/**
	 * Cached array of the content language items.
	 *
	 * @var  array
	 */
	protected static $items = null;

	/**
	 * Get a list of the available content language items.
	 *
	 * @param   boolean  $all        True to include All (*)
	 * @param   boolean  $translate  True to translate All
	 * @return  string
	 */
	public static function existing($all = false, $translate = false)
	{
		if (empty(self::$items))
		{
			// Get the database object and a new query object.
			$db = App::get('db');

			// Build the query.
			$query = $db->getQuery()
				->select('a.lang_code', 'value')
				->select('a.title', 'text')
				->select('a.title_native')
				->from('#__languages', 'a')
				->where('a.published', '>=', '0')
				->order('a.title', 'asc');

			// Set the query and load the options.
			$db->setQuery($query->toString());
			self::$items = $db->loadObjectList();
			if ($all)
			{
				array_unshift(self::$items, new Obj(array('value' => '*', 'text' => $translate ? Lang::alt('JALL', 'language') : 'JALL_LANGUAGE')));
			}

			// Detect errors
			if ($db->getErrorNum())
			{
				throw new RuntimeException($db->getErrorMsg(), 500);
			}
		}
		return self::$items;
	}
}
