<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2014 Purdue University. All rights reserved.
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
 * @author    Ben Mollet <bmollet@purdue.edu>
 * @copyright Copyright 2005-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 * @license   MIT License
 */

namespace Hubzero\Utility;

class Inflector
{
	protected static $uncountable_words = array(
		'equipment', 'information', 'rice', 'money',
		'species', 'series', 'fish', 'meta'
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