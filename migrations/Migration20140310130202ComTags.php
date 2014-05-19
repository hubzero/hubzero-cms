<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for merging duplicate tags
 **/
class Migration20140310130202ComTags extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__tags'))
		{
			// We need to clean out duplicates first
			$query = "SELECT *, count(id) as cnt FROM `#__tags` GROUP BY `tag` HAVING cnt > 1;";
			$this->db->setQuery($query);
			if ($results = $this->db->loadObjectList())
			{
				require_once(JPATH_ROOT . DS . 'components' . DS . 'com_tags' . DS . 'models' . DS . 'cloud.php');

				foreach ($results as $result)
				{
					// Get all duplicate tags
					$query = "SELECT * FROM `#__tags` WHERE `tag`=" . $this->db->quote($result->tag) . ";";
					$this->db->setQuery($query);
					if ($tags = $this->db->loadObjectList())
					{
						foreach ($tags as $tag)
						{
							if ($tag->id == $result->id)
							{
								continue;
							}
							$oldtag = new TagsModelTag($tag->id);
							if (!$oldtag->mergeWith($result->id))
							{
								continue;
							}
						}
					}
				}
			}

			if ($this->db->tableHasKey('#__tags', 'idx_tag'))
			{
				$query = "ALTER TABLE `#__tags` DROP INDEX `idx_tag`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			$query = "CREATE UNIQUE INDEX `idx_tag` ON `#__tags` (`tag`);";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__tags'))
		{
			if ($this->db->tableHasKey('#__tags', 'idx_tag'))
			{
				$query = "ALTER TABLE `#__tags` DROP INDEX `idx_tag`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			$query = "ALTER TABLE `#__tags` ADD INDEX `idx_tag` (`tag`);";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}