<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding general announcements table and hiding group messaging tab
  *
**/
class Migration20130619181459PlgGroupsAnnouncements extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__announcements')) {
            $schema->createTable('#__announcements')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->string('scope', 100)->nullable()
                ->integer('scope_id')->nullable()
                ->text('content')->nullable()
                ->tinyInteger('priority')->default(0)
                ->datetime('created')->default('0000-00-00 00:00:00')
                ->integer('created_by')->default(0)
                ->tinyInteger('state')->default(0)
                ->datetime('publish_up')->default('0000-00-00 00:00:00')
                ->datetime('publish_down')->default('0000-00-00 00:00:00')
                ->tinyInteger('sticky')->default(0)
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        $params = array(
            'plugin_access' => 'members',
            'display_tab'   => 1
        );

        $this->addPluginEntry('groups', 'announcements', 1, $params);

        // get citation params
        if ($schema->tableExists('#__extensions')) {
            $p = $this->db->getQuery(true)
                ->select('params')
                ->from('#__extensions')
                ->where('type', '=', 'plugin')
                ->where('element', '=', 'messages')
                ->where('folder', '=', 'groups')
                ->value('params');
        } else {
            $p = $this->db->getQuery(true)
                ->select('params')
                ->from('#__plugins')
                ->where('element', '=', 'messages')
                ->where('folder', '=', 'groups')
                ->value('params');
        }

        // load params object
        $params = new \Hubzero\Config\Registry($p);

        // set param to hide messages tab
        $params->set('display_tab', 0);

        // save new params
        if ($schema->tableExists('#__extensions')) {
            $this->db->getQuery(true)
                ->update('#__extensions')
                ->set(['params' => json_encode($params->toArray())])
                ->where('element', '=', 'messages')
                ->where('folder', '=', 'groups')
                ->execute();
        } else {
            $this->db->getQuery(true)
                ->update('#__plugins')
                ->set(['params' => $params->toString()])
                ->where('element', '=', 'messages')
                ->where('folder', '=', 'groups')
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        $schema->dropTable('#__announcements');

        $this->deletePluginEntry('groups', 'announcements');
    }
}
