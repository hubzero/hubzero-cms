<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding master_doi field to #__resources
  *
**/
class Migration20150312120000ComResources extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__resources')) {
            if (!$schema->hasColumn('#__resources', 'master_doi')) {
                $schema->addColumn('#__resources', 'master_doi')->string(100)->default('')->execute();
            }
        }
    }
}
