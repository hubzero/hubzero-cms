<?php
/**
 * @package		HUBzero CMS
 * @author		Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright	Copyright 2010 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-3.0.html GPLv3
 *
 * Copyright 2010 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 3 as 
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

class Hubzero_Password_CharacterClass
{
	static $classes = null;

	private function init()
	{
        $db = &JFactory::getDBO();

        if (empty($db))
        {
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

        if (empty($db))
		{
            return $result;
        }

		if (empty(self::$classes))
		{
			self::init();
		}

		if (empty(self::$classes))
		{
			return $result;
		}

		if (count($char) == 0)
		{
			$char = chr(0);
		}

		$char = $char{0};

		foreach(self::$classes as $class)
		{
			if (preg_match("/" . $class->regex . "/", $char))
			{
				$match = new stdClass();
				$match->name = $class->name;
				$match->flag = $class->flag;
				$result[] = $match;
			}
		}	

		return $result;
	}
}
