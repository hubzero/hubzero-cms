<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script to add 'curator' column to #__publication_versions
  *
**/
class Migration20150113130000ComPublications extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__publication_versions')) {
            if (!$schema->hasColumn('#__publication_versions', 'curator')) {
                $schema->addColumn('#__publication_versions', 'curator')->integer(11);
            }
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__publication_versions')) {
            if ($schema->hasColumn('#__publication_versions', 'curator')) {
                $schema->dropColumn('#__publication_versions', 'curator');
            }
        }
    }
}
