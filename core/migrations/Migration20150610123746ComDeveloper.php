<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding tables for developer component
 *
*/
class Migration20150610123746ComDeveloper extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        // add applications table
        if (!$schema->tableExists('#__developer_applications')) {
            $schema->createTable('#__developer_applications')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->string('name', 255)->default('')
                ->text('description')->nullable()
                ->string('client_id', 80)->nullable()
                ->string('client_secret', 80)
                ->string('redirect_uri', 2000)
                ->string('grant_types', 80)->nullable()
                ->datetime('created')->nullable()
                ->integer('created_by')->nullable()
                ->integer('state')->default(1)
                ->integer('hub_account')->default(0)
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        // add application team members table
        if (!$schema->tableExists('#__developer_application_team_members')) {
            $schema->createTable('#__developer_application_team_members')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->integer('uidNumber')->nullable()
                ->integer('application_id')->nullable()
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        // add access token table
        if (!$schema->tableExists('#__developer_access_tokens')) {
            $schema->createTable('#__developer_access_tokens')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->integer('application_id')
                ->string('access_token', 40)
                ->integer('uidNumber')->nullable()
                ->datetime('expires')
                ->datetime('created')->nullable()
                ->string('scope', 2000)->nullable()
                ->integer('state')->default(1)
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        // add authorization codes table
        if (!$schema->tableExists('#__developer_authorization_codes')) {
            $schema->createTable('#__developer_authorization_codes')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->integer('application_id')
                ->string('authorization_code', 40)
                ->integer('uidNumber')->nullable()
                ->string('redirect_uri', 2000)->nullable()
                ->datetime('expires')
                ->string('scope', 2000)->nullable()
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        // add refresh tokens table
        if (!$schema->tableExists('#__developer_refresh_tokens')) {
            $schema->createTable('#__developer_refresh_tokens')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->integer('application_id')
                ->string('refresh_token', 40)
                ->integer('uidNumber')->nullable()
                ->datetime('expires')
                ->datetime('created')->nullable()
                ->string('scope', 2000)->nullable()
                ->integer('state')->default(1)
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        // remove applications table
        if ($schema->tableExists('#__developer_applications')) {
            $schema->dropTable('#__developer_applications');
        }

        // remove application team members table
        if ($schema->tableExists('#__developer_application_team_members')) {
            $schema->dropTable('#__developer_application_team_members');
        }

        // remove access token table
        if ($schema->tableExists('#__developer_access_tokens')) {
            $schema->dropTable('#__developer_access_tokens');
        }

        // remove authorization codes table
        if ($schema->tableExists('#__developer_authorization_codes')) {
            $schema->dropTable('#__developer_authorization_codes');
        }

        // remove refresh tokens table
        if ($schema->tableExists('#__developer_refresh_tokens')) {
            $schema->dropTable('#__developer_refresh_tokens');
        }
    }
}
