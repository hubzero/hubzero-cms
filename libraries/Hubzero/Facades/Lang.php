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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Facades;

/**
 * Language helper facade
 */
class Lang extends Facade
{
	/**
	 * Get the registered name of the component.
	 *
	 * @return  string
	 */
	protected static function getAccessor()
	{
		return 'language';
	}

	/**
	 * Translates a string into the current language.
	 *
	 * @param   string  $string  The string to translate.
	 * @return  string  The translated string or the key is $script is true
	 */
	public static function txt($string)
	{
		$args = func_get_args();
		if (count($args) > 1)
		{
			return call_user_func_array(array('\JText', 'sprintf'), $args);
		}
		return \JText::_($string);
	}

	/**
	 * Translates a string into the current language.
	 *
	 * @param   string   $string  The format string.
	 * @param   integer  $n       The number of items
	 * @return  string   The translated string or the key is $script is true
	 */
	public static function txts($string, $n)
	{
		$args = func_get_args();
		return call_user_func_array(array('\JText', 'plural'), $args);
	}
}