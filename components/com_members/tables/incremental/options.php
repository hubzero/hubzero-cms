<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @author    Steve Snyder <snyder13@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Class for incremental registration options
 */
class ModIncrementalRegistrationOptions
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
		$dbg = isset($_GET['dbg']);
		if (!$uid)
		{
			$uid = (int)JFactory::getUser()->get('id');
		}
		if (!$uid || !JModuleHelper::isEnabled('incremental_registration'))
		{
			return false;
		}

		$dbh = JFactory::getDBO();
		$dbh->setQuery('SELECT emailConfirmed FROM `#__xprofiles` WHERE uidNumber = ' . $uid);
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

		$uid = $uid ?: (int)JFactory::getUser()->get('id');

		$dbh = JFactory::getDBO();
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
			$dbh = JFactory::getDBO();
			$dbh->setQuery('SELECT * FROM `#__incremental_registration_options` ORDER BY added DESC LIMIT 1');
			self::$current = $dbh->loadAssoc();
		}
		return self::$current;
	}
}
