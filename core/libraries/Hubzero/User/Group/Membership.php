<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\User\Group;

use Hubzero\User\Group;

/**
 * Time-limited group membership
 *
 * A membership row may carry an optional `expires` date. Absent one, the
 * membership is perpetual, which is how every group behaved before this
 * existed and how every pre-existing row still behaves.
 *
 * Enforcement is by revocation, not by filtering: when a term lapses the row
 * is deleted outright, so the thirty-odd places that read the membership
 * table with raw SQL become correct without needing to know about expiry.
 * The trade-off is that access ends when the reaper next runs rather than on
 * the exact second, which is why nothing here should be relied on as a
 * real-time access gate.
 */
class Membership
{
	/**
	 * Reasons a membership can leave the table
	 */
	const REASON_EXPIRED = 'expired';
	const REASON_MANUAL  = 'manual';
	const REASON_GROUP   = 'group_deleted';
	const REASON_USER    = 'user_deleted';

	/**
	 * Database driver
	 *
	 * @return  object
	 */
	protected static function db()
	{
		return \App::get('db');
	}

	/**
	 * Component params, tolerating a context where com_groups is unavailable
	 *
	 * @param   string  $key
	 * @param   mixed   $default
	 * @return  mixed
	 */
	protected static function param($key, $default = null)
	{
		try
		{
			return \Component::params('com_groups')->get($key, $default);
		}
		catch (\Exception $e)
		{
			return $default;
		}
	}

	/**
	 * Make sure com_groups' strings are available
	 *
	 * The messages raised here are shown to group managers, but this class is
	 * also reached from cron and the API, where the component's language file
	 * has not been loaded and Lang::txt() would hand back the raw key.
	 *
	 * @return  void
	 */
	protected static function loadLanguage()
	{
		static $loaded = false;

		if ($loaded)
		{
			return;
		}

		\Lang::load('com_groups')
			|| \Lang::load('com_groups', PATH_CORE . DS . 'components' . DS . 'com_groups' . DS . 'site');

		$loaded = true;
	}

	/**
	 * A translated message, with the component's strings guaranteed loaded
	 *
	 * @param   string  $key
	 * @param   mixed   $arg
	 * @return  string
	 */
	protected static function txt($key, $arg = null)
	{
		self::loadLanguage();

		return is_null($arg) ? \Lang::txt($key) : \Lang::txt($key, $arg);
	}

	/**
	 * Is the feature switched on for this hub?
	 *
	 * @return  boolean
	 */
	public static function enabled()
	{
		return (bool) self::param('membership_expiration', 0);
	}

	/**
	 * Does the schema carry the term columns yet?
	 *
	 * Lets callers behave sanely on a hub where the code is deployed but the
	 * migration has not been run.
	 *
	 * @return  boolean
	 */
	public static function supported()
	{
		static $supported = null;

		if (is_null($supported))
		{
			$supported = self::db()->tableHasField('#__xgroups_members', 'expires');
		}

		return $supported;
	}

	/**
	 * Normalize whatever a caller hands us into a SQL datetime, or null
	 *
	 * @param   mixed   $date
	 * @return  string|null
	 */
	public static function normalize($date)
	{
		if (is_null($date) || $date === '' || $date === '0000-00-00 00:00:00')
		{
			return null;
		}

		if (is_object($date) && method_exists($date, 'toSql'))
		{
			return $date->toSql();
		}

		if (is_numeric($date))
		{
			return \Date::of((int) $date)->toSql();
		}

		$stamp = strtotime($date);

		if ($stamp === false)
		{
			return null;
		}

		return \Date::of($stamp)->toSql();
	}

	/**
	 * The term on a membership, or null when it is perpetual
	 *
	 * @param   integer  $gidNumber
	 * @param   integer  $uidNumber
	 * @return  string|null
	 */
	public static function expiresFor($gidNumber, $uidNumber)
	{
		if (!self::supported())
		{
			return null;
		}

		$db = self::db();

		$db->setQuery(
			"SELECT `expires` FROM `#__xgroups_members`
			  WHERE `gidNumber`=" . $db->quote((int) $gidNumber) . "
			    AND `uidNumber`=" . $db->quote((int) $uidNumber) . " LIMIT 1"
		);

		$value = $db->loadResult();

		return ($value && $value != '0000-00-00 00:00:00') ? $value : null;
	}

