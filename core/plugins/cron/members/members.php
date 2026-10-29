<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Cron plugin for members
 */
class plgCronMembers extends \Hubzero\Plugin\Plugin
{
	/**
	 * Return a list of events
	 *
	 * @return  array
	 */
	public function onCronEvents()
	{
		$this->loadLanguage();

		$obj = new stdClass();
		$obj->plugin = 'members';
		$obj->events = array(
			array(
				'name'   => 'onPointRoyalties',
				'label'  => Lang::txt('PLG_CRON_MEMBERS_POINT_ROYALTIES'),
				'params' => ''
			),
			array(
				'name'   => 'expireInactiveAccounts',
				'label'  => Lang::txt('PLG_CRON_MEMBERS_EXPIRE_INACTIVE'),
				'params' => 'expireinactive'
			)
		);

		return $obj;
	}

	/**
	 * Calculate point royalties for members
	 *
	 * @param   object   $job  \Components\Cron\Models\Job
	 * @return  boolean
	 */
	public function onPointRoyalties(\Components\Cron\Models\Job $job)
	{
		$this->database = App::get('db');

		$action = 'royalty';

		// What month/year is it now?
		$curmonth = Date::format("F");
		$curyear  = Date::format("Y-m");
		$ref = strtotime($curyear);

		$this->_message = Lang::txt('PLG_CRON_MEMBERS_POINT_ROYALTIES_DISTRIBUTED_ANSWERS', $curyear);
		$rmsg   = Lang::txt('PLG_CRON_MEMBERS_POINT_ROYALTIES_DISTRIBUTED_REVIEWS', $curyear);
		$resmsg = Lang::txt('PLG_CRON_MEMBERS_POINT_ROYALTIES_DISTRIBUTED_RESOURCES', $curyear);

		// Make sure we distribute royalties only once/ month
		$royaltyAnswers   = \Hubzero\Bank\MarketHistory::getRecord('', $action, 'answers', $curyear, $this->_message);
		$royaltyReviews   = \Hubzero\Bank\MarketHistory::getRecord('', $action, 'reviews', $curyear, $rmsg);
		$royaltyResources = \Hubzero\Bank\MarketHistory::getRecord('', $action, 'resources', $curyear, $resmsg);

		// Include economy classes
		if (is_file(Component::path('com_answers') . DS . 'helpers' . DS . 'economy.php'))
		{
			require_once Component::path('com_answers') . DS . 'helpers' . DS . 'economy.php';
		}

		if (is_file(Component::path('com_resources') . DS . 'helpers' . DS . 'economy.php'))
		{
			require_once Component::path('com_resources') . DS . 'helpers' . DS . 'economy.php';
		}

		$AE = new \Components\Answers\Helpers\Economy($this->database);
		$accumulated = 0;

		// Get Royalties on Answers
		if (!$royaltyAnswers)
		{
			$rows = $AE->getQuestions();

			if ($rows)
			{
				foreach ($rows as $r)
				{
					$AE->distribute_points($r->id, $r->q_owner, $r->a_owner, $action);
					$accumulated = $accumulated + $AE->calculate_marketvalue($r->id, $action);
				}

				// make a record of royalty payment
				if (intval($accumulated) > 0)
				{
					$MH = \Hubzero\Bank\MarketHistory::blank()->set(array(
						'itemid'       => $ref,
						'date'         => Date::toSql(),
						'market_value' => $accumulated,
						'category'     => 'answers',
						'action'       => $action,
						'log'          => $this->_message
					));

					if (!$MH->save())
					{
						$err = $MH->getError();
					}
				}
			}
			else
			{
				$this->_message = Lang::txt('PLG_CRON_MEMBERS_POINT_ROYALTIES_NO_QUESTIONS');
			}
		}
		else
		{
			$this->_message = Lang::txt('PLG_CRON_MEMBERS_POINT_ROYALTIES_ALREADY_DISTRIBUTED_ANSWERS', $curyear);
		}

		// Get Royalties on Resource Reviews
		if (!$royaltyReviews)
		{
			// get eligible
			$RE = new \Components\Resources\Helpers\Economy\Reviews($this->database);
			$reviews = $RE->getReviews();

			// do we have ratings on reviews enabled?
			$param = Plugin::byType('resources', 'reviews');
			$plparam = new \Hubzero\Config\Registry($param->params);
			$voting = $plparam->get('voting');

			$accumulated = 0;
			if ($reviews && $voting)
			{
				foreach ($reviews as $r)
				{
					$RE->distribute_points($r, $action);
					$accumulated = $accumulated + $RE->calculate_marketvalue($r, $action);
				}

				$this->_message .= $rmsg;
			}
			else
			{
				$this->_message .= Lang::txt('PLG_CRON_MEMBERS_POINT_ROYALTIES_NO_REVIEWS');
			}

			// make a record of royalty payment
			if (intval($accumulated) > 0)
			{
				$MH = \Hubzero\Bank\MarketHistory::blank()->set(array(
					'itemid'       => $ref,
					'date'         => Date::toSql(),
					'market_value' => $accumulated,
					'category'     => 'reviews',
					'action'       => $action,
					'log'          => $rmsg
				));

				if (!$MH->save())
				{
					$err = $MH->getError();
				}
			}
		}
		else
		{
			$this->_message .= Lang::txt('PLG_CRON_MEMBERS_POINT_ROYALTIES_ALREADY_DISTRIBUTED_REVIEWS', $curyear);
		}

		// Get Royalties on Resources
		if (!$royaltyResources)
		{
			// get eligible
			$ResE = new \Components\Resources\Helpers\Economy($this->database);
			$cons = $ResE->getCons();

			$accumulated = 0;
			if ($cons)
			{
				foreach ($cons as $con)
				{
					$ResE->distribute_points($con, $action);
					$accumulated = $accumulated + $con->ranking;
				}

				$this->_message .= $resmsg;
			}
			else
			{
				$this->_message .= Lang::txt('PLG_CRON_MEMBERS_POINT_ROYALTIES_NO_RESOURCES');
			}

			// make a record of royalty payment
			if (intval($accumulated) > 0)
			{
				$MH = \Hubzero\Bank\MarketHistory::blank()->set(array(
					'itemid'       => $ref,
					'date'         => Date::toSql(),
					'market_value' => $accumulated,
					'category'     => 'resources',
					'action'       => $action,
					'log'          => $resmsg
				));

				if (!$MH->save())
				{
					$err = $MH->getError();
				}
			}
		}
		else
		{
			$this->_message .= Lang::txt('PLG_CRON_MEMBERS_POINT_ROYALTIES_ALREADY_DISTRIBUTED_RESOURCES', $curyear);
		}

		return true;
	}

