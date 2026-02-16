<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script to add group_owner field to #__publications table
 *
*/
class Migration20140827100656ComPublications extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__publications')
            && !$schema->hasColumn('#__publications', 'group_owner')
        ) {
            $schema->addColumn('#__publications', 'group_owner')->integer(11)->notNull()->default('0');
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__publications') && $schema->hasColumn('#__publications', 'group_owner')) {
            $schema->dropColumn('#__publications', 'group_owner');
        }
    }
}