	/**
	 * Every term in a group, as uidNumber => expires
	 *
	 * One query for a whole roster, so a member listing does not have to ask
	 * per row.
	 *
	 * @param   integer  $gidNumber
	 * @return  array
	 */
	public static function termsFor($gidNumber)
	{
		if (!self::supported())
		{
			return array();
		}

		$db = self::db();

		$db->setQuery(
			"SELECT `uidNumber`, `expires` FROM `#__xgroups_members`
			  WHERE `gidNumber`=" . $db->quote((int) $gidNumber) . "
			    AND `expires` IS NOT NULL"
		);

		$rows = $db->loadObjectList() ?: array();
		$out  = array();

		foreach ($rows as $row)
		{
			$out[(int) $row->uidNumber] = $row->expires;
		}

		return $out;
	}

	/**
	 * Is this membership present and not past its term?
	 *
	 * Answered from the database rather than from a Group object, so it is
	 * correct even in the window before the reaper has run.
	 *
	 * @param   integer  $gidNumber
	 * @param   integer  $uidNumber
	 * @return  boolean
	 */
	public static function isActive($gidNumber, $uidNumber)
	{
		$db = self::db();

		$where = self::supported()
			? " AND (`expires` IS NULL OR `expires` > UTC_TIMESTAMP())"
			: "";

		$db->setQuery(
			"SELECT COUNT(*) FROM `#__xgroups_members`
			  WHERE `gidNumber`=" . $db->quote((int) $gidNumber) . "
			    AND `uidNumber`=" . $db->quote((int) $uidNumber) . $where
		);

		return (bool) $db->loadResult();
	}

	/**
	 * Set (or with a null date, clear) the term on a membership
	 *
	 * @param   integer  $gidNumber
	 * @param   integer  $uidNumber
	 * @param   mixed    $date       datetime, timestamp, Date object or null
	 * @param   integer  $actor      user id setting it, defaults to current
	 * @return  boolean
	 * @throws  \InvalidArgumentException
	 */
	public static function setExpiration($gidNumber, $uidNumber, $date, $actor = null)
	{
		if (!self::supported())
		{
			throw new \InvalidArgumentException(self::txt('COM_GROUPS_MEMBERSHIP_EXPIRATION_UNAVAILABLE'));
		}

		$db  = self::db();
		$gid = (int) $gidNumber;
		$uid = (int) $uidNumber;

		$db->setQuery(
			"SELECT COUNT(*) FROM `#__xgroups_members`
			  WHERE `gidNumber`=" . $db->quote($gid) . " AND `uidNumber`=" . $db->quote($uid)
		);

		if (!$db->loadResult())
		{
			throw new \InvalidArgumentException(self::txt('COM_GROUPS_MEMBERSHIP_EXPIRATION_NOT_A_MEMBER'));
		}

		$expires = self::normalize($date);

		if (!is_null($expires))
		{
			// A term already in the past is a removal, and should be done as
			// one so the audit trail and events are right.
			if (strtotime($expires) <= time())
			{
				throw new \InvalidArgumentException(self::txt('COM_GROUPS_MEMBERSHIP_EXPIRATION_IN_PAST'));
			}

			$max = (int) self::param('membership_max_term_days', 0);

			if ($max > 0 && strtotime($expires) > strtotime('+' . $max . ' days'))
			{
				throw new \InvalidArgumentException(self::txt('COM_GROUPS_MEMBERSHIP_EXPIRATION_TOO_LONG', $max));
			}
		}

		if (is_null($actor))
		{
			$actor = (int) \User::get('id');
		}

		// expires_notified is cleared so that extending a term restarts the
		// warning cycle instead of leaving it stamped from the old one
		$db->setQuery(
			"UPDATE `#__xgroups_members`
			    SET `expires`=" . (is_null($expires) ? 'NULL' : $db->quote($expires)) . ",
			        `expires_set_by`=" . ($actor ? $db->quote($actor) : 'NULL') . ",
			        `expires_notified`=NULL
			  WHERE `gidNumber`=" . $db->quote($gid) . "
			    AND `uidNumber`=" . $db->quote($uid)
		);

		if (!$db->query())
		{
			return false;
		}

		self::log(
			$gid,
			$uid,
			is_null($expires) ? 'group_member_term_cleared' : 'group_member_term_set',
			array('uidNumber' => $uid, 'expires' => $expires),
			$actor
		);

		return true;
	}

