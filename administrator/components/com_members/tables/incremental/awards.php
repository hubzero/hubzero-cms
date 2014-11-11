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
 * Class for incremental registration awards
 */
class ModIncrementalRegistrationAwards
{
	/**
	 * Database connection
	 *
	 * @var  object
	 */
	private static $dbh;

	/**
	 * Awards
	 *
	 * @var  array
	 */
	private $awards;

	/**
	 * User ID
	 *
	 * @var  integer
	 */
	private $uid;

	/**
	 * Get the database connection
	 *
	 * @return  object
	 */
	private static function getDbh()
	{
		if (!self::$dbh)
		{
			self::$dbh = JFactory::getDBO();
		}
		return self::$dbh;
	}

	/**
	 * Constructor
	 *
	 * @param   object  $profile
	 * @return  void
	 */
	public function __construct($profile)
	{
		$this->profile = $profile;
		$this->uid = is_integer($profile) ? $profile : (int)$this->profile->get('uidNumber');
		self::getDbh();
		do
		{
			self::$dbh->setQuery('SELECT opted_out, name, orgtype, organization, countryresident, countryorigin, gender, url, reason, race, phone, picture, disability FROM `#__profile_completion_awards` WHERE user_id = ' . $this->uid);
			if (!($this->awards = self::$dbh->loadAssoc()))
			{
				self::$dbh->setQuery('INSERT INTO `#__profile_completion_awards` (user_id) VALUES (' . $this->uid . ')');
				self::$dbh->execute();
			}
		}
		while (!$this->awards);
	}

	/**
	 * Mark an entry as opted out
	 *
	 * @return  void
	 */
	public function optOut()
	{
		self::$dbh->setQuery('UPDATE `#__profile_completion_awards` SET opted_out = opted_out + 1, last_bothered = CURRENT_TIMESTAMP WHERE user_id = ' . $this->uid);
		self::$dbh->execute();
	}

	/**
	 * Mark an entry as opted out
	 *
	 * @return  mixed
	 */
	public function award()
	{
		if (!$this->uid)
		{
			return NULL;
		}
		$opts = new ModIncrementalRegistrationOptions;
		$awardPer = $opts->getAwardPerField();

		$fieldMap = array(
			'name'            => 'Fullname',
			'orgtype'         => 'Employment',
			'organization'    => 'Organization',
			'countryorigin'   => 'Citizenship',
			'countryresident' => 'Residency',
			'gender'          => 'Sex',
			'url'             => 'URL',
			'reason'          => 'Reason',
			'race'            => 'Race',
			'phone'           => 'Phone',
			'disability'      => 'Disability'
		);
		$alreadyComplete = 0;
		$eligible = array();
		$newAmount = 0;
		$completeSql = 'UPDATE `#__profile_completion_awards` SET edited_profile = 1';
		$optedOut = NULL;

		foreach ($this->awards as $k => $complete)
		{
			if ($k === 'opted_out')
			{
				$optedOut = $complete;
				continue;
			}
			if ($complete)
			{
				continue;
			}
			if ($k === 'picture')
			{
				self::$dbh->setQuery('SELECT picture FROM `#__xprofiles` WHERE uidNumber = ' . $this->uid);
				if (self::$dbh->loadResult())
				{
					$completeSql .= ', ' . $k . ' = 1';
					$alreadyComplete += $awardPer;
				}
				else
				{
					$eligible['picture'] = 1;
				}
				continue;
			}
			$regField = $fieldMap[$k];
			if ((bool)$this->profile->get($k))
			{
				$completeSql .= ', ' . $k . ' = 1';
				$alreadyComplete += $awardPer;
			}
			else
			{
				$eligible[$k == 'url' ? 'web' : $k] = 1;
			}
		}

		self::$dbh->setQuery('SELECT SUM(amount) AS amount FROM `#__users_transactions` WHERE type = \'deposit\' AND category = \'registration\' AND uid = ' . $this->uid);
		$prior = self::$dbh->loadResult();
		self::$dbh->setQuery($completeSql . ' WHERE user_id = ' . $this->uid);
		self::$dbh->execute();

		if ($alreadyComplete)
		{
			self::$dbh->setQuery('SELECT COALESCE((SELECT balance FROM `#__users_transactions` WHERE uid = ' . $this->uid . ' AND id = (SELECT MAX(id) FROM `#__users_transactions` WHERE uid = ' . $this->uid . ')), 0)');
			$newAmount = self::$dbh->loadResult() + $alreadyComplete;

			$BTL = new \Hubzero\Bank\Teller(self::$dbh, $this->uid);
			$BTL->deposit($alreadyComplete, 'Profile completion award', 'registration', 0);
		}

		return array(
			'prior'     => $prior,
			'new'       => $alreadyComplete,
			'eligible'  => $eligible,
			'opted_out' => $optedOut
		);
	}
}
