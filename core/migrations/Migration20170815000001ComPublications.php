<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding 'featured' column to publications table
 *
*/
class Migration20170815000001ComPublications extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->hasColumn('#__publications', 'featured')) {
            $schema->addColumn('#__publications', 'featured')->tinyInteger(1)->notNull()->default(0);
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->hasColumn('#__publications', 'featured')) {
            $schema->dropColumn('#__publications', 'featured');
        }
    }
}
