<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for tracking newsletter email bounces
**/
class Migration20130814185755ComNewsletter extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__email_bounces')) {
            $schema->createTable('#__email_bounces')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->string('email', 150)->nullable()
                ->string('component', 100)->nullable()
                ->string('object', 100)->nullable()
                ->integer('object_id')->nullable()
                ->text('reason')->nullable()
                ->datetime('date')->nullable()
                ->integer('resolved')->default(0)
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        $this->db->getQuery(true)
            ->update('#__cron_jobs')
            ->set(['params' => ''])
            ->where('plugin', '=', 'newsletter')
            ->where('event', '=', 'processMailings')
            ->whereLike('params', "newsletter_queue_limit=2\n")
            ->execute();
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__email_bounces')) {
            $schema->dropTable('#__email_bounces');
        }
    }
}
