<?php
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
		if ($this->db->tableExists('#__solr_search_searchcomponents') && !$this->db->tableHasField('#__solr_search_searchcomponents', 'custom'))
		{
			$query = "ALTER TABLE `#__solr_search_searchcomponents` ADD COLUMN `custom` VARCHAR(254) NULL;";
			$this->db->setQuery($query);
			$this->db->query();
			$searchComponents = SearchComponent::all()->rows();
			foreach ($searchComponents as $component)
			{
				$title = ucfirst($component->get('name'));
				$component->set('title', $title);
				$component->save();
			}
		}
	}

	public function down()
	{
		if ($this->db->tableExists('#__solr_search_searchcomponents') && $this->db->tableHasField('#__solr_search_searchcomponents', 'custom'))
		{
			$query = "ALTER TABLE `#__solr_search_searchcomponents` DROP COLUMN `custom`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
