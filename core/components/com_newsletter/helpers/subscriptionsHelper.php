<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright (c) 2005-2020 The Regents of the University of California.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Newsletter\Helpers;

use Exception;

class SubscriptionsHelper
{

	private static $activeReferenceField = 'preference';
	private static $activePattern = '/^yes/i';
	private static $badDataSubmitted = 'Unable to process your updates';
	private static $subscriptionNotFound = 'Selected user subscription not found';
	private static $userProfileOptionNotFound = 'Selected user profile option not found';

	public function __construct() {
		$this->db = App::get('db');
	}

	public function loadSubscriptions($userId)
	{
		$activeRef = self::$activeReferenceField;

		$this->db->setQuery("
			select pf.id, pf.label, es.order, es.view, up.profile_value as $activeRef,
			       pf.name as foreign_key
			from jos_email_subscriptions es
			left join jos_user_profile_fields pf on es.profile_field_name = pf.name
			left join jos_user_profiles up on pf.name = up.profile_key and up.user_id = $userId;");

		return array_map(function($sub) {
			return $this->subToMap($sub);
		}, $this->db->loadObjectList());
	}

	private function subToMap($sub)
	{
		$activeRef = self::$activeReferenceField;
		$subMap = (array) $sub;

		$subMap['active'] = preg_match(self::$activePattern, $sub->$activeRef);
		$subMap['options'] = self::loadSubscriptionOptions($sub->id);

		return $subMap;
	}

	// Added exception handling to provide feedback if database has not been set up correctly for com_reply features:
	private function loadSubscriptionOptions($profileFieldId)
	{
		// If empty profileFieldId passed, we were unable to look up the subscription:
		if ($profileFieldId) {
			$this->db->setQuery("select value
		                     from jos_user_profile_options
		                     where field_id = $profileFieldId");
		} else {
			throw new Exception(self::$subscriptionNotFound, 400);
		}

		// If profile option cannot be found, pass friendlier error:
		if ($this->db->loadColumn())
		{
			return $this->db->loadColumn();
		} else {
			throw new Exception(self::$userProfileOptionNotFound, 400);
		}
	}

	public function updateSubscriptions($userId, $updatedSubscriptions)
	{
		foreach ($updatedSubscriptions as $s)
		{
			$this->updateSubscription($userId, $s);
		}
	}

	private function updateSubscription($userId, $updatedSubscription)
	{
		$updateQuery = $this->buildUpdateQuery($userId, $updatedSubscription);

		$this->db->setQuery($updateQuery);

		$this->db->execute();
	}

	private function buildUpdateQuery($userId, $updatedSubscription)
	{
		$preference = $updatedSubscription['preference'];
		$profileFieldFk = $updatedSubscription['foreign_key'];

		$this->validateSubmittedSubscription($profileFieldFk, $preference);

		if ($this->subscriptionExists($userId, $updatedSubscription))
		{
			$query = $this->generateUpdateQuery($userId, $updatedSubscription);
		}
		else
		{
			$query = $this->generateInsertQuery($userId, $updatedSubscription);
		}

		return $query;
	}

	private function subscriptionExists($userId, $subscription) {
		$profileFieldFk = $subscription['foreign_key'];

		$countQuery = "select id
		               from jos_user_profiles
		               where user_id = $userId and profile_key = '$profileFieldFk';";

		$this->db->setQuery($countQuery);

		$count = $this->db->execute()->getNumRows();

		return $count > 0;
	}

	private function generateUpdateQuery($userId, $subscription) {
		$preference = $subscription['preference'];
		$profileFieldFk = $subscription['foreign_key'];

		return "update jos_user_profiles
		        set profile_value = '$preference'
		        where profile_key = '$profileFieldFk' and user_id = $userId;";
	}

	private function generateInsertQuery($userId, $subscription) {
		$preference = $subscription['preference'];
		$profileFieldFk = $subscription['foreign_key'];

		return "insert into jos_user_profiles
		        (user_id, profile_key, profile_value)
		        values($userId, '$profileFieldFk', '$preference');";
	}

	private function validateSubmittedSubscription($profileFieldFk,	$preference)
	{
		$this->validateProfileFieldFk($profileFieldFk);
		$this->validatePreference($preference);
	}

	private function validateProfileFieldFk($profileFieldFk)
	{
		$validProfileFieldFks = $this->loadProfileFieldFks();

		if (!in_array($profileFieldFk, $validProfileFieldFks)) {
			throw new Exception(self::$badDataSubmitted, 400);
		}
	}

	private function loadProfileFieldFks()
	{
		$this->db->setQuery("
				select pf.name
				from jos_email_subscriptions es
				left join jos_user_profile_fields pf on es.profile_field_name = pf.name;");

		return $this->db->loadColumn();
	}

	private function validatePreference($preference)
	{
		$validPreferences = $this->loadProfileOptionValues();

		if (!in_array($preference, $validPreferences)) {
			throw new Exception(self::$badDataSubmitted, 400);
		}
	}

	private function loadProfileOptionValues()
	{
		$this->db->setQuery("
				select po.value
				from jos_email_subscriptions es
				left join jos_user_profile_fields pf on es.profile_field_name = pf.name
				left join jos_user_profile_options po on pf.id = po.field_id;");

		return $this->db->loadColumn();
	}

}
