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
 * Migration script for updating tables for code refactoring based on new models
 *
*/
class Migration20150109190645ComTime extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        // Rename table fields to work with new models structure
        if ($schema->tableExists('#__time_tasks')) {
            if (
                $schema->hasColumn('#__time_tasks', 'liaison')
                && !$schema->hasColumn('#__time_tasks', 'liaison_id')
            ) {
                $schema->renameColumn('#__time_tasks', 'liaison', 'liaison_id')
                    ->integer()
                    ->nullable()
                    ->default(null)
                    ->execute();
            }

            if (
                $schema->hasColumn('#__time_tasks', 'assignee')
                && !$schema->hasColumn('#__time_tasks', 'assignee_id')
            ) {
                $schema->renameColumn('#__time_tasks', 'assignee', 'assignee_id')
                    ->integer()
                    ->nullable()
                    ->default(null)
                    ->execute();
            }
        }

        // Create new table #__time_liaisons
        if (!$schema->tableExists('#__time_liaisons')) {
            $schema->createTable('#__time_liaisons')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->integer('user_id')->nullable()
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();

            // Add rows to table from old time_users table
            $liaisons = $this->db->getQuery(true)
                ->select('*')
                ->from('#__time_users')
                ->where('liaison', '=', 1)
                ->loadObjectList();

            if ($liaisons && count($liaisons) > 0) {
                foreach ($liaisons as $liaison) {
                    $this->db->getQuery(true)
                        ->insert('#__time_liaisons')
                        ->columns(['user_id'])
                        ->values((int)$liaison->user_id)
                        ->execute();
                }
            }
        }

        // Drop liaison column from time_users table
        if ($schema->tableExists('#__time_users') && $schema->hasColumn('#__time_users', 'liaison')) {
            $schema->dropColumn('#__time_users', 'liaison');
        }

        // Rename time_users table to time_managers table
        if ($schema->tableExists('#__time_users') && !$schema->tableExists('#__time_proxies')) {
            $schema->renameTable('#__time_users', '#__time_proxies');
        }

        if (
            $schema->tableExists('#__time_proxies')
            &&  $schema->hasColumn('#__time_proxies', 'manager_id')
            && !$schema->hasColumn('#__time_proxies', 'proxy_id')
        ) {
            $schema->renameColumn('#__time_proxies', 'manager_id', 'proxy_id')
                ->integer()
                ->nullable()
                ->default(null)
                ->execute();
        }

        // Delete entries formally present just to represent a liaison, not an actually proxy relationship
        if ($schema->tableExists('#__time_proxies')) {
            $this->db->getQuery(true)
                ->delete('#__time_proxies')
                ->where('proxy_id', '=', 0)
                ->execute();
        }

        // Fix up entries in assets table to be singular
        $this->db->getQuery(true)
            ->update('#__assets')
            ->set(['name' => Expression::replace(Expression::column('name'), 'com_time.hubs.', 'com_time.hub.')])
            ->execute();
    }
}
