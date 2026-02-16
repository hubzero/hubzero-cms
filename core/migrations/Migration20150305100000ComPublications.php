<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding master_doi field to #__publications
  *
**/
class Migration20150305100000ComPublications extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__publications')) {
            if (!$schema->hasColumn('#__publications', 'master_doi')) {
                $schema->addColumn('#__publications', 'master_doi')
                    ->string(255)
                    ->default('')
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

        if ($schema->tableExists('#__publications')) {
            if ($schema->hasColumn('#__publications', 'master_doi')) {
                $schema->dropColumn('#__publications', 'master_doi');
            }
        }
    }
}
