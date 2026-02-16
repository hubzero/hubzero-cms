<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding repository_contact field to authors table
**/
class Migration20151106141855ComPublications extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->hasColumn('#__publication_authors', 'repository_contact')) {
            $schema->addColumn('#__publication_authors', 'repository_contact')->tinyInteger(2)->notNull()->default(0);
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->hasColumn('#__publication_authors', 'repository_contact')) {
            $schema->dropColumn('#__publication_authors', 'repository_contact');
        }
    }
}
