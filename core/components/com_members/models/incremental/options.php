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
 * @author    Steve Snyder <snyder13@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Members\Models\Incremental;

use Module;
use User;
use App;

/**
 * Class for incremental registration options
 */
class Options
{
	/**
	 * Database connection
	 *
	 * @var  object
	 */
	private static $current = NULL;

	/**
	 * Get award value per field
	 *
	 * @return  integer
	 */
	public function getAwardPerField()
	{
		$cur = self::getCurrent();
		return $cur['award_per'];
	}

	/**
	 * Check if enabled
	 *
	 * @param   integer  $uid
	 * @return  boolean
	 */
	public function isEnabled($uid = NULL)
	{
		// What is the next line for?
		//$dbg = isset($_GET['dbg']);
		if (!$uid)
		{
			$uid = (int)User::get('id');
		}
		if (!$uid || !Module::isEnabled('incremental_registration'))
		{
			return false;
		}

		$dbh = App::get('db');
		$dbh->setQuery('SELECT `activation` FROM `#__users` WHERE `id` = ' . $uid);
		if ($dbh->loadResult() < 0)
		{
			return false;
		}

		$cur = self::getCurrent();
		if (!$cur['test_group'])
		{
			return true;
		}

		$dbh->setQuery(
			'SELECT 1 FROM `#__xgroups_members` xme WHERE xme.gidNumber = ' . $cur['test_group'] . ' AND xme.uidNumber = ' . $uid . '
			UNION SELECT 1 FROM #__xgroups_managers xma WHERE xma.gidNumber = ' . $cur['test_group'] . ' AND xma.uidNumber = ' . $uid . ' LIMIT 1'
		);
		return (bool)$dbh->loadResult();
	}

	/**
	 * Check if the curl enabled
	 *
	 * @param   integer  $uid
	 * @return  boolean
	 */
	public function isCurlEnabled($uid = NULL)
	{
		if (!$this->isEnabled($uid))
		{
			return false;
		}

		$uid = $uid ?: (int)User::get('id');

		$dbh = App::get('db');
		$dbh->setQuery('SELECT edited_profile FROM `#__profile_completion_awards` WHERE user_id = ' . $uid);
		return !$dbh->loadResult();
	}

	/**
	 * Get the database connection
	 *
	 * @return  object
	 */
	private static function getCurrent()
	{
		if (!self::$current)
		{
			$dbh = App::get('db');
			$dbh->setQuery('SELECT * FROM `#__incremental_registration_options` ORDER BY added DESC LIMIT 1');
			self::$current = $dbh->loadAssoc();
		}
		return self::$current;
	}
}
