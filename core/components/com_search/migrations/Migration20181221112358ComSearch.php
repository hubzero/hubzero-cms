<?php

// phpcs:disable PSR1.Files.SideEffects, PSR1.Classes.ClassDeclaration.MissingNamespace

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;
use Components\Search\Models\Solr\SearchComponent;

require_once Component::path('com_search') . '/models/solr/searchcomponent.php';

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding custom column to SearchComponents
 **/
class Migration20181221112358ComSearch extends Base
{
    public function up()
    {
        $tableName = '#__solr_search_searchcomponents';
        if ($this->db->tableExists($tableName) && !$this->db->tableHasField($tableName, 'custom')) {
            $query = "ALTER TABLE `$tableName` ADD COLUMN `custom` VARCHAR(254) NULL;";
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

    public function down()
    {
        $tableName = '#__solr_search_searchcomponents';
        if ($this->db->tableExists($tableName) && $this->db->tableHasField($tableName, 'custom')) {
            $query = "ALTER TABLE `$tableName` DROP COLUMN `custom`;";
            $this->db->setQuery($query);
            $this->db->query();
        }
    }
}