	/**
	 * Give newly enrolled members the group's default term, if it has one
	 *
	 * Called from Group::update(), which is the single place every enrollment
	 * path goes through - join, approve, invite accept, admin add and the CSV
	 * importer - so none of those has to know about terms.
	 *
	 * Only fills a term that is not already set, so an explicit end date
	 * chosen by a manager is never overwritten by the default.
	 *
	 * @param   integer  $gidNumber
	 * @param   array    $uidNumbers
	 * @return  integer  rows given a term
	 */
	public static function applyDefaultTerm($gidNumber, $uidNumbers)
	{
		if (!self::supported() || !self::enabled() || empty($uidNumbers))
		{
			return 0;
		}

		$db  = self::db();
		$gid = (int) $gidNumber;

		$db->setQuery("SELECT `params` FROM `#__xgroups` WHERE `gidNumber`=" . $db->quote($gid));

		$params = new \Hubzero\Config\Registry($db->loadResult());
		$days   = (int) $params->get('membership_default_term_days', 0);

		if ($days <= 0)
		{
			return 0;
		}

		$max = (int) self::param('membership_max_term_days', 0);

		if ($max > 0 && $days > $max)
		{
			$days = $max;
		}

		$ids = array();

		foreach ((array) $uidNumbers as $uid)
		{
			$uid = (int) $uid;

			if ($uid > 0)
			{
				$ids[] = $uid;
			}
		}

		if (empty($ids))
		{
			return 0;
		}

		$db->setQuery(
			"UPDATE `#__xgroups_members`
			    SET `expires`=(UTC_TIMESTAMP() + INTERVAL " . $days . " DAY)
			  WHERE `gidNumber`=" . $db->quote($gid) . "
			    AND `uidNumber` IN (" . implode(',', $ids) . ")
			    AND `expires` IS NULL"
		);

		if (!$db->query())
		{
			return 0;
		}

		return (int) $db->getAffectedRows();
	}

	/**
	 * Push a term further out (or start one from now)
	 *
	 * @param   integer  $gidNumber
	 * @param   integer  $uidNumber
	 * @param   string   $interval   anything strtotime understands, e.g. '+6 months'
	 * @param   integer  $actor
	 * @return  boolean
	 */
	public static function extend($gidNumber, $uidNumber, $interval, $actor = null)
	{
		$current = self::expiresFor($gidNumber, $uidNumber);

		// extend from the existing term when it is still in the future,
		// otherwise from now
		$from = ($current && strtotime($current) > time()) ? strtotime($current) : time();

		$next = strtotime($interval, $from);

		if ($next === false)
		{
			throw new \InvalidArgumentException(self::txt('COM_GROUPS_MEMBERSHIP_EXPIRATION_BAD_INTERVAL'));
		}

		return self::setExpiration($gidNumber, $uidNumber, \Date::of($next)->toSql(), $actor);
	}

	/**
	 * Memberships whose term has lapsed, oldest first
	 *
	 * @param   integer  $limit
	 * @return  array
	 */
	public static function expired($limit = 500)
	{
		if (!self::supported())
		{
			return array();
		}

		$db    = self::db();
		$grace = (int) self::param('membership_expiration_grace_hours', 0);
		$cut   = $grace > 0
			? "(UTC_TIMESTAMP() - INTERVAL " . $grace . " HOUR)"
			: "UTC_TIMESTAMP()";

		$db->setQuery(
			"SELECT `id`, `gidNumber`, `uidNumber`, `expires`
			   FROM `#__xgroups_members`
			  WHERE `expires` IS NOT NULL
			    AND `expires` <= " . $cut . "
			  ORDER BY `expires` ASC
			  LIMIT " . (int) $limit
		);

		return $db->loadObjectList() ?: array();
	}

