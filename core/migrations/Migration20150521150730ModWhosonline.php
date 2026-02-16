<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding extension entry for admin mod_whosonline
**/
class Migration20150521150730ModWhosonline extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__extensions')) {
            $id = $this->db->getQuery(true)
                ->select('extension_id')
                ->from('#__extensions')
                ->where('element', '=', 'mod_whosonline')
                ->where('client_id', '=', 1)
                ->value('extension_id');

            if (!$id) {
                $this->addModuleEntry('mod_whosonline', 1, '', 1);
            } else {
                // Set the first zone as default
                $this->db->getQuery(true)
                    ->update('#__extensions')
                    ->set(['state' => 0])
                    ->where('extension_id', '=', $id)
                    ->execute();
            }
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deleteModuleEntry('mod_whosonline', 1);
    }
}
