<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

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
				require_once(PATH_CORE . DS . 'components' . DS . 'com_tags' . DS . 'models' . DS . 'cloud.php');

				$cls = '\\Components\\Tags\\Models\\Tag';
				// [!] - Backwards compatibility
				if (class_exists('TagsModelTag'))
				{
					$cls = 'TagsModelTag';
				}

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

							$oldtag = new $cls($tag->id);
							if ($oldtag instanceof \Hubzero\Database\Relational)
							{
								$oldtag = \Components\Tags\Models\Tag::oneOrNew($tag->id);
							}
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