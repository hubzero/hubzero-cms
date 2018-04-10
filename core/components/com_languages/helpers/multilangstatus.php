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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Languages\Helpers;

use App;

/**
 * Multilang status helper.
 */
abstract class Multilangstatus
{
	/**
	 * Get homes
	 *
	 * @return  integer
	 */
	public static function getHomes()
	{
		// Check for multiple Home pages
		$db = App::get('db');
		$query = $db->getQuery(true);
		$query->select('COUNT(*)');
		$query->from($db->quoteName('#__menu'));
		$query->where('home = 1');
		$query->where('published = 1');
		$query->where('client_id = 0');
		$db->setQuery($query);
		return $db->loadResult();
	}

	/**
	 * Get lang switchers
	 *
	 * @return  integer
	 */
	public static function getLangswitchers()
	{
		// Check if switcher is published
		$db = App::get('db');
		$query = $db->getQuery(true);
		$query->select('COUNT(*)');
		$query->from($db->quoteName('#__modules'));
		$query->where('module = ' . $db->quote('mod_languages'));
		$query->where('published = 1');
		$query->where('client_id = 0');
		$db->setQuery($query);
		return $db->loadResult();
	}

	/**
	 * Get content langs
	 *
	 * @return  array
	 */
	public static function getContentlangs()
	{
		// Check for published Content Languages
		$db = App::get('db');
		$query = $db->getQuery(true);
		$query->select('a.lang_code AS lang_code');
		$query->select('a.published AS published');
		$query->from('#__languages AS a');
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	/**
	 * Get site langs
	 *
	 * @return  array
	 */
	public static function getSitelangs()
	{
		// check for published Site Languages
		$db = App::get('db');
		$query = $db->getQuery(true);
		$query->select('a.element AS element');
		$query->from('#__extensions AS a');
		$query->where('a.type = '.$db->Quote('language'));
		$query->where('a.client_id = 0');
		$db->setQuery($query);
		return $db->loadObjectList('element');
	}

	/**
	 * Get home pages
	 *
	 * @return  array
	 */
	public static function getHomepages()
	{
		// Check for Home pages languages
		$db = App::get('db');
		$query = $db->getQuery(true);
		$query->select('language');
		$query->select('id');
		$query->from($db->quoteName('#__menu'));
		$query->where('home = 1');
		$query->where('published = 1');
		$query->where('client_id = 0');
		$db->setQuery($query);
		return $db->loadObjectList('language');
	}

	/**
	 * Get status
	 *
	 * @return  array
	 */
	public static function getStatus()
	{
		//check for combined status
		$db = App::get('db');
		$query = $db->getQuery(true);

		// Select all fields from the languages table.
		$query->select('a.*', 'l.home');
		$query->select('a.published AS published');
		$query->select('a.lang_code AS lang_code');
		$query->from('#__languages AS a');

		// Select the language home pages
		$query->select('l.home AS home');
		$query->select('l.language AS home_language');
		$query->join('LEFT', '#__menu  AS l  ON  l.language = a.lang_code AND l.home=1 AND l.published=1 AND l.language <> \'*\'' );
		$query->select('e.enabled AS enabled');
		$query->select('e.element AS element');
		$query->join('LEFT', '#__extensions  AS e ON e.element = a.lang_code');
		$query->where('e.client_id = 0');
		$query->where('e.enabled = 1');
		$query->where('e.state = 0');

		$db->setQuery($query);
		return $db->loadObjectList();
	}
}
