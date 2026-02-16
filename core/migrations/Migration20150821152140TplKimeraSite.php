<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding site Kimera template
**/
class Migration20150821152140TplKimeraSite extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__extensions')) {
            $query = $this->db->getQuery(true)
                ->select('extension_id')
                ->from('#__extensions')
                ->where('element', '=', 'kimera')
                ->where('type', '=', 'template');
            $id = $query->value('extension_id');

            if (!$id) {
                $this->addTemplateEntry('kimera', 'Kimera (site)', 0);

                $this->db->getQuery(true)
                    ->update('#__extensions')
                    ->set(['protected' => 1])
                    ->where('element', '=', 'kimera')
                    ->where('type', '=', 'template')
                    ->execute();
            }
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deleteTemplateEntry('kimera', 0);
    }
}
