<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for fixing up tags indices
 **/
class Migration20130426070126ComTags extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$query = "";

		if ($this->db->tableHasKey('#__tags_object', 'jos_tags_object_objectid_tbl_idx'))
		{
			$query .= "ALTER TABLE `#__tags_object` DROP KEY `jos_tags_object_objectid_tbl_idx`;\n";
		}
		if ($this->db->tableHasKey('#__tags_object', 'jos_tags_object_label_tagid_idx'))
		{
			$query .= "ALTER TABLE `#__tags_object` DROP KEY `jos_tags_object_label_tagid_idx`;\n";
		}
		if ($this->db->tableHasKey('#__tags_object', 'jos_tags_object_tbl_objectid_label_tagid_idx'))
		{
			$query .= "ALTER TABLE `#__tags_object` DROP KEY `jos_tags_object_tbl_objectid_label_tagid_idx`;\n";
		}
		if ($this->db->tableHasKey('#__tags_object', 'jos_tags_object_tagid_idx'))
		{
			$query .= "ALTER TABLE `#__tags_object` DROP KEY `jos_tags_object_tagid_idx`;\n";
		}
		if (!$this->db->tableHasKey('#__tags_object', 'idx_objectid_tbl'))
		{
			$query .= "ALTER TABLE `#__tags_object` ADD KEY `idx_objectid_tbl` (`objectid`,`tbl`);\n";
		}
		if (!$this->db->tableHasKey('#__tags_object', 'idx_label_tagid'))
		{
			$query .= "ALTER TABLE `#__tags_object` ADD KEY `idx_label_tagid` (`label`, `tagid`);\n";
		}
		if (!$this->db->tableHasKey('#__tags_object', 'idx_tbl_objectid_label_tagid'))
		{
			$query .= "ALTER TABLE `#__tags_object` ADD KEY `idx_tbl_objectid_label_tagid` (`tbl`, `objectid`, `label`, `tagid`);\n";
		}
		if (!$this->db->tableHasKey('#__tags_object', 'idx_tagid'))
		{
			$query .= "ALTER TABLE `#__tags_object` ADD KEY `idx_tagid` (`tagid`);\n";
		}
		if (!$this->db->tableHasKey('#__tags_substitute', 'idx_tag_id'))
		{
			$query .= "ALTER TABLE `#__tags_substitute` ADD KEY `idx_tag_id` (`tag_id`);\n";
		}
		if ($this->db->tableHasKey('#__tags', 'jos_tags_raw_tag_alias_description_ftidx'))
		{
			$query .= "ALTER TABLE `#__tags` DROP KEY `jos_tags_raw_tag_alias_description_ftidx`;\n";
		}
		if ($this->db->tableHasKey('#__tags', 'jos_tags_raw_tag_description_ftidx'))
		{
			$query .= "ALTER TABLE `#__tags` DROP KEY `jos_tags_raw_tag_description_ftidx`;\n";
		}
		if (!$this->db->tableHasKey('#__tags', 'ftidx_raw_tag_description'))
		{
			$query .= "ALTER TABLE `#__tags` ADD FULLTEXT ftidx_raw_tag_description (`raw_tag`,`description`);\n";
		}
		if ($this->db->tableHasKey('#__tags', 'description'))
		{
			$query .= "ALTER TABLE `#__tags` DROP KEY `description`;\n";
		}
		if (!$this->db->tableHasKey('#__tags', 'ftidx_description'))
		{
			$query .= "ALTER TABLE `#__tags` ADD FULLTEXT ftidx_description (`description`);\n";
		}

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}