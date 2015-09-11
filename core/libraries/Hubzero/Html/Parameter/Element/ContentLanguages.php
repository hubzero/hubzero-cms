<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @copyright Copyright 2005-2014 Open Source Matters, Inc.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GPLv2
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
		$query = $db->getQuery(true);

		$query->select('a.lang_code AS value, a.title AS text, a.title_native');
		$query->from('#__languages AS a');
		$query->where('a.published >= 0');
		$query->order('a.title');

		// Get the options.
		$db->setQuery($query);
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
