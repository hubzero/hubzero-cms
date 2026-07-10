<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Massmail\Helpers;

use Components\Massmail\Models\Mailing;

/**
 * Expands a mailing's recipient spec into per-recipient rows.
 *
 * Everything is done with INSERT ... SELECT so a 100k-user expansion is a
 * single DB operation (no PHP hydration), and the UNIQUE (mailing_id, email)
 * key + INSERT IGNORE dedupes automatically across overlapping selections.
 *
 * Hard exclusions ALWAYS applied (even when opt-out is overridden):
 *   - blocked accounts         (block = 1)
 *   - never-activated accounts (activation <= 0; negative = pending token)
 *   - accounts with no email    (email = '')
 */
class Recipients
{
	/**
	 * The non-negotiable "this address is mailable at all" predicate.
	 *
	 * @return  string
	 */
	protected static function hardFilter()
	{
		$sql = "u.block = 0 AND u.activation > 0 AND u.email <> ''";

		// Optional: also require the account-approval workflow to have passed.
		// Off by default (honor the operator's blocked/activation/email spec).
		$params = \Component::params('com_massmail');
		if ((int) $params->get('require_approved', 0))
		{
			$sql .= " AND u.approved > 0";
		}
		return $sql;
	}

	/**
	 * The opt-out / confirmation predicate (skipped entirely when overriding).
	 *
	 * @param   boolean  $ignoreOptout
	 * @return  string
	 */
	protected static function preferenceFilter($ignoreOptout)
	{
		$params = \Component::params('com_massmail');
		$sql    = '';

		if (!$ignoreOptout)
		{
			if ((int) $params->get('no_preference_sendable', 1))
			{
				// Exclude only explicit opt-outs; "-1" (no preference) is mailable.
				$sql .= " AND COALESCE(x.mailPreferenceOption, -1) <> 0";
			}
			else
			{
				// Only users who explicitly opted in.
				$sql .= " AND COALESCE(x.mailPreferenceOption, -1) = 1";
			}
		}

		if ((int) $params->get('require_email_confirmed', 0))
		{
			$sql .= " AND COALESCE(x.emailConfirmed, 0) > 0";
		}
		return $sql;
	}

	/**
	 * Expand a mailing's recipients into #__massmail_recipients.
	 *
	 * @param   Mailing  $mailing
	 * @return  integer  total recipients queued
	 */
	public static function expand(Mailing $mailing)
	{
		$db  = \App::get('db');
		$mid = (int) $mailing->get('id');

		$spec = json_decode((string) $mailing->get('recipient_spec'), true);
		if (!is_array($spec))
		{
			$spec = array();
		}

		$ignoreOptout = (int) $mailing->get('ignore_optout') ? true : false;
		$hard = self::hardFilter();
		$pref = self::preferenceFilter($ignoreOptout);

		$type = isset($spec['type']) ? $spec['type'] : '';

		// --- All active users -------------------------------------------------
		if ($type == 'all' || !empty($spec['all']))
		{
			$db->setQuery(
				"INSERT IGNORE INTO `#__massmail_recipients` (mailing_id, uid, email, name, status)
				 SELECT $mid, u.id, u.email, u.name, 0
				 FROM `#__users` u
				 LEFT JOIN `#__xprofiles` x ON x.uidNumber = u.id
				 WHERE $hard $pref"
			);
			$db->execute();
		}

		// --- Members of one or more groups -----------------------------------
		$gids = isset($spec['groups']) ? self::resolveGroups($spec['groups']) : array();
		if ($gids)
		{
			$in = implode(',', $gids);
			$db->setQuery(
				"INSERT IGNORE INTO `#__massmail_recipients` (mailing_id, uid, email, name, status)
				 SELECT $mid, u.id, u.email, u.name, 0
				 FROM `#__users` u
				 JOIN `#__xgroups_members` gm ON gm.uidNumber = u.id
				 LEFT JOIN `#__xprofiles` x ON x.uidNumber = u.id
				 WHERE gm.gidNumber IN ($in) AND $hard $pref"
			);
			$db->execute();
		}

		// --- Specific users ---------------------------------------------------
		$uids = isset($spec['users']) ? array_filter(array_map('intval', (array) $spec['users'])) : array();
		if ($uids)
		{
			$in = implode(',', $uids);
			$db->setQuery(
				"INSERT IGNORE INTO `#__massmail_recipients` (mailing_id, uid, email, name, status)
				 SELECT $mid, u.id, u.email, u.name, 0
				 FROM `#__users` u
				 LEFT JOIN `#__xprofiles` x ON x.uidNumber = u.id
				 WHERE u.id IN ($in) AND $hard $pref"
			);
			$db->execute();
		}

		// --- Raw addresses (admin-entered; sent as typed) --------------------
		$emails = isset($spec['emails']) ? (array) $spec['emails'] : array();
		foreach ($emails as $em)
		{
			$em = trim($em);
			if ($em === '' || !filter_var($em, FILTER_VALIDATE_EMAIL))
			{
				continue;
			}
			$db->setQuery(
				"INSERT IGNORE INTO `#__massmail_recipients` (mailing_id, uid, email, name, status)
				 VALUES ($mid, 0, " . $db->quote($em) . ", '', 0)"
			);
			$db->execute();
		}

		$db->setQuery("SELECT COUNT(*) FROM `#__massmail_recipients` WHERE mailing_id = $mid");
		return (int) $db->loadResult();
	}

