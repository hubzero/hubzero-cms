<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for enabling the plugin by default.
**/
class Migration20160810121746PlgContentFormathtml extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__extensions')) {
            $this->db
                ->getQuery()
                ->update('#__extensions')
                ->set(['enabled' => 1])
                ->where('folder', '=', 'content')
                ->where('element', '=', 'formathtml')
                ->execute();
        }
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__extensions')) {
            $this->db
                ->getQuery()
                ->update('#__extensions')
                ->set(['enabled' => 0])
                ->where('folder', '=', 'content')
                ->where('element', '=', 'formathtml')
                ->execute();
        }
    }
}
