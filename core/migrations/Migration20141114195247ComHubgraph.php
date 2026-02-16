<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for fixing up hubgraph queue engine and character set
  *
**/
class Migration20141114195247ComHubgraph extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('hg_update_queue')) {
            if (strtolower($schema->getEngine('hg_update_queue')) != 'myisam') {
                $schema->setTableEngine('hg_update_queue', 'MyISAM');
            }

            if (strtolower($schema->getCharacterSet('hg_update_queue')) != 'utf8') {
                $schema->setTableCharset('hg_update_queue', 'utf8');
            }
        }
    }
}