	/**
	 * Count active users who WOULD be reached, without queuing them.
	 * Used to preview a mailing before launch.
	 *
	 * @param   array    $spec
	 * @param   boolean  $ignoreOptout
	 * @return  integer
	 */
	public static function previewCount($spec, $ignoreOptout = false)
	{
		$db   = \App::get('db');
		$hard = self::hardFilter();
		$pref = self::preferenceFilter($ignoreOptout);
		$type = isset($spec['type']) ? $spec['type'] : '';

		if ($type == 'all' || !empty($spec['all']))
		{
			$db->setQuery(
				"SELECT COUNT(*) FROM `#__users` u
				 LEFT JOIN `#__xprofiles` x ON x.uidNumber = u.id
				 WHERE $hard $pref"
			);
			return (int) $db->loadResult();
		}

		$gids = isset($spec['groups']) ? self::resolveGroups($spec['groups']) : array();
		if ($gids)
		{
			$in = implode(',', $gids);
			$db->setQuery(
				"SELECT COUNT(DISTINCT u.id) FROM `#__users` u
				 JOIN `#__xgroups_members` gm ON gm.uidNumber = u.id
				 LEFT JOIN `#__xprofiles` x ON x.uidNumber = u.id
				 WHERE gm.gidNumber IN ($in) AND $hard $pref"
			);
			return (int) $db->loadResult();
		}

		$uids = isset($spec['users']) ? array_filter(array_map('intval', (array) $spec['users'])) : array();
		if ($uids)
		{
			$in = implode(',', $uids);
			$db->setQuery(
				"SELECT COUNT(*) FROM `#__users` u
				 LEFT JOIN `#__xprofiles` x ON x.uidNumber = u.id
				 WHERE u.id IN ($in) AND $hard $pref"
			);
			return (int) $db->loadResult();
		}

		$emails = isset($spec['emails']) ? (array) $spec['emails'] : array();
		$n = 0;
		foreach ($emails as $em)
		{
			if (filter_var(trim($em), FILTER_VALIDATE_EMAIL))
			{
				$n++;
			}
		}
		return $n;
	}

	/**
	 * Resolve a list of group tokens -- numeric group IDs and/or cn aliases --
	 * to group IDs. Lets the picker accept human-friendly aliases.
	 *
	 * @param   array  $tokens
	 * @return  array  group IDs
	 */
	public static function resolveGroups($tokens)
	{
		$tokens = array_filter(array_map('trim', (array) $tokens), 'strlen');
		if (!$tokens)
		{
			return array();
		}

		$db    = \App::get('db');
		$ids   = array();
		$names = array();
		foreach ($tokens as $t)
		{
			if (ctype_digit((string) $t)) { $ids[] = (int) $t; }
			else { $names[] = $db->quote($t); }
		}

		$where = array();
		if ($ids)   { $where[] = 'gidNumber IN (' . implode(',', $ids) . ')'; }
		if ($names) { $where[] = 'cn IN (' . implode(',', $names) . ')'; }
		if (!$where)
		{
			return array();
		}

		$db->setQuery("SELECT gidNumber FROM `#__xgroups` WHERE " . implode(' OR ', $where));
		return array_map('intval', (array) $db->loadColumn());
	}
}