	/**
	 * How many memberships are past due right now
	 *
	 * Feeds the admin health panel: with revocation-by-cron, a stopped job is
	 * the difference between terms being enforced and terms being decorative,
	 * so it needs to be visible.
	 *
	 * @return  integer
	 */
	public static function pastDueCount()
	{
		if (!self::supported())
		{
			return 0;
		}

		$db = self::db();

		$db->setQuery(
			"SELECT COUNT(*) FROM `#__xgroups_members`
			  WHERE `expires` IS NOT NULL AND `expires` <= UTC_TIMESTAMP()"
		);

		return (int) $db->loadResult();
	}

	/**
	 * Is this user the only manager the group has left?
	 *
	 * @param   integer  $gidNumber
	 * @param   integer  $uidNumber
	 * @return  boolean
	 */
	public static function isLastManager($gidNumber, $uidNumber)
	{
		$db = self::db();

		$db->setQuery(
			"SELECT COUNT(*) FROM `#__xgroups_managers`
			  WHERE `gidNumber`=" . $db->quote((int) $gidNumber) . "
			    AND `uidNumber`=" . $db->quote((int) $uidNumber)
		);

		if (!$db->loadResult())
		{
			return false;
		}

		$db->setQuery(
			"SELECT COUNT(*) FROM `#__xgroups_managers`
			  WHERE `gidNumber`=" . $db->quote((int) $gidNumber)
		);

		return ((int) $db->loadResult()) <= 1;
	}

	/**
	 * Drop a user's role assignments within one group
	 *
	 * `#__xgroups_member_roles` has no gidNumber of its own, so the group is
	 * reached through the roles it owns.
	 *
	 * @param   integer  $gidNumber
	 * @param   integer  $uidNumber
	 * @return  boolean
	 */
	public static function clearRoles($gidNumber, $uidNumber)
	{
		$db = self::db();

		$db->setQuery(
			"DELETE mr FROM `#__xgroups_member_roles` AS mr
			  INNER JOIN `#__xgroups_roles` AS r ON r.`id`=mr.`roleid`
			  WHERE mr.`uidNumber`=" . $db->quote((int) $uidNumber) . "
			    AND r.`gidNumber`=" . $db->quote((int) $gidNumber)
		);

		return (bool) $db->query();
	}

