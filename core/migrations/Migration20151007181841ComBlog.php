<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

// Restricted access
/**
 * Migration script for making Blog Entry state and access conform to standard conventions
 *
*/
class Migration20151007181841ComBlog extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__blog_entries') && $schema->hasColumn('#__blog_entries', 'access')) {
            // Public entries
            $this->db->getQuery(true)
                ->update('#__blog_entries')
                ->set(['access' => 1])
                ->where('state', '=', 1)
                ->execute();

            // Registered entries
            $this->db->getQuery(true)
                ->update('#__blog_entries')
                ->set(['access' => 2])
                ->where('state', '=', 2)
                ->execute();

            // Private entries
            $this->db->getQuery(true)
                ->update('#__blog_entries')
                ->set(['access' => 5])
                ->where('state', '=', 0)
                ->execute();

            // All entries are "published"
            $this->db->getQuery(true)
                ->update('#__blog_entries')
                ->set(['state' => 1])
                ->where('state', '>=', 0)
                ->execute();

            // Change the state of trashed entries
            $this->db->getQuery(true)
                ->update('#__blog_entries')
                ->set(['state' => 2])
                ->where('state', '<', 0)
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__blog_entries') && $schema->hasColumn('#__blog_entries', 'access')) {
            // Public entries
            $this->db->getQuery(true)
                ->update('#__blog_entries')
                ->set(['state' => 1])
                ->where('access', '=', 1)
                ->execute();

            // Registered entries
            $this->db->getQuery(true)
                ->update('#__blog_entries')
                ->set(['state' => 2])
                ->where('access', '=', 2)
                ->execute();

            // Private entries
            $this->db->getQuery(true)
                ->update('#__blog_entries')
                ->set(['state' => 0])
                ->where('access', '=', 5)
                ->execute();

            // Change the state of trashed entries
            $this->db->getQuery(true)
                ->update('#__blog_entries')
                ->set(['state' => -1])
                ->where('state', '=', 2)
                ->execute();
        }
    }
}
