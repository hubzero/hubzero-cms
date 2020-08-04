<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
	public static function match($char = '')
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

		if (empty($char))
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
