<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Creative Commons 4.0 license to resources
  *
**/
class Migration20150901115230ComResources extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__resource_licenses')) {
            $query = $this->db->getQuery(true)
                ->select('id')
                ->from('#__resource_licenses')
                ->where('name', '=', 'cc40-by-nc-sa');
            $id = $query->value('id');

            if (!$id) {
                $query = $this->db->getQuery(true)
                    ->select('ordering')
                    ->from('#__resource_licenses')
                    ->order('ordering', 'desc');
                $ordering = (int) $query->value('ordering');

                $text = "You are free:\n\n"
                    . "to Share — copy and redistribute the material in any medium or format\n"
                    . "to Adapt — remix, transform, and build upon the material\n\n"
                    . "The licensor cannot revoke these freedoms as long as you follow the license terms.\n"
                    . "Under the following terms:\n\n"
                    . "Attribution — You must give appropriate credit, provide a link to the license, "
                    . "and indicate if changes were made. You may do so in any reasonable manner, but "
                    . "not in any way that suggests the licensor endorses you or your use.\n"
                    . "NonCommercial — You may not use the material for commercial purposes.\n"
                    . "ShareAlike — If you remix, transform, or build upon the material, you must "
                    . "distribute your contributions under the same license as the original.\n"
                    . "No additional restrictions — You may not apply legal terms or technological "
                    . "measures that legally restrict others from doing anything the license permits.\n\n"
                    . "Notices:\n"
                    . "You do not have to comply with the license for elements of the material in the "
                    . "public domain or where your use is permitted by an applicable exception or limitation.\n"
                    . "No warranties are given. The license may not give you all of the permissions "
                    . "necessary for your intended use. For example, other rights such as publicity, "
                    . "privacy, or moral rights may limit how you use the material.\n\n"
                    . "For more information visit http://creativecommons.org/licenses/by-nc-sa/4.0/legalcode.";

                $this->db->getQuery(true)
                    ->insert('#__resource_licenses')
                    ->set([
                        'ordering' => $ordering + 1,
                        'name'     => 'cc40-by-nc-sa',
                        'title'    => 'Creative Commons BY-NC-SA 4.0',
                        'url'      => 'http://creativecommons.org/licenses/by-nc-sa/4.0/',
                        'text'     => $text,
                    ])
                    ->execute();
            }
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__resource_licenses')) {
            $query = $this->db->getQuery(true)
                ->select('id')
                ->from('#__resource_licenses')
                ->where('name', '=', 'cc40-by-nc-sa');
            $id = $query->value('id');

            if ($id) {
                // Set the first zone as default
                $this->db->getQuery(true)
                    ->delete('#__resource_licenses')
                    ->where('name', '=', 'cc40-by-nc-sa')
                    ->execute();
            }
        }
    }
}
