<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for removing unused flag from publication_licenses
 *
*/
class Migration20150722090000ComPublications extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__publication_licenses')
            && $schema->hasColumn('#__publication_licenses', 'apps_only')
        ) {
            $schema->dropColumn('#__publication_licenses', 'apps_only');
        }
    }
}
