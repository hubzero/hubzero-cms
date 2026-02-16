<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding normalized resource aliases where missing
 *
*/
class Migration20170322093811ComResources extends Base
{
    public function normalize($txt)
    {
        return preg_replace("/[^a-zA-Z0-9\-_]/", '', strtolower($txt));
    }

    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__resource_types')) {
            $results = $this->db->getQuery(true)
                ->select(['id', 'type'])
                ->from('#__resource_types')
                ->where('alias', '=', '')
                ->orWhereNull('alias')
                ->loadObjectList();

            foreach ($results as $result) {
                $alias = $this->normalize($result->type);
                $this->db->getQuery(true)
                    ->update('#__resource_types')
                    ->set(['alias' => $alias])
                    ->where('id', '=', (int)$result->id)
                    ->execute();
            }
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        /* Can't undo this without recording which entries changed. */
    }
}
