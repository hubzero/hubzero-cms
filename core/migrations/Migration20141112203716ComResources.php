<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for resource changes needed by com_hubgraph
**/
class Migration20141112203716ComResources extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__resource_assoc') && !$schema->hasColumn('#__resource_assoc', 'id')) {
            $schema->addAutoIncrementPrimaryKey('#__resource_assoc', 'id', true);
        }

        if ($schema->tableExists('#__author_assoc') && !$schema->hasColumn('#__author_assoc', 'id')) {
            $schema->dropPrimaryKey('#__author_assoc');

            if (!$schema->hasKey('#__author_assoc', 'uidx_subtable_subid_authorid')) {
                $schema->addUniqueIndex(
                    '#__author_assoc',
                    'uidx_subtable_subid_authorid',
                    ['subtable', 'subid', 'authorid']
                );
            }

            $schema->addAutoIncrementPrimaryKey('#__author_assoc', 'id', true);
        }
    }
}
