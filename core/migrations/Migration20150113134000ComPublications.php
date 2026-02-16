<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script to remove 'ark' column from #__publication_versions
**/
class Migration20150113134000ComPublications extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__publication_versions')
            && $schema->hasColumn('#__publication_versions', 'ark')
        ) {
            $schema->dropColumn('#__publication_versions', 'ark');
        }
    }
}
