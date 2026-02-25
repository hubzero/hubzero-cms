<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Support\Helpers;

use Hubzero\Base\Obj;
use Hubzero\User\Helper as UserHelper;
use Hubzero\Facades\User;

/**
 * Helper class for support ACL
 */
class ACL extends Obj
{
    /**
     * Current user
     *
     * @var object
     */
    private $user;

    /**
     * Database
     *
     * @var object
     */
    private $db;

    /**
     * Raw data from database
     *
     * @var array
     */
    private $rawData;

    /**
     * User's groups
     *
     * @var array
     */
    private $userGroups;

    /**
     * Constructor
     *
     * @return  void
     */
    public function __construct()
    {
        $this->user = User::getInstance();
        $this->db = \Hubzero\Facades\App::get('db');

        $sql = "SELECT m.*, r.model AS aro_model, r.foreign_key AS aro_foreign_key,
				r.alias AS aro_alias, c.model AS aco_model, c.foreign_key AS aco_foreign_key
				FROM `#__support_acl_aros_acos` AS m
				LEFT JOIN `#__support_acl_aros` AS r ON m.aro_id=r.id
				LEFT JOIN `#__support_acl_acos` AS c ON m.aco_id=c.id";

        $this->db->setQuery($sql);
        $this->rawData = $this->db->loadAssocList();

        if (!$this->user->isGuest()) {
            $this->user_groups = UserHelper::getGroups($this->user->get('id'));
        }
    }

    /**
     * Get the support ACL, creating if not already exists
     *
     * @return  object
     */
    public static function &getACL()
    {
        static $instance;

        if (!is_object($instance)) {
            $instance = new self();
        }

        return $instance;
    }

    /**
     * Check permissions
     *
     * @param   string   $action           Action to check permissions for
     * @param   string   $aco              ACO model (comment, ticket, etc)
     * @param   integer  $aco_foreign_key  Parameter description (if any) ...
     * @param   integer  $aro_foreign_key  User ID
     * @return  integer  1 = allowed, 0 = not allowed
     */
    public function check(
        $action = null,
        $aco = null,
        $aco_foreign_key = null,
        $aro_foreign_key = null
    ) {
        $permission = 0;

        // Check if they are logged in
        if (!$aro_foreign_key && $this->user->isGuest()) {
            return $permission;
        }

        if ($this->user->authorise('core.admin')) {
            return 1;
        }

        if ($aro_foreign_key) {
            $this->setUser($aro_foreign_key);
        }

        // Check user's groups
        if ($this->user_groups && count($this->user_groups) > 0) {
            foreach ($this->user_groups as $ug) {
                foreach ($this->rawData as $line) {
                    // Get the aco permission
                    if (
                        $line['aro_model'] == 'group'
                        && $line['aro_foreign_key'] == $ug->gidNumber
                        && $line['aco_model'] == $aco
                    ) {
                        $actionValue = $line['action_' . $action];
                        $isHigher = $actionValue > $permission;
                        $isNegativeAndZero = $actionValue < 0 && $permission == 0;
                        $permission = ($isHigher || $isNegativeAndZero)
                            ? $actionValue : $permission;
                    }
                    // Get the specific aco model permission if specified (overrides aco permission)
                    if ($aco_foreign_key) {
                        if (
                            $line['aro_model'] == 'group'
                            && $line['aro_foreign_key'] == $ug->gidNumber
                            && $line['aco_model'] == $aco
                            && $line['aco_foreign_key'] == $aco_foreign_key
                        ) {
                            $actionValue = $line['action_' . $action];
                            $isHigher = $actionValue > $permission;
                            $isNegativeAndZero = $actionValue < 0 && $permission == 0;
                            $permission = ($isHigher || $isNegativeAndZero)
                                ? $actionValue : $permission;
                        }
                    }
                }
            }
        }
        $grouppermission = $permission;
        $userspecific = false;
        // Check individual
        $permission = 0;
        foreach ($this->rawData as $line) {
            // Get the aco permission
            if (
                $line['aro_model'] == 'user'
                && $line['aro_foreign_key'] == $this->user->get('id')
                && $line['aco_model'] == $aco
            ) {
                if (isset($line['action_' . $action])) {
                    $actionValue = $line['action_' . $action];
                    $isHigher = $actionValue > $permission;
                    $isNegativeAndZero = $actionValue < 0 && $permission == 0;
                    $permission = ($isHigher || $isNegativeAndZero)
                        ? $actionValue : $permission;
                    $userspecific = true;
                }
            }
            // Get the specific aco model permission if specified (overrides aco permission)
            if ($aco_foreign_key) {
                if (
                    $line['aro_model'] == 'user'
                    && $line['aro_foreign_key'] == $this->user->get('id')
                    && $line['aco_model'] == $aco
                    && $line['aco_foreign_key'] == $aco_foreign_key
                ) {
                    if (isset($line['action_' . $action])) {
                        $actionValue = $line['action_' . $action];
                        $isHigher = $actionValue > $permission;
                        $isNegativeAndZero = $actionValue < 0 && $permission == 0;
                        $permission = ($isHigher || $isNegativeAndZero)
                            ? $actionValue : $permission;
                        $userspecific = true;
                    }
                }
            }
        }

        if ($userspecific) {
            return $permission;
        }

        return $grouppermission;
    }

