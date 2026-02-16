<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for making default collections private.
 *
 * The collections component only pulls public collections. This allows
 * default collections to be renamed and publicly displayed, whereas
 * previously they would be filtered out by the component.
  *
**/
class Migration20140630084843ComCollections extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__collections')) {
            $this->db->getQuery(true)
                ->update('#__collections')
                ->set(['access' => 4])
                ->where('is_default', '=', 1)
                ->where('access', '=', 0)
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__collections')) {
            $this->db->getQuery(true)
                ->update('#__collections')
                ->set(['access' => 0])
                ->where('is_default', '=', 1)
                ->where('access', '=', 4)
                ->execute();
        }
    }
}
