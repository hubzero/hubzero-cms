<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
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
     *
     * @return  void
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
