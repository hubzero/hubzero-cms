<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding project tool tables
  *
**/
class Migration20150401110000ComProjects extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__project_tools')) {
            $schema->createTable('#__project_tools')
                ->integer('id', ['autoIncrement' => true])
                ->string('name', 64)->default('')
                ->string('title', 127)->default('')
                ->tinyInteger('repotype')->default(1)
                ->string('repopath', 255)->default('')
                ->integer('status')->default(0)
                ->string('status_changed', 31)
                ->integer('status_changed_by')
                ->datetime('created')
                ->integer('created_by')
                ->integer('svntool_id')
                ->integer('project_id')
                ->tinyInteger('published')->default(0)
                ->tinyInteger('opendev')->default(0)
                ->tinyInteger('opensource')->default(0)
                ->primaryKey('id')
                ->uniqueIndex('toolname', 'name')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }
        if (!$schema->tableExists('#__project_tool_instances')) {
            $schema->createTable('#__project_tool_instances')
                ->integer('id', ['autoIncrement' => true])
                ->integer('parent_id')->default(0)
                ->string('parent_name', 64)->default('')
                ->string('instance', 100)->default('')
                ->integer('revision')
                ->string('commit', 255)
                ->string('access', 16)
                ->integer('state')
                ->integer('created_by')
                ->datetime('created')
                ->integer('modified_by')->nullable()
                ->datetime('modified')->nullable()
                ->integer('svntool_version_id')->nullable()
                ->text('params')->nullable()
                ->primaryKey('id')
                ->uniqueIndex('toolname', ['parent_name', 'instance'])
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }
        if (!$schema->tableExists('#__project_tool_logs')) {
            $schema->createTable('#__project_tool_logs')
                ->integer('id', ['autoIncrement' => true])
                ->integer('parent_id')->default(0)
                ->string('parent_name', 64)->default('')
                ->integer('instance_id')->nullable()
                ->string('action', 255)->default('')
                ->integer('actor')
                ->datetime('recorded')
                ->integer('project_activity_id')->default(0)
                ->tinyInteger('status_changed')->default(0)
                ->tinyInteger('admin')->default(0)
                ->tinyInteger('access')->default(0)
                ->text('log')->nullable()
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }
        if (!$schema->tableExists('#__project_tool_views')) {
            $schema->createTable('#__project_tool_views')
                ->integer('id', ['autoIncrement' => true])
                ->integer('parent_id')->default(0)
                ->integer('userid')
                ->datetime('viewed')
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }
        if (!$schema->tableExists('#__project_tool_statuses')) {
            $schema->createTable('#__project_tool_statuses')
                ->integer('id', ['autoIncrement' => true])
                ->string('status', 100)->default('')
                ->text('status_about')->nullable()
                ->string('next', 100)->default('')
                ->string('next_admin', 100)->default('')
                ->text('next_about')->nullable()
                ->tinyInteger('next_actor')->default(0)
                ->string('wait_time', 100)->default('')
                ->text('options')->nullable()
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();

            $this->db->getQuery(true)
                ->insert('#__project_tool_statuses')
                ->columns([
                    'id',
                    'status',
                    'status_about',
                    'next',
                    'next_admin',
                    'next_about',
                    'next_actor',
                    'wait_time',
                    'options',
                ])
                ->values([
                    [
                        1,
                        'created',
                        '',
                        'upload your code and request install',
                        'wait for develper to upload code',
                        'You need to upload your code into the tool <a '
                            . 'href="{app-source}">repository</a>. When your code is ready for install, notify '
                            . 'administrator via this screen.',
                        0,
                        '',
                        '{"option-message":"1","option-cancel":"1"}',
                    ],
                    [2, 'deleted', '', '', '', '', 2, '', ''],
                    [
                        3,
                        'uploaded',
                        '',
                        'wait for admin to install latest code',
                        'install developer code',
                        'Administrator need to intsall your uploaded code. You will get notified when the '
                            . 'code is installed.',
                        1,
                        '24hrs',
                        '{"option-message":"1"}',
                    ],
                    [
                        4,
                        'installed',
                        '',
                        'test installed code',
                        'wait for develper to test code',
                        'Test your code to make sure it is working as expected. Make further changes and '
                            . 'request install if needed, or let administrator know the tool is working '
                            . 'properly.',
                        0,
                        '',
                        '{"option-message":"1"}',
                    ],
                    [
                        5,
                        'broken',
                        '',
                        'fix the code',
                        'wait for develper to fix code',
                        'There is a problem with your code that needs to be fixed before it can be installed.',
                        0,
                        '',
                        '{"option-message":"1"}',
                    ],
                    [
                        7,
                        'working',
                        '',
                        '',
                        '',
                        'The tool is working and can now be published. If you need administrator to '
                            . 'install an update, request install via this screen.',
                        2,
                        '',
                        '{"option-message":"1"}',
                    ],
                    [8, 'retired', '', '', '', 'You tool is retired. ', 2, '', '{"option-message":"1"}']
                ])
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
    }
}
