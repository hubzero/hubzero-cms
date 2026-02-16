<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for creating publication collaborator table
  *
**/
class Migration20240613212454ComPublications extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__publication_collaborators')) {
            $schema->createTable('#__publication_collaborators')
                ->id()
                ->string('name', 100)->nullable()
                ->string('orcid', 100)->nullable()
                ->string('access_token', 100)->nullable()
                ->datetime('acquisition_date')->nullable()
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__publication_collaborators')) {
            $schema->dropTable('#__publication_collaborators');
        }
    }
}
