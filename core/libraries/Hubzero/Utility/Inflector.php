<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @author    Ben Mollet <bmollet@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 * @license   MIT License
 */

namespace Hubzero\Utility;

class Inflector
{
	protected static $uncountable_words = array(
		'equipment', 'information', 'rice', 'money',
		'species', 'series', 'fish', 'meta', 'metadata'
	);

	protected static $plural_rules = array(
		'/^(ox)$/i'                 => '\1\2en',     // ox
		'/([m|l])ouse$/i'           => '\1ice',      // mouse, louse
		'/(matr|vert|ind)ix|ex$/i'  => '\1ices',     // matrix, vertex, index
		'/(x|ch|ss|sh)$/i'          => '\1es',       // search, switch, fix, box, process, address
		'/([^aeiouy]|qu)y$/i'       => '\1ies',      // query, ability, agency
		'/(hive)$/i'                => '\1s',        // archive, hive
		'/(?:([^f])fe|([lr])f)$/i'  => '\1\2ves',    // half, safe, wife
		'/sis$/i'                   => 'ses',        // basis, diagnosis
		'/([ti])um$/i'              => '\1a',        // datum, medium
		'/(p)erson$/i'              => '\1eople',    // person, salesperson
		'/(m)an$/i'                 => '\1en',       // man, woman, spokesman
		'/(c)hild$/i'               => '\1hildren',  // child
		'/(buffal|tomat)o$/i'       => '\1\2oes',    // buffalo, tomato
		'/(bu|campu)s$/i'           => '\1\2ses',    // bus, campus
		'/(alias|status|virus)$/i'  => '\1es',       // alias
		'/(octop)us$/i'             => '\1i',        // octopus
		'/(ax|cris|test)is$/i'      => '\1es',       // axis, crisis
		'/s$/'                     => 's',          // no change (compatibility)
		'/$/'                      => 's',
	);

	protected static $singular_rules = array(
		'/(matr)ices$/i'         => '\1ix',
		'/(vert|ind)ices$/i'     => '\1ex',
		'/^(ox)en/i'             => '\1',
		'/(alias)es$/i'          => '\1',
		'/([octop|vir])i$/i'     => '\1us',
		'/(cris|ax|test)es$/i'   => '\1is',
		'/(shoe)s$/i'            => '\1',
		'/(o)es$/i'              => '\1',
		'/(bus|campus)es$/i'     => '\1',
		'/([m|l])ice$/i'         => '\1ouse',
		'/(x|ch|ss|sh)es$/i'     => '\1',
		'/(m)ovies$/i'           => '\1\2ovie',
		'/(s)eries$/i'           => '\1\2eries',
		'/([^aeiouy]|qu)ies$/i'  => '\1y',
		'/([lr])ves$/i'          => '\1f',
		'/(tive)s$/i'            => '\1',
		'/(hive)s$/i'            => '\1',
		'/([^f])ves$/i'          => '\1fe',
		'/(^analy)ses$/i'        => '\1sis',
		'/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/i' => '\1\2sis',
		'/([ti])a$/i'            => '\1um',
		'/(p)eople$/i'           => '\1\2erson',
		'/(m)en$/i'              => '\1an',
		'/(s)tatuses$/i'         => '\1\2tatus',
		'/(c)hildren$/i'         => '\1\2hild',
		'/(n)ews$/i'             => '\1\2ews',
		'/([^us])s$/i'           => '\1',
	);

	/**
	 * Gets the plural version of the given word
	 *
	 * @param   string  the word to pluralize
	 * @param   int     number of instances
	 * @return  string  the plural version of $word
	 */
	public static function pluralize($word, $count = 0)
	{
		$result = strval($word);

		// If a counter is provided, and that equals 1
		// return as singular.
		if ($count === 1)
		{
			return $result;
		}

		if ( ! static::is_countable($result))
		{
			return $result;
		}

		foreach (static::$plural_rules as $rule => $replacement)
		{
			if (preg_match($rule, $result))
			{
				$result = preg_replace($rule, $replacement, $result);
				break;
			}
		}

		return $result;
	}

	/**
	 * Checks if the given word has a plural version.
	 *
	 * @param   string  the word to check
	 * @return  bool    if the word is countable
	 */
	public static function is_countable($word)
	{
		return ! (\in_array(strtolower(\strval($word)), static::$uncountable_words));
	}
}