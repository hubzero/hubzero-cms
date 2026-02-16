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
 * Migration script for user groups (access control list)
**/
class Migration20130924000007Core extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__user_usergroup_map')) {
            $schema->createTable('#__user_usergroup_map')
                ->unsignedInteger('user_id')->default(0)
                ->unsignedInteger('group_id')->default(0)
                ->primaryKey(['user_id', 'group_id'])
                ->execute();
        }
        if (!$schema->tableExists('#__usergroups')) {
            $schema->createTable('#__usergroups')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->unsignedInteger('parent_id')->default(0)
                ->integer('lft')->default(0)
                ->integer('rgt')->default(0)
                ->string('title', 100)->default('')
                ->primaryKey('id')
                ->uniqueIndex('idx_usergroup_parent_title_lookup', ['parent_id', 'title'])
                ->index('idx_usergroup_title_lookup', 'title')
                ->index('idx_usergroup_adjacency_lookup', 'parent_id')
                ->index('idx_usergroup_nested_set_lookup', ['lft', 'rgt'])
                ->execute();

            // Insert default data
            $usergroups = [
                [1, 0, 1, 20, 'Public'],
                [2, 1, 6, 17, 'Registered'],
                [3, 2, 7, 14, 'Author'],
                [4, 3, 8, 11, 'Editor'],
                [5, 4, 9, 10, 'Publisher'],
                [6, 1, 2, 5, 'Manager'],
                [7, 6, 3, 4, 'Administrator'],
                [8, 1, 18, 19, 'Super Users']
            ];

            foreach ($usergroups as $group) {
                $this->db->getQuery(true)
                    ->insert('#__usergroups')
                    ->values([
                        'id'        => $group[0],
                        'parent_id' => $group[1],
                        'lft'       => $group[2],
                        'rgt'       => $group[3],
                        'title'     => $group[4]
                    ])
                    ->execute();
            }
        }
        if (!$schema->tableExists('#__viewlevels')) {
            $schema->createTable('#__viewlevels')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->string('title', 100)->default('')
                ->integer('ordering')->default(0)
                ->string('rules', 5120)
                ->primaryKey('id')
                ->uniqueIndex('idx_assetgroup_title_lookup', 'title')
                ->execute();

            // Insert default data
            $viewlevels = [
                [1, 'Public', 0, '[1]'],
                [2, 'Registered', 1, '[6,2,8]'],
                [3, 'Special', 2, '[6,3,8]']
            ];

            foreach ($viewlevels as $level) {
                $this->db->getQuery(true)
                    ->insert('#__viewlevels')
                    ->values([
                        'id'       => $level[0],
                        'title'    => $level[1],
                        'ordering' => $level[2],
                        'rules'    => $level[3]
                    ])
                    ->execute();
            }

            // Update access levels on a few Joomla things as needed
            $tables = ['#__categories', '#__contact_details', '#__content', '#__menu', '#__modules'];
            foreach ($tables as $table) {
                $this->db->getQuery(true)
                    ->update($table)
                    ->set(['access' => Expression::column('access')->plus(1)])
                    ->execute();
            }

            // Add rows to usergroup map table for existing users
            $results = $this->db->getQuery(true)
                ->select(['id', 'usertype'])
                ->from('#__users')
                ->loadObjectList();

            if (count($results) > 0) {
                foreach ($results as $r) {
                    // Map old names to new
                    switch ($r->usertype) {
                        case 'Registered':
                        case 'Author':
                        case 'Editor':
                        case 'Publisher':
                        case 'Manager':
                        case 'Administrator':
                        case 'Super Administrator':
                            // Mapping logic based on index
                            $m = [
                                'Registered'          => 2,
                                'Author'              => 3,
                                'Editor'              => 4,
                                'Publisher'           => 5,
                                'Manager'             => 6,
                                'Administrator'       => 7,
                                'Super Administrator' => 8
                            ];
                            $group_id = $m[$r->usertype];
                            break;
                        default:
                            $group_id = 2;
                            break;
                    }
                    $this->db->getQuery(true)
                        ->insert('#__user_usergroup_map')
                        ->values([
                            'user_id'  => $r->id,
                            'group_id' => $group_id
                        ])
                        ->execute();
                }
            }

            // Update user params (specifically to remove timezone)
            $results = $this->db->getQuery(true)
                ->select(['id', 'params'])
                ->from('#__users')
                ->loadObjectList();

            if (count($results) > 0) {
                foreach ($results as $r) {
                    $params = trim($r->params);
                    if (empty($params) || $params == '{}') {
                        continue;
                    }

                    $array = array();
                    $ar    = explode("\n", $params);

                    foreach ($ar as $a) {
                        $a = trim($a);
                        if (empty($a)) {
                            continue;
                        }

                        $ar2     = explode("=", $a, 2);
                        if ($ar2[0] == 'timezone' && is_numeric($ar2[1])) {
                            $ar2[1] = "";
                        }
                        $array[$ar2[0]] = (isset($ar2[1])) ? $ar2[1] : '';
                    }

                    $this->db->getQuery(true)
                        ->update('#__users')
                        ->set(['params' => json_encode($array)])
                        ->where('id', '=', $r->id)
                        ->execute();
                }
            }
        }

        if (!$schema->tableExists('#__user_profiles')) {
            $schema->createTable('#__user_profiles')
                ->integer('user_id')
                ->string('profile_key', 100)
                ->string('profile_value', 255)
                ->integer('ordering')->default(0)
                ->uniqueIndex('idx_user_id_profile_key', ['user_id', 'profile_key'])
                ->execute();
        }

        if (!$schema->hasColumn('#__users', 'lastResetTime') && $schema->hasColumn('#__users', 'params')) {
            $schema->addColumn('#__users', 'lastResetTime')
                ->datetime()
                ->notNull()
                ->default('0000-00-00 00:00:00')
                ->execute();
        }
        if (
            !$schema->hasColumn('#__users', 'resetCount')
            && $schema->hasColumn('#__users', 'lastResetTime')
        ) {
            $schema->addColumn('#__users', 'resetCount')->integer()->notNull()->default(0)->execute();
        }
        $schema->addIndex('#__users', 'idx_block', 'block');
        $schema->dropIndex('#__users', 'gid_block');
        if ($schema->tableExists('#__core_acl_groups_aro_map')) {
            $schema->dropTable('#__core_acl_groups_aro_map');
        }
        if ($schema->tableExists('#__core_acl_aro_sections')) {
            $schema->dropTable('#__core_acl_aro_sections');
        }
        if ($schema->tableExists('#__core_acl_aro_map')) {
            $schema->dropTable('#__core_acl_aro_map');
        }
        if ($schema->tableExists('#__core_acl_aro_groups')) {
            $schema->dropTable('#__core_acl_aro_groups');
        }
        if ($schema->tableExists('#__core_acl_aro')) {
            $schema->dropTable('#__core_acl_aro');
        }
        if ($schema->tableExists('#__groups')) {
            $schema->dropTable('#__groups');
        }
    }
}
