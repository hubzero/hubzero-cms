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

use App;

/**
 * Class for incremental registration groups
 */
class Groups
{
	/**
	 * Possible columns
	 *
	 * @var  array
	 */
	private static $possibleCols = array();

	/**
	 * Database connection
	 *
	 * @var  object
	 */
	private static $dbh = NULL;

	/**
	 * Constructor
	 *
	 * @return  void
	 */
	public function __construct()
	{
		if (!self::$dbh)
		{
			self::$dbh = App::get('db');
		}
		if (!self::$possibleCols)
		{
			self::$dbh->setQuery('SELECT field, label FROM `#__incremental_registration_labels` ORDER BY label');
			self::$possibleCols = array_map(
				function($v)
				{
					return $v['label'];
				},
				self::$dbh->loadAssocList('field')
			);
		}
	}

	/**
	 * Get all groups
	 *
	 * @return  array
	 */
	public function getAllGroups()
	{
		$groups = array();
		self::$dbh->setQuery(
			'SELECT group_id, hours, irl.field
			FROM `#__incremental_registration_groups` irg
			LEFT JOIN `#__incremental_registration_group_label_rel` irglr ON irglr.group_id = irg.id
			INNER JOIN `#__incremental_registration_labels` irl ON irl.id = irglr.label_id
			ORDER BY hours'
		);
		foreach (self::$dbh->loadAssocList() as $row)
		{
			if (!isset($groups[$row['group_id']]))
			{
				$groups[$row['group_id']] = array(
					'hours' => $row['hours'],
					'cols'  => array()
				);
			}
			$groups[$row['group_id']]['cols'][] = $row['field'];
		}
		return array_values($groups);
	}

	/**
	 * Get all possible columns
	 *
	 * @return  array
	 */
	public function getPossibleColumns()
	{
		return self::$possibleCols;
	}

	/**
	 * Get active columns for a user
	 *
	 * @param   integer  $uid
	 * @return  array
	 */
	public function getActiveColumns($uid)
	{
		$uid = (int)$uid;
		self::$dbh->setQuery('SELECT irl.* FROM `#__incremental_registration_groups` irg LEFT JOIN `#__incremental_registration_group_label_rel` irglr ON irglr.group_id = irg.id INNER JOIN `#__incremental_registration_labels` irl ON irl.id = irglr.label_id WHERE irg.hours <= (SELECT (unix_timestamp(current_timestamp) - unix_timestamp(registerDate))/60/60 AS hours FROM `#__users` WHERE id = ' . $uid . ')');
		$cols = self::$dbh->loadAssocList();
		// no data we want, don't bother
		if (!$cols)
		{
			return array();
		}

		self::$dbh->setQuery('SELECT coalesce((SELECT opted_out FROM `#__profile_completion_awards` WHERE user_id = ' . $uid . '), 0)');
		$times = self::$dbh->loadResult();
		if ($times > 0)
		{
			self::$dbh->setQuery('SELECT (unix_timestamp() - unix_timestamp(last_bothered))/60/60 > (SELECT coalesce((SELECT hours FROM `#__incremental_registration_popover_recurrence` WHERE idx = ' . ($times - 1) . '), (SELECT hours FROM `#__incremental_registration_popover_recurrence` ORDER BY idx DESC LIMIT 1))) FROM `#__profile_completion_awards` WHERE user_id = ' . $uid);
			// opt-out period is in effect
			if (!self::$dbh->loadResult())
			{
				return array();
			}
		}

		$colNames = array();
		$wantRace = false;
		$wantDisability = false;
		foreach ($cols as $col)
		{
			if ($col['field'] == 'race')
			{
				$wantRace = true;
				continue;
			}
			if ($col['field'] == 'disability')
			{
				$wantDisability = true;
				continue;
			}
			$colNames[] = $col['field'];
		}
		self::$dbh->setQuery('SELECT '.implode(', ', $colNames).' FROM `#__xprofiles` WHERE uidNumber = ' . $uid);
		$profile = self::$dbh->loadAssoc();
		$neededCols = array();
		$nonUS = false;
		foreach ($cols as $col)
		{
			if (!array_key_exists($col['field'], $profile))
			{
				continue;
			}
			if ($col['field'] == 'mailPreferenceOption')
			{
				if ($profile[$col['field']] == -1)
				{
					$neededCols['mailPreferenceOption'] = $col['label'];
				}
				continue;
			}
			if (!trim($profile[$col['field']]))
			{
				$neededCols[$col['field']] = $col['label'];
			}
		}
		if ($wantRace)
		{
			self::$dbh->setQuery('SELECT countryorigin FROM `#__xprofiles` WHERE uidNumber = ' . $uid);
			if (!($country = self::$dbh->loadResult()) || strtolower($country) == 'us')
			{
				self::$dbh->setQuery('SELECT COUNT(*) FROM `#__xprofiles_race` WHERE uidNumber = ' . $uid);
				if (!self::$dbh->loadResult())
				{
					$neededCols['race'] = 'Race';
				}
			}
		}
		if ($wantDisability)
		{
			self::$dbh->setQuery('SELECT 1 FROM `#__xprofiles_disability` WHERE uidNumber = ' . $uid . ' LIMIT 1');
			if (!self::$dbh->loadResult())
			{
				$neededCols['disability'] = 'Disability';
			}
		}
		return $neededCols;
	}
}
