<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
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
	private static $dbh = null;

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
			$colNames[] = self::$dbh->quote($col['field']);
		}

		self::$dbh->setQuery('SELECT profile_key, profile_value FROM `#__user_profiles` WHERE user_id = ' . $uid . ' AND profile_key IN ('.implode(', ', $colNames).')');
		$data = self::$dbh->loadAssocList();
		$profile = array();
		foreach ($data as $datum)
		{
			$profile[$datum['profile_key']] = $datum['profile_value'];
		}
		self::$dbh->setQuery('SELECT name, sendEmail AS mailPreferenceOption FROM `#__users` WHERE id = ' . $uid);
		$data = self::$dbh->loadAssoc();
		foreach ($data as $key => $datum)
		{
			$profile[$key] = $datum;
		}

		$neededCols = array();
		$nonUS = false;
		foreach ($cols as $col)
		{
			// Was the field found in the results?
			// If so, then the user filled it out
			if (array_key_exists($col['field'], $profile))
			{
				continue;
			}
			if ($col['field'] == 'sendEmail')
			{
				$col['field'] = 'mailPreferenceOption';
			}
			// We need to handle this one a little differently
			if ($col['field'] == 'mailPreferenceOption')
			{
				if ($profile[$col['field']] == -1)
				{
					$neededCols['mailPreferenceOption'] = $col['label'];
				}
				continue;
			}
			// Default to the label value
			if (!isset($profile[$col['field']]) || !trim($profile[$col['field']]))
			{
				$neededCols[$col['field']] = $col['label'];
			}
		}

		return array_slice($neededCols, 0, 4);
	}
}
