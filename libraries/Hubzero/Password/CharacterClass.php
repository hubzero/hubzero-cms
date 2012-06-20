<?php
/**
 * HUBzero CMS
 *
 * Copyright 2010-2012 Purdue University. All rights reserved.
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
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2010-2012 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

class Hubzero_Password_CharacterClass
{
	static $classes = null;

	private function init()
	{
		$db = &JFactory::getDBO();

		if (empty($db))	{
	    	return false;
		}

		$query = "SELECT id,name,regex,flag FROM #__password_character_class;";

		$db->setQuery($query);

		self::$classes = $db->loadObjectList();
	}

    public function match($char = null)
	{
		$result = array();

		$db = &JFactory::getDBO();

		if (empty($db)) {
		    return $result;
		}

		if (empty(self::$classes)) {
			self::init();
		}

		if (empty(self::$classes)) {
			return $result;
		}

		if (count($char) == 0) {
			$char = chr(0);
		}

		$char = $char{0};

		foreach(self::$classes as $class) {
			if (preg_match("/" . $class->regex . "/", $char)) {
				$match = new stdClass();
				$match->name = $class->name;
				$match->flag = $class->flag;
				$result[] = $match;
			}
		}	

		return $result;
	}
}
