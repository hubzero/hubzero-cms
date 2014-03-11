<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for ...
 **/
class Migration20140310130202ComTags extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		if ($db->tableExists('#__tags'))
		{
			// We need to clean out duplicates first
			$query = "SELECT *, count(id) as cnt FROM `#__tags` GROUP BY `tag` HAVING cnt > 1;";
			$db->setQuery($query);
			if ($results = $db->loadObjectList())
			{
				require_once(JPATH_ROOT . DS . 'components' . DS . 'com_tags' . DS . 'models' . DS . 'cloud.php');

				foreach ($results as $result)
				{
					// Get all duplicate tags
					$query = "SELECT * FROM `#__tags` WHERE `tag`=" . $db->quote($result->tag) . ";";
					$db->setQuery($query);
					if ($tags = $db->loadObjectList())
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

			if ($db->tableHasKey('#__tags', 'idx_tag'))
			{
				$query = "ALTER TABLE `#__tags` DROP INDEX `idx_tag`;";
				$db->setQuery($query);
				$db->query();
			}

			$query = "CREATE UNIQUE INDEX `idx_tag` ON `#__tags` (`tag`);";
			$db->setQuery($query);
			$db->query();
		}
	}

	/**
	 * Down
	 **/
	protected static function down($db)
	{
		if ($db->tableExists('#__tags'))
		{
			if ($db->tableHasKey('#__tags', 'idx_tag'))
			{
				$query = "ALTER TABLE `#__tags` DROP INDEX `idx_tag`;";
				$db->setQuery($query);
				$db->query();
			}

			$query = "ALTER TABLE `#__tags` ADD INDEX `idx_tag` (`tag`);";
			$db->setQuery($query);
			$db->query();
		}
	}
}