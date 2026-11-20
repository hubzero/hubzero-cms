<?php

/*
 * @package   hubzero-cms
 * @copyright Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Newsletter\Helpers;

use Exception;
use Hubzero\Facades\App;

class SubscriptionsHelper
{
    /**
     * Database connection
     *
     * @var \Hubzero\Database\Driver
     */
    private $db;

    /**
     * @var  string
     */
    private static $activeReferenceField = 'preference';
    /**
     * @var  string
     */
    private static $activePattern = '/^yes/i';
    /**
     * @var  string
     */
    private static $badDataSubmitted = 'Unable to process your updates';
    /**
     * @var  string
     */
    private static $subscriptionNotFound = 'Selected user subscription not found';
    /**
     * @var  string
     */
    private static $userProfileOptionNotFound = 'Selected user profile option not found';

    public function __construct()
    {
        $this->db = App::get('db');
    }

    /**
     * @param   mixed  $userId
     * @return  array<mixed>
     */
    public function loadSubscriptions($userId)
    {
        $userId = (int) $userId;
        $activeRef = self::$activeReferenceField;

        $this->db->setQuery("
			select pf.id, pf.label, es.order, es.view, up.profile_value as $activeRef,
			       pf.name as foreign_key
			from #__email_subscriptions es
			left join #__user_profile_fields pf on es.profile_field_name = pf.name
			left join #__user_profiles up on pf.name = up.profile_key and up.user_id = $userId;");

        return array_map($this->subToMap(...), $this->db->loadObjectList());
    }

    /**
     * @param   mixed  $sub
     * @return  array<mixed>
     */
    private function subToMap($sub)
    {
        $activeRef = self::$activeReferenceField;
        $subMap = (array) $sub;

        $subMap['active'] = preg_match(self::$activePattern, $sub->$activeRef);
        $subMap['options'] = self::loadSubscriptionOptions($sub->id);

        return $subMap;
    }

    // Added exception handling to provide feedback if database has not been set up correctly for com_reply features:
    /**
     * @param   mixed  $profileFieldId
     * @return  array<int, mixed>
     */
    private function loadSubscriptionOptions($profileFieldId)
    {
        // If empty profileFieldId passed, we were unable to look up the subscription:
        if ($profileFieldId) {
            $this->db->setQuery("select value
		                     from #__user_profile_options
		                     where field_id = $profileFieldId");
        } else {
            throw new Exception(self::$subscriptionNotFound, 400);
        }

        // If profile option cannot be found, pass friendlier error:
        if ($this->db->loadColumn()) {
            return $this->db->loadColumn();
        } else {
            throw new Exception(self::$userProfileOptionNotFound, 400);
        }
    }

    /**
     * @param   int  $userId
     * @param   array<array<mixed>>  $updatedSubscriptions
     * @return void
     */
    public function updateSubscriptions($userId, $updatedSubscriptions)
    {
        $userId = (int) $userId;

        foreach ($updatedSubscriptions as $s) {
            $this->updateSubscription($userId, $s);
        }
    }

    /**
     * @param   int  $userId
     * @param   mixed  $updatedSubscription
     * @return void
     */
    private function updateSubscription($userId, $updatedSubscription)
    {
        $updateQuery = $this->buildUpdateQuery($userId, $updatedSubscription);

        $this->db->setQuery($updateQuery);

        $this->db->execute();
    }

    /**
     * @param   mixed  $userId
     * @param   mixed  $updatedSubscription
     * @return  mixed
     */
    private function buildUpdateQuery($userId, $updatedSubscription)
    {
        $preference = $updatedSubscription['preference'];
        $profileFieldFk = $updatedSubscription['foreign_key'];

        $this->validateSubmittedSubscription($profileFieldFk, $preference);

        if ($this->subscriptionExists($userId, $updatedSubscription)) {
            $query = $this->generateUpdateQuery($userId, $updatedSubscription);
        } else {
            $query = $this->generateInsertQuery($userId, $updatedSubscription);
        }

        return $query;
    }

    /**
     * @param   mixed  $userId
     * @param   mixed  $subscription
     * @return  bool
     */
    private function subscriptionExists($userId, $subscription)
    {
        $profileFieldFk = $subscription['foreign_key'];

        $countQuery = "select id
		               from #__user_profiles
		               where user_id = $userId and profile_key = " . $this->db->quote($profileFieldFk) . ";";

        $this->db->setQuery($countQuery);

        $count = $this->db->execute()->getNumRows();

        return $count > 0;
    }

    /**
     * @param   mixed  $userId
     * @param   mixed  $subscription
     * @return  string
     */
    private function generateUpdateQuery($userId, $subscription)
    {
        $preference = $subscription['preference'];
        $profileFieldFk = $subscription['foreign_key'];

        return "update #__user_profiles
		        set profile_value = " . $this->db->quote($preference) . "
		        where profile_key = " . $this->db->quote($profileFieldFk) . " and user_id = $userId;";
    }

    /**
     * @param   mixed  $userId
     * @param   mixed  $subscription
     * @return  string
     */
    private function generateInsertQuery($userId, $subscription)
    {
        $preference = $subscription['preference'];
        $profileFieldFk = $subscription['foreign_key'];

        return "insert into #__user_profiles
		        (user_id, profile_key, profile_value)
		        values($userId, " . $this->db->quote($profileFieldFk) . ", " . $this->db->quote($preference) . ");";
    }

    /**
     * @param   mixed  $profileFieldFk
     * @param   mixed  $preference
     * @return void
     */
    private function validateSubmittedSubscription($profileFieldFk, $preference)
    {
        $this->validateProfileFieldFk($profileFieldFk);
        $this->validatePreference($preference);
    }

    /**
     * @param   mixed  $profileFieldFk
     * @return void
     */
    private function validateProfileFieldFk($profileFieldFk)
    {
        $validProfileFieldFks = $this->loadProfileFieldFks();

        if (!in_array($profileFieldFk, $validProfileFieldFks)) {
            throw new Exception(self::$badDataSubmitted, 400);
        }
    }

    /**
     * @return  array<int, mixed>
     */
    private function loadProfileFieldFks()
    {
        $this->db->setQuery("
				select pf.name
				from #__email_subscriptions es
				left join #__user_profile_fields pf on es.profile_field_name = pf.name;");

        return $this->db->loadColumn() ?: [];
    }

    /**
     * @param   mixed  $preference
     * @return void
     */
    private function validatePreference($preference)
    {
        $validPreferences = $this->loadProfileOptionValues();

        if (!in_array($preference, $validPreferences)) {
            throw new Exception(self::$badDataSubmitted, 400);
        }
    }

    /**
     * @return  array<int, mixed>
     */
    private function loadProfileOptionValues()
    {
        $this->db->setQuery("
				select po.value
				from #__email_subscriptions es
				left join #__user_profile_fields pf on es.profile_field_name = pf.name
				left join #__user_profile_options po on pf.id = po.field_id;");

        return $this->db->loadColumn() ?: [];
    }
}
