<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Html\Parameter\Element;

use Hubzero\Html\Parameter\Element;
use Exception;

/**
 * Renders a select list of Asset Groups
 */
class ContentLanguages extends Select
{
	/**
	 * Element name
	 *
	 * @var  string
	 */
	protected $_name = 'ContentLanguages';

	/**
	 * Get the options for the element
	 *
	 * @param   object  &$node  XMLElement node object containing the settings for the element
	 * @return  array
	 */
	protected function _getOptions(&$node)
	{
		$db = \App::get('db');

		$query = $db->getQuery()
			->select('a.lang_code', 'value')
			->select('a.title', 'title')
			->select('a.title_native')
			->from('#__languages', 'a')
			->where('a.published', '>=', '0')
			->order('a.title', 'asc');

		// Get the options.
		$db->setQuery($query->toString());
		$options = $db->loadObjectList();

		// Check for a database error.
		if ($db->getErrorNum())
		{
			throw new Exception($db->getErrorMsg(), 500);
		}

		// Merge any additional options in the XML definition.
		return array_merge(parent::_getOptions($node), $options);
	}
}
