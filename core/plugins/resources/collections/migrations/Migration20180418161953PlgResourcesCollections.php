<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Resources\Collections\Migrations;

use Components\Resources\Models\Type;
use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Resources - Related plugin
 **/
class Migration20180418161953PlgResourcesCollections extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        if ($this->db->tableExists('#__resource_types')) {
            if (!$this->db->tableHasField('#__resource_types', 'collection')) {
                $query = "ALTER TABLE `#__resource_types` ADD COLUMN `collection` INT(2) NULL DEFAULT 0";
                $this->db->setQuery($query);
                $this->db->query();
            }
        }
        // A hub without a series type has nothing to mark; saving the empty
        // model it gets back would add a resource type with no name
        $type = Type::onebyAlias('series');
        if (!$type->isNew()) {
            $type->set('collection', 1);
            $type->save();
        }
        $this->addPluginEntry('resources', 'collections');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        if ($this->db->tableExists('#__resource_types')) {
            if ($this->db->tableHasField('#__resource_types', 'collection')) {
                $query = "ALTER TABLE `#__resource_types` DROP COLUMN `collection`";
                $this->db->setQuery($query);
                $this->db->query();
            }
        }
        $this->deletePluginEntry('resources', 'collections');
    }
}
