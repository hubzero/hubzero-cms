<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration to add id and primary key to `#__stats_topvals` table
 *
**/
class Migration20180924162929Core extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__stats_topvals')) {
            if (!$schema->hasColumn('#__stats_topvals', 'id')) {
                $schema->addAutoIncrementPrimaryKey('#__stats_topvals', 'id', true, false);
            }
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__stats_topvals')) {
            if ($schema->hasColumn('#__stats_topvals', 'id')) {
                $schema->dropColumn('#__stats_topvals', 'id');
            }
        }
    }
}
