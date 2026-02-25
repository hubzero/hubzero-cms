<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;
use Hubzero\Database\Expression;

/**
 * Migration script for moving data from #__xprofiles to #__user_profiles
  *
**/
class Migration20160513140417ComMembers extends Base
{
    /**
     * Profile fields to move to new profiles table
     *
     * @var  array
     */
    public static $moveToProfile = array(
        'orgtype',
        'organization',
        'countryresident',
        'countryorigin',
        'gender',
        'url',
        'reason',
        'nativeTribe',
        'phone',
        'orcid'
    );

    /**
     * Multi-value Profile fields to move to new profiles table
     *
     * @var  array
     */
    public static $moveToProfileMulti = array(
        'disability',
        'edulevel',
        'hispanic',
        'race',
        'bio'
    );

    /**
     * Profile fields to move to users table
     *
     * @var  array
     */
    public static $moveToUser = array(
        'mailPreferenceOption' => 'sendMail',
        'usageAgreement' => 'usageAgreement',
        'emailConfirmed' => 'activation',
        'regIP' => 'registerIP',
        'regHost' => 'registerHost',
        'homeDirectory' => 'homeDirectory',
        'loginShell' => 'loginShell',
        'ftpShell' => 'ftpShell'
    );

    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__xprofiles')) {
            if ($schema->tableExists('#__users')) {
                // Update users table with profile data
                $results = $this->db->getQuery(true)
                    ->select('*')
                    ->from('#__xprofiles')
                    ->loadObjectList();

                if ($results) {
                    foreach ($results as $result) {
                        $this->db->getQuery(true)
                            ->update('#__users')
                            ->set([
                                'homeDirectory' => $result->homeDirectory,
                                'loginShell'    => $result->loginShell,
                                'ftpShell'      => $result->ftpShell,
                                'usageAgreement' => $result->usageAgreement,
                                'activation'     => $result->emailConfirmed,
                                'registerIP'     => $result->regIP,
                                'access'         => $result->public,
                                'sendEmail'      => $result->mailPreferenceOption
                            ])
                            ->where('id', '=', (int) $result->uidNumber)
                            ->execute();
                    }
                }

                $this->db->getQuery(true)
                    ->update('#__users')
                    ->set(['access' => 5])
                    ->where('id', 'IN', function ($query) {
                        $query->select('uidNumber')
                            ->from('#__xprofiles')
                            ->where('public', '=', 0);
                    })
                    ->execute();
            }

            $schema = $this->db->schema();

            if ($schema->tableExists('#__user_profiles')) {
                if (!$schema->hasColumn('#__user_profiles', 'access')) {
                    $schema->addColumn('#__user_profiles', 'access')->integer(10)->notNull()->default(0);
                }

                $fields = array();
                if ($schema->tableExists('#__user_profile_fields')) {
                    $fields = $this->db->getQuery(true)
                        ->select('*')
                        ->from('#__user_profile_fields')
                        ->loadAssocList('name');
                }

                foreach (self::$moveToProfile as $field) {
                    $ordering = 0;
                    $access   = 5;

                    if (isset($fields[$field])) {
                        $ordering = (int)$fields[$field]['ordering'];
                        $access   = (int)$fields[$field]['access'];
                    }

                    $subquery = $this->db->getQuery(true)
                        ->select([
                            'uidNumber',
                            Expression::literal($field),
                            Expression::column($field),
                            Expression::literal((int)$ordering),
                            Expression::literal((int)$access),
                        ])
                        ->from('#__xprofiles')
                        ->whereNotNull($field)
                        ->where($field, '!=', '');

                    $this->db->getQuery(true)
                        ->insert('#__user_profiles')
                        ->columns(['user_id', 'profile_key', 'profile_value', 'ordering', 'access'])
                        ->select($subquery)
                        ->execute();
                }

                foreach (self::$moveToProfileMulti as $field) {
                    if (!$schema->tableExists('#__xprofiles_' . $field)) {
                        continue;
                    }

                    $ordering = 0;
                    $access   = 5;

                    if (isset($fields[$field])) {
                        $ordering = (int)$fields[$field]['ordering'];
                        $access   = (int)$fields[$field]['access'];
                    }

                    $subquery = $this->db->getQuery(true)
                        ->select([
                            'uidNumber',
                            Expression::literal($field),
                            Expression::column($field),
                            Expression::literal((int)$ordering),
                            Expression::literal((int)$access)
                        ])
                        ->from('#__xprofiles_' . $field)
                        ->whereNotNull($field)
                        ->where($field, '!=', '');

                    $this->db->getQuery(true)
                        ->insert('#__user_profiles')
                        ->columns(['user_id', 'profile_key', 'profile_value', 'ordering', 'access'])
                        ->select($subquery)
                        ->execute();
                }
            }

            if ($schema->tableExists('#__xprofiles_address')) {
                $addresses = $this->db->getQuery(true)
                    ->select('*')
                    ->from('#__xprofiles_address')
                    ->loadObjectList();

                $ordering = 0;
                $access   = 5;

                if (isset($fields[$field])) {
                    $ordering = (int)$fields['address']['ordering'];
                    $access   = (int)$fields['address']['access'];
                }

                foreach ($addresses as $address) {
                    $a = new \stdClass();
                    $a->address1  = $address->address1;
                    $a->address2  = $address->address2;
                    $a->city      = $address->addressCity;
                    $a->region    = $address->addressRegion;
                    $a->postal    = $address->addressPostal;
                    $a->country   = $address->addressCountry;
                    $a->latitude  = $address->addressLatitude;
                    $a->longitude = $address->addressLongitude;

                    $id = $address->uidNumber;

                    $this->db->getQuery(true)
                        ->insert('#__user_profiles')
                        ->values([
                            'user_id'       => $id,
                            'profile_key'   => 'address',
                            'profile_value' => json_encode($a),
                            'ordering'      => $ordering,
                            'access'        => $access
                        ])
                        ->execute();
                }
            }
        }
    }
}
