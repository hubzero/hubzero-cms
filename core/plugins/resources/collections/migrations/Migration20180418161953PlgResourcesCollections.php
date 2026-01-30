<?php

// phpcs:disable PSR1.Files.SideEffects.FoundWithSymbols

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Resources\Collections\Migrations;

require_once Component::path('com_resources') . '/models/type.php';
use Components\Resources\Models\Type;
use Hubzero\Content\Migration\Base;
/**
 * Migration script for adding Resources - Related plugin
 **/
class Migration20180418161953PlgResourcesCollections extends Base
{
    /**
     * Up
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
        $type = Type::onebyAlias('series');
        $type->set('collection', 1);
        $type->save();
        $this->addPluginEntry('resources', 'collections');
    }

    /**
     * Down
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
