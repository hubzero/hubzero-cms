<?php
/**
 * HUBzero CMS
 *
 * Copyright 2010-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2010-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Password;

/**
 * Character class helper
 */
class CharacterClass
{
	/**
	 * List of character classes
	 *
	 * @var  array
	 */
	static $classes = null;

	/**
	 * Populate the list of character classes
	 *
	 * @return  void
	 */
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

	/**
	 * Match character class
	 *
	 * @param   string  $char
	 * @return  object
	 */
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
