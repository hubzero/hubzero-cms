<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding publication version fulltext key
 *
*/
class Migration20131106154023ComPublications extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        $schema->addFulltextIndex('#__publication_versions', 'idx_fulltxt_title_description_abstract', [
            'title',
            'description',
            'abstract',
        ]);
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        $schema->dropIndex('#__publication_versions', 'idx_fulltxt_title_description_abstract');
    }
}
