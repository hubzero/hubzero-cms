<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding comment field to curation history table
**/
class Migration20141117095313ComPublications extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__publication_curation_history')) {
            if (!$schema->hasColumn('#__publication_curation_history', 'comment')) {
                $schema->addColumn('#__publication_curation_history', 'comment')->text();
            }
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__publication_curation_history')) {
            if ($schema->hasColumn('#__publication_curation_history', 'comment')) {
                $schema->dropColumn('#__publication_curation_history', 'comment');
            }
        }
    }
}