	/**
	 * Take a membership away
	 *
	 * Ordered so a partial failure leaves the user out rather than in: the
	 * rows that grant access go first, bookkeeping after.
	 *
	 * Does the deletes directly rather than through Group::remove() plus
	 * update(), which rewrites the group's entire member list and would cost
	 * O(members) writes for every single revocation.
	 *
	 * Does **not** fire user.onAfterStoreGroup — callers revoking in bulk
	 * should fire it once per group when they are done, with a freshly read
	 * Group so listeners see the post-revocation roster.
	 *
	 * @param   integer  $gidNumber
	 * @param   integer  $uidNumber
	 * @param   string   $reason
	 * @param   integer  $actor      null for the reaper
	 * @return  boolean
	 */
	public static function revoke($gidNumber, $uidNumber, $reason = self::REASON_EXPIRED, $actor = null)
	{
		$db  = self::db();
		$gid = (int) $gidNumber;
		$uid = (int) $uidNumber;

		$expires = self::supported() ? self::expiresFor($gid, $uid) : null;

		$db->setQuery(
			"SELECT COUNT(*) FROM `#__xgroups_members`
			  WHERE `gidNumber`=" . $db->quote($gid) . " AND `uidNumber`=" . $db->quote($uid)
		);

		if (!$db->loadResult())
		{
			// already gone; nothing to do and nothing to log
			return false;
		}

		$db->setQuery(
			"SELECT COUNT(*) FROM `#__xgroups_managers`
			  WHERE `gidNumber`=" . $db->quote($gid) . " AND `uidNumber`=" . $db->quote($uid)
		);

		$wasManager = (int) $db->loadResult() ? 1 : 0;

		// 1. the row that grants access
		$db->setQuery(
			"DELETE FROM `#__xgroups_members`
			  WHERE `gidNumber`=" . $db->quote($gid) . " AND `uidNumber`=" . $db->quote($uid)
		);

		if (!$db->query())
		{
			return false;
		}

		// 2. a manager row without a membership row is a phantom manager
		$db->setQuery(
			"DELETE FROM `#__xgroups_managers`
			  WHERE `gidNumber`=" . $db->quote($gid) . " AND `uidNumber`=" . $db->quote($uid)
		);
		$db->query();

		// 3. roles held in this group only
		self::clearRoles($gid, $uid);

		// 4. keep the term that lapsed
		self::archive($gid, $uid, $expires, $reason, $wasManager, $actor);

		// 5/6. audit
		self::log($gid, $uid, 'group_member_' . $reason, array('uidNumber' => $uid, 'expires' => $expires), $actor);

		$recipients = array(
			array('group', $gid),
			array('user', $uid)
		);

		try
		{
			\Event::trigger('system.logActivity', array(
				'activity' => array(
					'action'      => 'removed',
					'scope'       => 'group.membership',
					'scope_id'    => $gid,
					'description' => 'membership ' . $reason,
					'details'     => array(
						'gidNumber' => $gid,
						'uidNumber' => $uid,
						'reason'    => $reason
					)
				),
				'recipients' => $recipients
			));
		}
		catch (\Exception $e)
		{
			// activity logging must never take the revocation down with it
		}

		// 7. symmetric to groups.onGroupUserEnrollment, so listeners that set
		// something up on join get a chance to tear it down
		\Event::trigger('groups.onGroupUserRevocation', array($gid, $uid, $reason));

		return true;
	}

