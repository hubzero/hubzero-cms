<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Migrations;

use Hubzero\Content\Migration\Base;
use Components\Search\Models\Solr\SearchComponent;

/**
 * Migration script for adding title column to SearchComponents
 **/
class Migration20180806093200ComSearch extends Base
{
    /**
     * Up
     *
     * @return  void
     */
    public function up()
    {
        $tableName = '#__solr_search_searchcomponents';
        if ($this->db->tableExists($tableName) && !$this->db->tableHasField($tableName, 'title')) {
            $query = "ALTER TABLE `$tableName` ADD COLUMN `title` VARCHAR(45) NULL;";
            $this->db->setQuery($query);
            $this->db->query();
            $searchComponents = SearchComponent::all()->rows();
            foreach ($searchComponents as $component) {
                $title = ucfirst($component->get('name'));
                $component->set('title', $title);
                $component->save();
            }
        }
    }

    /**
     * Down
     *
     * @return  void
     */
    public function down()
    {
        $tableName = '#__solr_search_searchcomponents';
        if ($this->db->tableExists($tableName) && $this->db->tableHasField($tableName, 'title')) {
            $query = "ALTER TABLE `$tableName` DROP COLUMN `title`;";
            $this->db->setQuery($query);
            $this->db->query();
        }
    }
}