    /**
     * Set a specific user to check permissions for
     *
     * @param   integer  $aro_foreign_key  User ID
     * @return  void
     */
    public function setUser($aro_foreign_key = null)
    {
        if ($aro_foreign_key) {
            if ($this->user->get('id') != $aro_foreign_key) {
                $this->user = User::getInstance($aro_foreign_key);
                $this->user_groups = UserHelper::getGroups($this->user->get('id'));
            }
        }
    }

    /**
     * Set the permissions for an action
     *
     * @param   string   $action           Action to check permissions for
     * @param   string   $aco              ACO model (comment, ticket, etc)
     * @param   integer  $permission       Permission to set
     * @param   integer  $aco_foreign_key  Parameter description (if any) ...
     * @param   integer  $aro_foreign_key  User ID
     * @return  void
     */
    public function setAccess(
        $action = null,
        $aco = null,
        $permission = null,
        $aco_foreign_key = null,
        $aro_foreign_key = null
    ) {
        if ($aro_foreign_key) {
            $this->setUser($aro_foreign_key);
        }
        $set = false;
        for ($i = 0, $n = count($this->rawData); $i < $n; $i++) {
            $line =& $this->rawData[$i];

            // Get the aco permission
            if (
                $line['aro_model'] == 'user'
                && $line['aro_foreign_key'] == $this->user->get('id')
                && $line['aco_model'] == $aco
            ) {
                $line['action_' . $action] = $permission;
                $set = true;
            }
            // Get the specific aco model permission if specified (overrides aco permission)
            if ($aco_foreign_key) {
                if (
                    $line['aro_model'] == 'user'
                    && $line['aro_foreign_key'] == $this->user->get('id')
                    && $line['aco_model'] == $aco
                    && $line['aco_foreign_key'] == $aco_foreign_key
                ) {
                    $line['action_' . $action] = $permission;
                    $set = true;
                }
            }
        }
        if (!$set) {
            $l = array(
                'aro_model'         => 'user',
                'aro_foreign_key'   => $this->user->get('id'),
                'aco_model'         => $aco,
                'aco_foreign_key'   => $aco_foreign_key,
                'action_' . $action => $permission
            );
            array_push($this->rawData, $l);
        }
    }

    /**
     * Check if a user is in a group
     *
     * @param   string   $group  Group to check
     * @return  boolean  True if in group
     */
    public function authorize($group = null)
    {
        if ($group && $this->user_groups && count($this->user_groups) > 0) {
            foreach ($this->user_groups as $ug) {
                if ($ug->gidNumber == $group) {
                    return true;
                }
            }
        }
        return false;
    }
}
