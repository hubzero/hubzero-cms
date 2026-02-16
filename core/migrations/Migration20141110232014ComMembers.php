<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for fixing members names that dont have individual names filled in.
 *
*/
class Migration20141110232014ComMembers extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        // load all members without given name or surname filled in
        $result = $this->db->getQuery(true)
            ->select(['uidNumber', 'username', 'name', 'surname', 'givenName', 'middleName'])
            ->from('#__xprofiles')
            ->beginOrGroup()
                ->where('givenName', '=', '')
                ->orWhereIsNull('givenName')
            ->endAndGroup()
            ->beginOrGroup()
                ->where('surname', '=', '')
                ->orWhereIsNull('surname')
            ->endAndGroup()
            ->loadObjectList();

        // fix each name
        foreach ($result as $profile) {
            $firstname  = $profile->givenName;
            $middlename = $profile->middleName;
            $lastname   = $profile->surname;
            $name       = $profile->name;
            $username   = $profile->username;

            // all good
            if ($firstname && $surname) {
                continue;
            }

            if (empty($firstname) && empty($middlename) && empty($surname) && empty($name)) {
                $name = $this->db->getQuery(true)
                    ->select('name')
                    ->from('#__users')
                    ->where('id', '=', $profile->uidNumber)
                    ->value('name');

                $name = $name ?: $username;
                $firstname = $username;
            }

            if (empty($firstname) && empty($middlename) && empty($surname)) {
                $words = array_map('trim', explode(' ', $name));
                $count = count($words);

                if ($count == 1) {
                    $firstname = $words[0];
                } elseif ($count == 2) {
                    $firstname = $words[0];
                    $lastname  = $words[1];
                } elseif ($count == 3) {
                    $firstname  = $words[0];
                    $middlename = $words[1];
                    $lastname   = $words[2];
                } else {
                    $firstname  = $words[0];
                    $lastname   = $words[$count - 1];
                    $middlename = $words[1];

                    for ($i = 2; $i < $count - 1; $i++) {
                        $middlename .= ' ' . $words[$i];
                    }
                }

                // TODO:
                // if firstname all caps, and lastname isn't, switch them
                // reparse names with " de , del ,  in them
            }

            $firstname  = trim($firstname);
            $middlename = trim($middlename);
            $lastname   = trim($lastname);

            // update name
            $this->db->getQuery(true)
                ->update('#__xprofiles')
                ->set([
                    'name'       => $name,
                    'givenName'  => $firstname,
                    'middleName' => $middlename,
                    'surname'    => $lastname
                ])
                ->where('uidNumber', '=', $profile->uidNumber)
                ->execute();
        }
    }
}
