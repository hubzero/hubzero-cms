<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for updating scope on existing migration entries
**/
class Migration20150612203219Migrations extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__migrations')) {
            $this->db->getQuery(true)
                ->update('#__migrations')
                ->set(['scope' => $this->db->quote('core/migrations')])
                ->where('scope', '=', 'migrations')
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__migrations')) {
            $this->db->getQuery(true)
                ->update('#__migrations')
                ->set(['scope' => $this->db->quote('migrations')])
                ->where('scope', '=', 'core/migrations')
                ->execute();
        }
    }
}
