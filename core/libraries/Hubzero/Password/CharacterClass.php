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

namespace Hubzero\Password;

class CharacterClass
{
	static $classes = null;

	private static function init()
	{
		$classes[] = array('id' => '1', 'name' => 'uppercase', 'regex' => '[A-Z]',                                       'flag' => '1');
		$classes[] = array('id' => '2', 'name' => 'numeric',   'regex' => '[0-9]',                                       'flag' => '1');
		$classes[] = array('id' => '3', 'name' => 'lowercase', 'regex' => '[a-z]',                                       'flag' => '1');
		$classes[] = array('id' => '4', 'name' => 'special',   'regex' => '[!"\'(),-.:;?[`{}#$%&*+<=>@^_|~\]\/\\\]',     'flag' => '1');
		$classes[] = array('id' => '5', 'name' => 'nonalpha',  'regex' => '[!"\'(),\-.:;?[`{}#$%&*+<=>@^_|~\]\/\\\0-9]', 'flag' => '0');
		$classes[] = array('id' => '6', 'name' => 'alpha',     'regex' => '[A-Za-z]',                                    'flag' => '0');

		self::$classes = $classes;
	}

	public static function match($char = null)
	{
		$result = array();

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

		foreach (self::$classes as $class)
		{
			if (preg_match("/" . $class['regex'] . "/", $char))
			{
				$match = new \stdClass();
				$match->name = $class['name'];
				$match->flag = $class['flag'];
				$result[] = $match;
			}
		}

		return $result;
	}
}