	/**
	 * Inactivity period from the job parameter
	 *
	 * @param   mixed  $value
	 * @return  integer  Months, or 0 if the value is not a whole number >= 1
	 */
	public static function inactivityMonths($value)
	{
		$value = trim((string) $value);

		return (ctype_digit($value) && (int) $value >= 1) ? (int) $value : 0;
	}

	/**
	 * Accounts to expire: not blocked, no account expiry set, and neither a
	 * web sign-in (or registration, if never signed in) nor a password change
	 * since the cutoff
	 *
	 * @param   string   $quotedCutoff       SQL datetime (UTC), already quoted
	 * @param   integer  $cutoffDay          The cutoff in days since the epoch
	 * @param   string   $quotedExemptGroup  Title of an access group whose members are skipped, already quoted; null for none
	 * @return  string   Query selecting user ids
	 */
	public static function inactiveAccountsQuery($quotedCutoff, $cutoffDay, $quotedExemptGroup = null)
	{
		$lastSeen = "CASE WHEN u.`lastvisitDate` IS NULL OR u.`lastvisitDate` = '0000-00-00 00:00:00'"
			. " THEN u.`registerDate` ELSE u.`lastvisitDate` END";

		return "SELECT u.`id` FROM `#__users` AS u"
			. " LEFT JOIN `#__users_password` AS p ON p.`user_id` = u.`id`"
			. " WHERE u.`block` = 0"
			. " AND p.`shadowExpire` IS NULL"
			. " AND " . $lastSeen . " < " . $quotedCutoff
			. " AND (p.`shadowLastChange` IS NULL OR p.`shadowLastChange` < " . (int) $cutoffDay . ")"
			. ($quotedExemptGroup === null ? '' :
				" AND u.`id` NOT IN (SELECT m.`user_id` FROM `#__user_usergroup_map` AS m"
				. " JOIN `#__usergroups` AS g ON g.`id` = m.`group_id` WHERE g.`title` = " . $quotedExemptGroup . ")");
	}

	/**
	 * Expire accounts with no activity in the last N months: no web sign-in
	 * (or registration, if never signed in) and no password change.
	 *
	 * Marks them with shadowExpire = Password::INACTIVE_EXPIRE_DAY, which
	 * LDAP/PAM read as an expired account (no SSH/SFTP, keys included) and the
	 * CMS reads as "no password sign-in, no password reset". A web sign-in by
	 * another method, or an admin setting a new password, lifts it.
	 *
	 * Accounts with any other shadowExpire (blocked, or not yet reset) are
	 * left alone, and so are members of the access group named by the
	 * members_expireinactive_exempt_group parameter. An interrupted run is
	 * completed by the next one.
	 *
	 * @param   object   $job  \Components\Cron\Models\Job
	 * @return  boolean
	 */
	public function expireInactiveAccounts(\Components\Cron\Models\Job $job)
	{
		$value  = $job->params->get('members_expireinactive_months', '36');
		$months = self::inactivityMonths($value);

		if (!$months)
		{
			Log::error('PLG_CRON_MEMBERS expireInactiveAccounts: members_expireinactive_months must be a whole number of months, at least 1; got "' . $value . '". Nothing changed.');
			return false;
		}

		$cutoff    = Date::of('now')->modify('-' . $months . ' months')->toSql();
		$cutoffDay = intval(strtotime($cutoff . ' UTC') / 86400);

		// Service accounts that never sign in on the web (SSH keys only, or no login at all)
		$exempt = trim((string) $job->params->get('members_expireinactive_exempt_group', 'Service accounts'));

		$db    = App::get('db');
		$query = self::inactiveAccountsQuery($db->quote($cutoff), $cutoffDay, $exempt === '' ? null : $db->quote($exempt));

		$db->setQuery($query);
		$ids = $db->loadColumn();

		$marked = 0;

		foreach ((array) $ids as $id)
		{
			// Creates the password row from the profile if there is none
			$hzup = \Hubzero\User\Password::getInstance((int) $id);

			if (!$hzup)
			{
				continue;
			}

			$hzup->set('shadowExpire', \Hubzero\User\Password::INACTIVE_EXPIRE_DAY);

			// Fires onAfterStorePassword, which syncs the account to LDAP
			$hzup->update();

			$marked++;
		}

		Log::info('PLG_CRON_MEMBERS expireInactiveAccounts: ' . $marked . ' account(s) with no activity since ' . $cutoff . ' expired.');

		return true;
	}
}