	/**
	 * Record a membership that has left the table
	 *
	 * @param   integer  $gidNumber
	 * @param   integer  $uidNumber
	 * @param   string   $expires
	 * @param   string   $reason
	 * @param   integer  $wasManager
	 * @param   integer  $actor
	 * @return  boolean
	 */
	public static function archive($gidNumber, $uidNumber, $expires, $reason, $wasManager = 0, $actor = null)
	{
		$db = self::db();

		if (!$db->tableExists('#__xgroups_member_history'))
		{
			return false;
		}

		$db->setQuery(
			"INSERT INTO `#__xgroups_member_history`
				(`gidNumber`,`uidNumber`,`expires`,`revoked`,`reason`,`was_manager`,`actor`)
			 VALUES ("
				. $db->quote((int) $gidNumber) . ","
				. $db->quote((int) $uidNumber) . ","
				. (is_null($expires) ? 'NULL' : $db->quote($expires)) . ","
				. "UTC_TIMESTAMP(),"
				. $db->quote($reason) . ","
				. $db->quote((int) $wasManager) . ","
				. (is_null($actor) ? 'NULL' : $db->quote((int) $actor))
			. ")"
		);

		return (bool) $db->query();
	}

	/**
	 * Archive every member of a group at once, for group deletion
	 *
	 * @param   integer  $gidNumber
	 * @param   string   $reason
	 * @return  integer  rows archived
	 */
	public static function archiveGroup($gidNumber, $reason = self::REASON_GROUP)
	{
		$db = self::db();

		if (!$db->tableExists('#__xgroups_member_history'))
		{
			return 0;
		}

		$expires = self::supported() ? 'm.`expires`' : 'NULL';

		$db->setQuery(
			"INSERT INTO `#__xgroups_member_history`
				(`gidNumber`,`uidNumber`,`expires`,`revoked`,`reason`,`was_manager`,`actor`)
			 SELECT m.`gidNumber`, m.`uidNumber`, " . $expires . ", UTC_TIMESTAMP(), "
				. $db->quote($reason) . ",
				(SELECT COUNT(*) FROM `#__xgroups_managers` AS g
				  WHERE g.`gidNumber`=m.`gidNumber` AND g.`uidNumber`=m.`uidNumber`),
				" . ((int) \User::get('id') ?: 'NULL') . "
			   FROM `#__xgroups_members` AS m
			  WHERE m.`gidNumber`=" . $db->quote((int) $gidNumber)
		);

		if (!$db->query())
		{
			return 0;
		}

		return (int) $db->getAffectedRows();
	}

	/**
	 * Archive a set of memberships in one statement
	 *
	 * Used where a roster change drops several people at once. The manager
	 * flag and the term are read from the tables rather than passed in, so
	 * the caller does not have to look them up per row.
	 *
	 * @param   integer  $gidNumber
	 * @param   array    $uidNumbers
	 * @param   string   $reason
	 * @param   integer  $actor
	 * @return  integer  rows archived
	 */
	public static function archiveMembers($gidNumber, $uidNumbers, $reason = self::REASON_MANUAL, $actor = null)
	{
		$db = self::db();

		if (!$db->tableExists('#__xgroups_member_history') || empty($uidNumbers))
		{
			return 0;
		}

		$ids = array();

		foreach ((array) $uidNumbers as $uid)
		{
			$uid = (int) $uid;

			if ($uid > 0)
			{
				$ids[] = $uid;
			}
		}

		if (empty($ids))
		{
			return 0;
		}

		$expires = self::supported() ? 'm.`expires`' : 'NULL';

		$db->setQuery(
			"INSERT INTO `#__xgroups_member_history`
				(`gidNumber`,`uidNumber`,`expires`,`revoked`,`reason`,`was_manager`,`actor`)
			 SELECT m.`gidNumber`, m.`uidNumber`, " . $expires . ", UTC_TIMESTAMP(), "
				. $db->quote($reason) . ",
				(SELECT COUNT(*) FROM `#__xgroups_managers` AS g
				  WHERE g.`gidNumber`=m.`gidNumber` AND g.`uidNumber`=m.`uidNumber`),
				" . (is_null($actor) ? 'NULL' : $db->quote((int) $actor)) . "
			   FROM `#__xgroups_members` AS m
			  WHERE m.`gidNumber`=" . $db->quote((int) $gidNumber) . "
			    AND m.`uidNumber` IN (" . implode(',', $ids) . ")"
		);

		if (!$db->query())
		{
			return 0;
		}

		return (int) $db->getAffectedRows();
	}

	/**
	 * Archive every membership a user holds, for account deletion
	 *
	 * @param   integer  $uidNumber
	 * @param   string   $reason
	 * @return  integer  rows archived
	 */
	public static function archiveUser($uidNumber, $reason = self::REASON_USER)
	{
		$db = self::db();

		if (!$db->tableExists('#__xgroups_member_history'))
		{
			return 0;
		}

		$expires = self::supported() ? 'm.`expires`' : 'NULL';

		$db->setQuery(
			"INSERT INTO `#__xgroups_member_history`
				(`gidNumber`,`uidNumber`,`expires`,`revoked`,`reason`,`was_manager`,`actor`)
			 SELECT m.`gidNumber`, m.`uidNumber`, " . $expires . ", UTC_TIMESTAMP(), "
				. $db->quote($reason) . ",
				(SELECT COUNT(*) FROM `#__xgroups_managers` AS g
				  WHERE g.`gidNumber`=m.`gidNumber` AND g.`uidNumber`=m.`uidNumber`),
				NULL
			   FROM `#__xgroups_members` AS m
			  WHERE m.`uidNumber`=" . $db->quote((int) $uidNumber)
		);

		if (!$db->query())
		{
			return 0;
		}

		return (int) $db->getAffectedRows();
	}

	/**
	 * Write a row to the group log
	 *
	 * Done in SQL rather than through Components\Groups\Models\Log so the
	 * library keeps no dependency on the component.
	 *
	 * @param   integer  $gidNumber
	 * @param   integer  $uidNumber
	 * @param   string   $action
	 * @param   mixed    $comments
	 * @param   integer  $actor
	 * @return  boolean
	 */
	protected static function log($gidNumber, $uidNumber, $action, $comments = array(), $actor = null)
	{
		$db = self::db();

		if (!$db->tableExists('#__xgroups_log'))
		{
			return false;
		}

		$db->setQuery(
			"INSERT INTO `#__xgroups_log` (`gidNumber`,`timestamp`,`userid`,`action`,`comments`,`actorid`)
			 VALUES ("
				. $db->quote((int) $gidNumber) . ","
				. "UTC_TIMESTAMP(),"
				. $db->quote((int) $uidNumber) . ","
				. $db->quote($action) . ","
				. $db->quote(json_encode($comments)) . ","
				. $db->quote((int) $actor)
			. ")"
		);

		return (bool) $db->query();
	}

	/**
	 * The configured warning thresholds, in days, largest first
	 *
	 * @return  array
	 */
	public static function warningDays()
	{
		return self::parseWarningDays(self::param('membership_expiration_warning_days', '30,7,1'));
	}

	/**
	 * Turn a warning-days setting into a clean, descending list
	 *
	 * Split out from warningDays() so the parsing can be exercised without a
	 * configured hub behind it.
	 *
	 * @param   string  $raw
	 * @return  array
	 */
	public static function parseWarningDays($raw)
	{
		$days = array();

		foreach (explode(',', (string) $raw) as $piece)
		{
			$piece = trim($piece);

			// Only accept plain integers; "7 days" or "abc" are configuration
			// mistakes and silently reading them as 7 or 0 helps nobody.
			if ($piece === '' || !preg_match('/^\d+$/', $piece))
			{
				continue;
			}

			$piece = (int) $piece;

			if ($piece > 0)
			{
				$days[] = $piece;
			}
		}

		$days = array_values(array_unique($days));
		rsort($days);

		return $days;
	}

	/**
	 * Memberships due a warning at the given threshold
	 *
	 * A row qualifies when its term falls inside the threshold window and it
	 * has not already been warned since that window opened. Storing the stamp
	 * rather than a flag means each threshold fires once, and extending a term
	 * (which clears the stamp) restarts the cycle.
	 *
	 * @param   integer  $days
	 * @param   integer  $limit
	 * @return  array
	 */
	public static function dueForWarning($days, $limit = 1000)
	{
		if (!self::supported())
		{
			return array();
		}

		$db   = self::db();
		$days = (int) $days;

		$db->setQuery(
			"SELECT `id`, `gidNumber`, `uidNumber`, `expires`, `expires_notified`
			   FROM `#__xgroups_members`
			  WHERE `expires` IS NOT NULL
			    AND `expires` > UTC_TIMESTAMP()
			    AND `expires` <= (UTC_TIMESTAMP() + INTERVAL " . $days . " DAY)
			    AND (`expires_notified` IS NULL
			         OR `expires_notified` < (`expires` - INTERVAL " . $days . " DAY))
			  ORDER BY `expires` ASC
			  LIMIT " . (int) $limit
		);

		return $db->loadObjectList() ?: array();
	}

	/**
	 * Stamp a membership as warned
	 *
	 * @param   integer  $gidNumber
	 * @param   integer  $uidNumber
	 * @return  boolean
	 */
	public static function markNotified($gidNumber, $uidNumber)
	{
		if (!self::supported())
		{
			return false;
		}

		$db = self::db();

		$db->setQuery(
			"UPDATE `#__xgroups_members` SET `expires_notified`=UTC_TIMESTAMP()
			  WHERE `gidNumber`=" . $db->quote((int) $gidNumber) . "
			    AND `uidNumber`=" . $db->quote((int) $uidNumber)
		);

		return (bool) $db->query();
	}

	/**
	 * Record that the reaper ran
	 *
	 * Kept in the group log with a null gidNumber (a hub-wide entry) rather
	 * than in component params: this is state, not configuration, and saving
	 * the config form would otherwise wipe it.
	 *
	 * @param   array  $result  as returned by reap()
	 * @return  boolean
	 */
	public static function heartbeat($result = array())
	{
		$db = self::db();

		if (!$db->tableExists('#__xgroups_log'))
		{
			return false;
		}

		$comments = json_encode(array(
			'revoked' => isset($result['revoked']) ? (int) $result['revoked'] : 0,
			'skipped' => isset($result['skipped']) ? (int) $result['skipped'] : 0,
			'groups'  => isset($result['groups']) ? (int) $result['groups'] : 0
		));

		$db->setQuery(
			"INSERT INTO `#__xgroups_log` (`gidNumber`,`timestamp`,`userid`,`action`,`comments`,`actorid`)
			 VALUES (NULL, UTC_TIMESTAMP(), 0, 'membership_reaper_run', " . $db->quote($comments) . ", 0)"
		);

		$ok = (bool) $db->query();

		// Running every quarter of an hour, this would otherwise add tens of
		// thousands of rows a year to a table that exists for group audit.
		// Only the recent ones are of any use - the panel reads the latest.
		$db->setQuery(
			"DELETE FROM `#__xgroups_log`
			  WHERE `action`='membership_reaper_run'
			    AND `timestamp` < (UTC_TIMESTAMP() - INTERVAL 7 DAY)"
		);
		$db->query();

		return $ok;
	}

	/**
	 * When the reaper last ran, and what it did
	 *
	 * @return  object|null
	 */
	public static function lastRun()
	{
		$db = self::db();

		if (!$db->tableExists('#__xgroups_log'))
		{
			return null;
		}

		$db->setQuery(
			"SELECT `timestamp`, `comments` FROM `#__xgroups_log`
			  WHERE `action`='membership_reaper_run'
			  ORDER BY `timestamp` DESC LIMIT 1"
		);

		return $db->loadObject();
	}

	/**
	 * Is the reaper alive?
	 *
	 * @param   integer  $withinHours
	 * @return  boolean
	 */
	public static function reaperHealthy($withinHours = 24)
	{
		$last = self::lastRun();

		if (!$last || empty($last->timestamp))
		{
			return false;
		}

		return (strtotime($last->timestamp . ' UTC') > (time() - ($withinHours * 3600)));
	}

	/**
	 * Reap every lapsed membership, in batches
	 *
	 * @param   integer  $limit  rows to consider in this pass
	 * @return  array    counts: revoked, skipped, groups
	 */
	public static function reap($limit = null)
	{
		$result = array('revoked' => 0, 'skipped' => 0, 'groups' => 0);

		if (!self::supported())
		{
			return $result;
		}

		if (is_null($limit))
		{
			$limit = (int) self::param('membership_expiration_batch', 500);
			$limit = $limit > 0 ? $limit : 500;
		}

		$rows = self::expired($limit);

		if (empty($rows))
		{
			return $result;
		}

		// group the work so the store event fires once per group
		$byGroup = array();

		foreach ($rows as $row)
		{
			$byGroup[(int) $row->gidNumber][] = $row;
		}

		foreach ($byGroup as $gid => $members)
		{
			$touched = false;

			foreach ($members as $row)
			{
				$uid = (int) $row->uidNumber;

				// a group with no manager cannot be administered back to
				// health, so the last one keeps their seat and the situation
				// is logged for an administrator to resolve
				if (self::isLastManager($gid, $uid))
				{
					self::log($gid, $uid, 'group_member_expiry_blocked', array(
						'uidNumber' => $uid,
						'expires'   => $row->expires,
						'reason'    => 'last manager'
					));

					$result['skipped']++;
					continue;
				}

				if (self::revoke($gid, $uid, self::REASON_EXPIRED, null))
				{
					$result['revoked']++;
					$touched = true;
				}
			}

			if ($touched)
			{
				$result['groups']++;

				// read the group *after* the deletes so listeners see the
				// roster they are supposed to sync
				$group = new Group();

				if ($group->read($gid))
				{
					\Event::trigger('user.onAfterStoreGroup', array($group));
				}
			}
		}

		return $result;
	}
}
