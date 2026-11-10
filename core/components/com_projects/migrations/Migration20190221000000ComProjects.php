<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Projects\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for changing DATETIME fields default to NULL for com_projects
 **/
class Migration20190221000000ComProjects extends Base
{
    /**
     * List of tables and their datetime fields
     *
     * @var  array
     **/
    public static $tables = [
        '#__project_activity' => [
            'recorded',
        ],
        '#__project_comments' => [
            'created',
        ],
        '#__project_logs' => [
            'time',
        ],
        '#__project_microblog' => [
            'posted',
        ],
        '#__project_owners' => [
            'added',
        ],
        '#__project_repos' => [
            'created',
        ],
        '#__project_stats' => [
            'processed',
        ],
        '#__project_todo' => [
            'created',
        ],
        '#__project_tool_instances' => [
            'created',
        ],
        '#__project_tool_logs' => [
            'recorded',
        ],
        '#__project_tool_views' => [
            'viewed',
        ],
        '#__project_tools' => [
            'created',
        ],
        '#__projects' => [
            'created',
        ],
    ];

    /**
     * Up
     **/
    public function up()
    {
        foreach (self::$tables as $table => $fields) {
            foreach ($fields as $field) {
                if (
                    $this->db->tableExists($table)
                    && $this->db->tableHasField($table, $field)
                ) {
                    $query = "ALTER TABLE `$table` CHANGE `$field` `$field` DATETIME  NULL  DEFAULT NULL";

                    $this->db->setQuery($query);
                    $this->db->query();

                    $query = "UPDATE `$table` SET `$field`=NULL WHERE `$field`='0000-00-00 00:00:00'";

                    $this->db->setQuery($query);
                    $this->db->query();
                }
            }
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        foreach (self::$tables as $table => $fields) {
            foreach ($fields as $field) {
                if (
                    $this->db->tableExists($table)
                    && $this->db->tableHasField($table, $field)
                ) {
                    $query = "ALTER TABLE `$table` CHANGE `$field` `$field` "
                        . "DATETIME  NOT NULL  DEFAULT '0000-00-00 00:00:00'";

                    $this->db->setQuery($query);
                    $this->db->query();
                }
            }
        }
    }
}
