<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script to add tables to associate sessions allowed with user groups
 *
 */
class Migration20150326183839ComTools extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__tool_session_classes')) {
            $schema->createTable('#__tool_session_classes')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->string('alias', 255)->default('')
                ->integer('jobs')->default(0)
                ->primaryKey('id')
                ->uniqueIndex('uidx_alias', 'alias')
                ->engine('InnoDB')
                ->charset('utf8')
                ->execute();

            $this->db->getQuery(true)
                ->insert('#__tool_session_classes')
                ->set([
                    'alias' => 'default',
                    'jobs'  => 3
                ])
                ->execute();
        }

        if (!$schema->tableExists('#__tool_session_class_groups')) {
            $schema->createTable('#__tool_session_class_groups')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->unsignedInteger('class_id')->default(0)
                ->unsignedInteger('group_id')->default(0)
                ->primaryKey('id')
                ->index('idx_class_id', 'class_id')
                ->index('idx_group_id', 'group_id')
                ->engine('InnoDB')
                ->charset('utf8')
                ->execute();
        }

        if ($schema->tableExists('#__users_tool_preferences')) {
            if (!$schema->hasColumn('#__users_tool_preferences', 'class_id')) {
                $schema->addColumn('#__users_tool_preferences', 'class_id')
                    ->integer()
                    ->notNull()
                    ->default(0)
                    ->execute();

                $schema->addIndex('#__users_tool_preferences', 'idx_class_id', 'class_id');
            }

            if (!$schema->hasColumn('#__users_tool_preferences', 'jobs')) {
                $schema->addColumn('#__users_tool_preferences', 'jobs')->integer()->notNull()->default(0)->execute();
            }
        }

        if ($schema->tableExists('#__users_tool_preferences')) {
            if ($schema->hasColumn('#__xprofiles', 'jobsAllowed')) {
                // Create a preferences entry for anyone who has a non-default value for jobs allowed
                $rows = $this->db->getQuery(true)
                    ->select(['uidNumber', 'jobsAllowed'])
                    ->from('#__xprofiles')
                    ->where('jobsAllowed', '!=', 3)
                    ->where('uidNumber', '>', 0)
                    ->loadObjectList();

                if ($rows) {
                    foreach ($rows as $row) {
                        $exists = $this->db->getQuery(true)
                            ->select('id')
                            ->from('#__users_tool_preferences')
                            ->where('user_id', '=', $row->uidNumber)
                            ->exists();

                        $jobs = ($row->jobsAllowed ? $row->jobsAllowed : 10);

                        if ($exists) {
                            $this->db->getQuery(true)
                                ->update('#__users_tool_preferences')
                                ->set([
                                    'class_id' => 0,
                                    'jobs'     => $jobs,
                                ])
                                ->where('user_id', '=', $row->uidNumber)
                                ->execute();
                        } else {
                            $this->db->getQuery(true)
                                ->insert('#__users_tool_preferences')
                                ->set([
                                    'user_id'  => $row->uidNumber,
                                    'class_id' => 0,
                                    'jobs'     => $jobs,
                                ])
                                ->execute();
                        }
                    }
                }

                $schema->dropColumn('#__xprofiles', 'jobsAllowed');
            }
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        $schema->dropTable('#__tool_session_classes');
        $schema->dropTable('#__tool_session_class_groups');

        if ($schema->tableExists('#__users_tool_preferences')) {
            if (!$schema->hasColumn('#__xprofiles', 'jobsAllowed')) {
                $schema->addColumn('#__xprofiles', 'jobsAllowed')->integer()->notNull()->default(0)->execute();
            }

            // Create a preferences entry for anyone who has a non-default value for jobs allowed
            $rows = $this->db->getQuery(true)
                ->select('*')
                ->from('#__users_tool_preferences')
                ->where('jobs', '!=', 3)
                ->loadObjectList();

            if ($rows) {
                foreach ($rows as $row) {
                    $this->db->getQuery(true)
                        ->update('#__xprofiles')
                        ->set(['jobsAllowed' => $row->jobs])
                        ->where('uidNumber', '=', $row->user_id)
                        ->execute();
                }
            }

            $this->db->getQuery(true)
                ->update('#__xprofiles')
                ->set(['jobsAllowed' => 3])
                ->where('jobsAllowed', '=', 0)
                ->execute();

            if ($schema->hasColumn('#__users_tool_preferences', 'class_id')) {
                $schema->dropColumn('#__users_tool_preferences', 'class_id');
            }

            if ($schema->hasColumn('#__users_tool_preferences', 'jobs')) {
                $schema->dropColumn('#__users_tool_preferences', 'jobs');
            }
        }
    }
}
