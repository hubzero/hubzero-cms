<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding 'derivatives' field to publication licenses
 *
*/
class Migration20170712200400ComPublications extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__publication_licenses')) {
            if (!$schema->hasColumn('#__publication_licenses', 'derivatives')) {
                $schema->addColumn('#__publication_licenses', 'derivatives')->tinyInteger(2)->notNull()->default(0);

                $this->db->getQuery(true)
                    ->update('#__publication_licenses')
                    ->set(['derivatives' => 1])
                    ->whereIn('name', ['cc', 'standard', 'cc0', 'cc40-by-nc-sa', 'cc40-by-sa'])
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

        if ($schema->tableExists('#__publication_licenses')) {
            if ($schema->hasColumn('#__publication_licenses', 'derivatives')) {
                $schema->dropColumn('#__publication_licenses', 'derivatives');
            }
        }
    }
}
