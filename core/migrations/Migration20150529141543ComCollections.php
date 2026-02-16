<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for fixing incorrect access values on colleciton items
**/
class Migration20150529141543ComCollections extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__collections_items')) {
            $this->db->getQuery(true)
                ->update('#__collections_items')
                ->set(['access' => 0])
                ->where('access', '=', 1)
                ->execute();
        }
    }
}
