<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding state column to publication ratings table
 *
*/
class Migration20150518190000ComPublications extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__publication_ratings')) {
            if (!$schema->hasColumn('#__publication_ratings', 'state')) {
                $schema->addColumn('#__publication_ratings', 'state')->tinyInteger(2)->notNull()->default('1');
            }
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__publication_ratings')) {
            if ($schema->hasColumn('#__publication_ratings', 'state')) {
                $schema->dropColumn('#__publication_ratings', 'state');
            }
        }
    }
}
