<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for making sure collection created_by is filled in
 *
*/
class Migration20140829142600ComCollections extends Base
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
                ->setColumn('created_by', 'object_id')
                ->where('object_type', '=', 'member')
                ->where('created_by', '=', 0)
                ->execute();

            $this->db->getQuery(true)
                ->update('#__collections_posts AS p')
                ->leftJoin('#__collections AS c', 'p.collection_id', 'c.id')
                ->setColumn('p.created_by', 'c.created_by')
                ->where('p.created_by', '=', 0)
                ->where('c.is_default', '=', 1)
                ->execute();
        }
    }
}
