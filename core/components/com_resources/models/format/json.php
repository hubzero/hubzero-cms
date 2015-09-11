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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Resources\Models\Format;

use Components\Resources\Models\Format as Base;
use stdClass;

/**
 * JSON format handler
 */
class JSON extends Base
{
	/**
	 * Converts an object into a JSON formatted string.
	 *
	 * @param   object  $object   Data source object.
	 * @param   array   $options  Options used by the formatter.
	 * @return  string  JSON formatted string.
	 */
	public function objectToString($object, $options = array())
	{
		return json_encode($object);
	}

	/**
	 * Parse a JSON formatted string and convert it into an object.
	 *
	 * If the string is not in JSON format, this method will attempt to parse it as INI format.
	 *
	 * @param   string  $data     JSON formatted string to convert.
	 * @param   array   $options  Options used by the formatter.
	 * @return  object   Data object.
	 */
	public function stringToObject($data, $options = array('processSections' => false))
	{
		// Fix legacy API.
		if (is_bool($options))
		{
			$options = array('processSections' => $options);

			// Deprecation warning.
			\JLog::add('ResourcesElementsFormatJSON::stringToObject() second argument should not be a boolean.', \JLog::WARNING, 'deprecated');
		}

		$data = trim($data);
		if ((substr($data, 0, 1) != '{') && (substr($data, -1, 1) != '}'))
		{
			$ini = Base::getInstance('INI');
			$obj = $ini->stringToObject($data, $options);
		}
		else
		{
			$obj = json_decode($data);
		}
		return $obj;
	}
}
